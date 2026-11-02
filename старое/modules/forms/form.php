<?php
namespace Forms;
class Form {

	// Хранит описание полей формы (тип type, обязательность optional, значения по умолчанию initial, nosave)
	protected const FIELDS = [];

	/* $fld_params Параметры поля из константы FIELDS (тип, значение по умолчанию и т.д.)
 	 $initial Данные из БД */

	private static function get_initial_value($fld_name, $fld_params, $initial = []){
		//Если есть данные из БД
		if(isset($initial[$fld_name]))
			$val = $initial[$fld_name];

		//Если данных из БД нет, то берем значение по умолчанию из $fld_params

		else if (isset($fld_params['initial']))
			$val = $fld_params['initial'];
		//Если ничего нет - оставляем пустую строку
		else
			$val = '';

		//Преобразование числа в строковый формат "дата время"

		if($fld_params['type'] == 'timestamp') {
			if(gettype($val) =='integer')
				$val = strftime('%Y-%m-%d %H:%M:%S', $val);
			$val = explode(' ', $val);
		}
		return $val;
	}




	protected static function after_initialize_data(&$data){}
	
	//Возвращает массив с начальными значениями полей из БД
	
	public static function get_initial_data($initial =[]){
		$data = [];

		// Перебираем все поля, описанные в константе FIELDS, для каждой таблицы свои поля
		foreach (static::FIELDS as $fld_name => $fld_params)

			//Подставляем начальные значения для каждого поля таблицы
			$data[$fld_name] = self::get_initial_value($fld_name, $fld_params, $initial);

		// Позволяет изменить $data в классе-наследнике перед возвратом результата
		static::after_initialize_data($data);

		return $data;
	}





	protected static function after_normalize_data(&$data, &$errors){}

	/*Данный метод проверяет и очищает данные которые пришли из формы
		Возвращает массив с чистыми данными, если есть ошибка добавляет в массив ключ __errors и значение ошибки
	*/
	public static function get_normalized_data($form_data){

		$data=[];
		$errors=[];

		//Перебираем все поля, которые описаны в форме

		foreach(static::FIELDS as $fld_name=>$fld_params){

			//Определяем тип поля (если не указан — 'string')

			$fld_type=(isset($fld_params['type'])) ? $fld_params['type'] : 'string';

			if($fld_type =='boolean')
				$data[$fld_name] = !empty($form_data[$fld_name]);

			//Если тип не boolean
			else{
				//Если поле пустое, то 
				if(empty($form_data[$fld_name])){

					//Подставляются значения по умолчанию, определенные в \Forms\Имя_таблицы в Fields

					$data[$fld_name]=self::get_initial_value($fld_name, $fld_params);
					
					//Если нет параметра optional(необязательность заполнения)
					if(!isset($fld_params['optional']))
						//Выводим ошибку
						$errors[$fld_name] = 'Это поле обязательно к заполнению'; 
				}
				//Если поле не пустое, проверяем его тип и нормализуем данные (например '123 abs' превратится в '123')
				else{
					$fld_value=$form_data[$fld_name];
					switch($fld_type){
						//оставляет только цифры
						case 'integer':
							$v=filter_var($fld_value, FILTER_SANITIZE_NUMBER_INT);
							if($v)
								$data[$fld_name]=$v;
							else
								$errors[$fld_name]='Введите целое число';
							break;
						
						//оставляет цифры и точку
						case 'float':
							$v=filter_var($fld_value, FILTER_SANITIZE_NUMBER_FLOAT,['flags'=>FILTER_FLAG_ALLOW_FRACTION]);
							if($v)
								$data[$fld_name]=$v;
							else
								$errors[$fld_name]='Введите вещественное число';
							break;

						//проверяет корректность даты
						case 'timestamp':
							$v =strtotime(implode(' ', $fld_value));//если функция вернула число, то дата корректна 
							if($v)
								$data[$fld_name]=$fld_value; //заносим данные в значение поля
							else
								$errors[$fld_name] ='Выберите дату и время';
							break;

						//оставляет допустимые символы для email
						case 'email':
							$v=filter_var($fld_value, FILTER_SANITIZE_EMAIL);
							if($v)
								$data[$fld_name]=$v;
							else
								$errors[$fld_name]='Введите адрес электронной почты';
							break;

						/*Если тип string или другой - удаляем html-теги
							Удаление HTML-тегов — защита от XSS-атак
							XSS (Cross-Site Scripting) — когда злоумышленник вставляет в форму вредоносный код на JavaScript
							Без очистки: злоумышленник может украсть куки, перенаправить на вредоносный сайт, подменить содержимое страницы.
							С очисткой: вредоносный код превращается в безобидный текст.
						*/
						default:
							$data[$fld_name] =filter_var($fld_value, FILTER_SANITIZE_STRING);
						}
					}
				}
			}
			static::after_normalize_data($data, $errors); //можно переопределить в наследнике, чтобы добавить свои проверки
			
			//Если есть ошибки, добавляем данные в массив под ключем __errors
			if($errors)
				$data['__errors']=$errors;
			
			//Возвращаем массив с нормализованными данными 
			return $data;
	}





	protected static function after_prepare_data(&$data, &$norm_data){}

	/*Этот метод готовит данные для сохранения формы
		-Берёт нормализованные данные ($norm_data = значения которые ввел пользователь) из get_normalized_data
		-Преобразует их в формат, понятный для БД
		-Возвращает массив $data для insert() или update()
	*/

	public static function get_prepared_data($norm_data){
		$data =[];
		
		//$fld_params — настройки поля (неизменные  - type, optional, nosave)
		foreach(static::FIELDS as $fld_name => $fld_params){

			if(!isset($fld_params['nosave']) && isset($norm_data[$fld_name])){

				//Значение которое ввел пользователь
				$val=$norm_data[$fld_name]; //Например $norm_data['user'] = admin

				if($fld_params['type'] =='timestamp')
					$data[$fld_name] = implode(' ', $val); 
				else
					$data[$fld_name] =$val;
				}
			}
		static::after_prepare_data($data, $norm_data);
		return $data;
	}
		
}