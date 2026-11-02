<?php
namespace Models;
require_once $base_path . 'modules/helpers.php';

class Model implements \Iterator{
	protected const TABLE_NAME='';
	protected const DEFAULT_ORDER='';
	protected const RELATIONS=[];

	static private $connection = NULL;
	static private $connection_count=0;

	function __construct(){
		if(!self::$connection)
			self::$connection = \Helpers\connect_to_db();
		self::$connection_count++;
	}
	function __destruct(){
		self::$connection_count--;
		if(self::$connection_count == 0)
			self::$connection = NULL;
	}

	// $query — это объект PDOStatement (результат подготовленного запроса)
	// NULL означает, что запроса пока нет

	private $query = NULL;
	
	// Выполняет SQL-запрос с защитой от SQL-инъекци
	function run($sql, $params=NULL){
		
		// Если был предыдущий запрос, освобождаем ресурсы 

		if($this->query)
			$this->query->closeCursor();

		// 1. Подготавливаем запрос (отделяем структуру SQL от данных)
		$this->query = self::$connection -> prepare($sql);
		
		// 2. Если есть параметры для подстановки
		if($params){
			foreach($params as $key=>$value)
			{
				$k = (is_integer($key)) ? $key+1 : $key;   /* Определяем тип плейсхолдера:
           									  - если ключ целое число (0,1,2...) — это позиционный плейсхолдер 
            									  - номер плейсхолдера = ключ + 1 (потому что в SQL нумерация с 1)
          									   - если ключ строка — это именованный плейсхолдер :name
									*/
				// Определяем тип данных для безопасной привязки
				switch (gettype($value))
				{
					case 'integer':
						$t = \PDO::PARAM_INT;
						break;
					case 'boolean':
						$t = \PDO::PARAM_BOOL;
						break;
					case 'NULL':
						$t = \PDO::PARAM_NULL;
						break;
					default:
						$t = \PDO::PARAM_STR;
				}
				// Привязываем значение к плейсхолдеру
				$this->query->bindValue($k, $value, $t);
			}
		}
		
		 // 3. Выполняем запрос
		$this->query->execute();

	}

	function select($fields = '*',
			 $links=NULL, 
			$where='',
			 $params = NULL,
			 $order='', 
			$offset=NULL,
			 $limit=NULL,
			 $group='', 
			$having='')
	{
		$s = 'SELECT ' . $fields . ' FROM ' . static::TABLE_NAME;
		if($links)
			foreach($links as $ext_table){
				$rel = static::RELATIONS[$ext_table];
				$s .= ' ' . ((key_exists('type', $rel)) ? $rel['type'] : 'INNER') . ' JOIN ' . $ext_table . ' ON ' . static::TABLE_NAME . '.' . $rel['external'] . '=' . $ext_table . '.' . $rel['primary']; 
			}
		if($where)
			$s .= ' WHERE ' . $where;
		if($group){
			$s .= ' GROUP BY ' . $group;
			if($having)
				$s .= ' HAVING ' . $having;
		}
		if($order)
			$s .= ' ORDER BY ' . $order;
		else
			$s .= ' ORDER BY ' . static::DEFAULT_ORDER;
		if($limit && $offset !== NULL)
			$s .= ' LIMIT ' . $offset . ', ' . $limit;
		$s .= ';';

		$this->run($s, $params);
	}

	private $record = FALSE;
	function current(){
		return $this->record;
	}
	function key(){
		return 0;
	}
	function next(){
		$this->record = $this->query->fetch(\PDO::FETCH_ASSOC);
	}
	function rewind(){
		$this->record = $this->query->fetch(\PDO::FETCH_ASSOC);
	}
	function valid(){
		return $this->record !== FALSE;
	}

	function get_record($fields = '*', $links=NULL, $where='', $params=NULL)
	{
		$this->record=FALSE;
		$this->select($fields, $links, $where, $params);
		return $this->query->fetch(\PDO::FETCH_ASSOC);
	}
	function get($value, $key_field='id', $fields='*', $links=NULL){
		return $this->get_record($fields, $links, $key_field . ' =?', [$value]);
	}
	function get_or_404($value, $key_field='id', $fields='*', $links=NULL){
		$rec=$this->get($value, $key_field, $fields, $links);
		if($rec)
			return $rec;
		else
			throw new \Page404Exception();
	}
	
	/*
	     Метод  before_insert(&$fields) принимает массив со значениями полей.
             Нужен для того, чтобы перед добавлением записей выполнить какие-либо доп. действия.
	*/
	protected function before_insert(&$fields){}

	// Формируем SQL-запрос вида: INSERT INTO название_таблицы (поле1, поле2) VALUES (:поле1, :поле2)
	/*
		 Плейсхолдеры (:поле1, :поле2) нужны для безопасности:
			 - защита от SQL-инъекций (значения не вшиваются в запрос напрямую)
			 - автоматическое экранирование спецсимволов
 			 - значения подставляются отдельно через run()
 	*/
	function insert($fields){
		static::before_insert($fields);
		$s = 'INSERT INTO ' . static::TABLE_NAME;
		$s2 = $s1 = '';
		foreach($fields as $n=>$v) {
			if($s1){
				$s1 .= ', ';
				$s2 .= ', ';
			}
			$s1 .= $n;
			$s2 .= ':' . $n;
		}
		$s .= ' (' . $s1 . ') VALUES (' . $s2 . ');';
		$this->run($s, $fields);
		$id = self::$connection -> lastInsertId(); //для возвращения номера добавленной записи 
		return $id;

	}

	/*
	      Метод before_update вызывается перед обновлением записи.
	      Позволяет модифицировать данные до сохранения (например, обновить дату).
			Параметры:
 				 - $fields: массив с новыми значениями полей (например, ['name' => 'Новое имя'])
				 - $value: значение, по которому ищем запись (например, 5)
 				 - $key_field: имя поля для поиска (по умолчанию 'id')
	 */
	protected function before_update(&$fields, $value, $key_field='id'){}

	/*
	Формирует запрос вида: UPDATE имя_таблицы SET поле1 = :поле1, поле2 = :поле2 WHERE id = :__key
 	 Плейсхолдер :__key специальный, чтобы не конфликтовать с реальными полями
 	*/
	function update($fields, $value, $key_field='id'){
		static::before_update($fields, $value, $key_field);
		$s= 'UPDATE ' . static::TABLE_NAME . ' SET ';
		$s1='';
		foreach($fields as $n=>$v){
			if($s1)
				$s1 .= ', ';
			$s1 .= $n . ' = :' .$n;
		}
		$s .= $s1 . ' WHERE ' . $key_field . ' = :__key;';
		$fields['__key'] = $value;  // добавляем значение для плейсхолдера :__key
		$this->run($s, $fields);
	}

	protected function before_delete($value, $key_field = 'id'){}
	
	/*
	   Формирует запрос вида: DELETE FROM таблица WHERE id = ?
 	   Использует позиционный плейсхолдер ? (не :name), потому что параметр всего один
 	*/
	function delete($value, $key_field='id'){
		static::before_delete($value, $key_field);
		$s = 'DELETE FROM ' . static::TABLE_NAME;
		$s .= ' WHERE ' . $key_field . ' = ?;';
		$this->run($s, [$value]);
	}

}