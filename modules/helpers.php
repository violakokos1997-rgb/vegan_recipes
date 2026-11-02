<?php
namespace Helpers{
	function render(string $template, array $context){
		global $base_path;
		extract($context);
		require $base_path . '/modules/templates/' . $template . '.php';
	}
	function get_fragment_path(string $fragment) : string{
		global $base_path;
		return $base_path . '/modules/templates/' . $fragment . '.inc.php';
	}
	
	function connect_to_db(){
		$conn_str = 'mysql:host=' . \Settings\DB_HOST . ';dbname=' . \Settings\DB_NAME . ';charset=utf8';
		return new \PDO($conn_str, \Settings\DB_USERNAME, \Settings\DB_PASSWORD);
		
	}
	function get_GET_params(array $existing_param_names, array $new_params = []):string{
		$a = [];
		foreach($existing_param_names as $name)
			if(!empty($_GET[$name]))
				$a[] = $name . '=' . urlencode($_GET[$name]);
		foreach ($new_params as $name=>$value)
			$a[]= $name . '=' . urlencode($value);
		$s = implode('&', $a);
		if($s)
			$s = '?' . $s;
		return $s;
	}
	function get_formatted_timestamp(string $timestamp) :string{

		return strftime('%d.%m.%Y %H:%M:%S', strtotime($timestamp));
	}
	
	/*Редирект заменяет последний POST-запрос на GET, 
	поэтому при обновлении страницы браузер не пытается повторно отправить форму*/

	function redirect(string $url, int $status = 302){
		header('Location: ' . $url, TRUE, $status);
	}

	//Выводит сообщение об ошибке для конкретного поля
	function show_errors(string $fld_name, array $form_data){
		if(isset($form_data['__errors'][$fld_name]))

			// Значение ошибки = $form_data['__errors'][$fld_name] 

			echo '<div class="error">' . $form_data['__errors'][$fld_name] . '</div>';
	}

	//функция генерирует и возвращает имя под которым будет сохранен отправленный посетителем файл

	function get_filename(array $file): string{
		global $base_path;
		$image_file_path = $base_path . \Settings\IMAGE_FILE_PATH; //путь к папке файла от корня

		//берем имя файла от пользователя и достаем его расширение
		$ext = pathinfo($file['name'], PATHINFO_EXTENSION); 

		//достаем имя без расширения
		$name = pathinfo($file['name'], PATHINFO_FILENAME); 

		/*очищаем от ненужных символов : 
			\p{L}-любая буква(и кирилица и латиница)
			\p{N}-любая цифра
			_ нижнее подчеркивание
			- дефис
			'/[^\p{L}\p{N}_-]/u' — ищем любой символ, КРОМЕ букв, цифр, дефиса, подчёркивания
				'' — заменяем на пустоту (удаляем)
		*/

		 $name = preg_replace('/[^\p{L}\p{N}_-]/u', '', $name); 
		 //переводим в нижний регистр
		 $name = strtolower($name);

		 //проверяем уникальность
   		 $postfix = '';
  		 $number = 0;
  		 while (file_exists($image_file_path . $name . $postfix . '.' . $ext)) {
        		$postfix = '_' . ++$number;
  		  }
    
   		 return $name . $postfix . '.' . $ext;

	}

	//сохранение файла 
	function save_file(array $file): string{

		global $base_path;
		$image_file_path = $base_path . \Settings\IMAGE_FILE_PATH;
		$filename = get_filename($file);

		/* 	move_uploaded_file — перемещает временный файл в папку постоянного хранения
			$file['tmp_name'] - временное имя файла на сервере
			$image_file_path - путь к папке изображений
			$filename - уникальное имя файла (из функции выше)    */

		move_uploaded_file($file['tmp_name'], $image_file_path . $filename);

		//возвращает имя файла
		return $filename;
	}
	
	//удаление файла
	function delete_file(string $filename) {
		global $base_path;

		//полный путь файла
		$file_path = $base_path . \Settings\IMAGE_FILE_PATH . $filename;

		//проверка, существует ли файл по данному адресу
		if(file_exists($file_path))

			// удаляет файл с диска
			unlink($file_path);
	}

	function generate_slug(string $title): string {


    		// 1. Переводим в нижний регистр
    		$slug = mb_strtolower($title, 'UTF-8');
    
   		 // 2. Заменяем пробелы и спецсимволы 
   		 $slug = preg_replace('/[^\p{L}\p{N}]+/u', '_', $slug);
   		
    		// 3. Удаляем дефисы в начале и конце
   		 $slug = trim($slug, '-');
    
   		 return $slug;
	}

	/* Что приходит  в функцию: $template = "Привет, %user%! Твой комментарий: %text%";
				$values = [ 'user' => 'Анна', 'text' => 'Вкусно!'];
		$key = 'user', $value = 'Анна' , $literals[] = '/%user%/iu', 	$vals[] = 'Анна'
		preg_replace() берёт шаблон и заменяет каждый образец из $literals на соответствующее значение из $vals
			
	*/

	function render_text(string $template, array $values) : string {
		$literals=[];
		$vals=[];
		foreach($values as $key=>$value){
			$literals[]='/%' . $key . '%/iu';
			$vals[]=$value;
		}
		return preg_replace($literals, $vals, $template);

	}

	function send_mail(string $to, string $subject, string $body, array $values){


		global $base_path;

		//Подключаем файл с классом для отправки писем через SMTP
		require_once $base_path . 'modules/SendMailSmtpClass.php';

		//(Если письма отправлять нужно)
		if(\Settings\MAIL_SEND){

			//Создание объекта для отправки писем
			//Этот объект умеет подключаться к почтовому серверу Google и отправлять письма

			$letter = new \SendMailSmtpClass(\Settings\SMTP_LOGIN, 
							\Settings\SMTP_PASSWORD, 
							\Settings\SMTP_HOST, \Settings\SMTP_PORT);

			//Формируется путь к файлу, с сообщением(тема письма)
			$s_path = $base_path . 'modules/templates/' . $subject . '.txt';

			//file_get_contents() читает содержимое файла
			//render_text() заменяет метки %...% на реальные значения из $values

			$s = render_text(file_get_contents($s_path), $values);

			//Тело письма
			$b_path = $base_path . 'modules/templates/' . $body . '.txt';

			
			$b = render_text(file_get_contents($b_path), $values);


			/*
				Параметр       Что означает	Пример
				$to	      Кому отправляем	anna@gmail.com
				$s	        Тема письма     "Новый комментарий к рецепту Веганский борщ"
				$b	        Тело письма	Текст с комментарием
			\Settings\MAIL_FROM	От чьего имени	 site@vegan.ru

			*/
		

			$result = $letter->send($to, $s, $b, \Settings\MAIL_FROM);
		



		}
         }	

	function generate_token(): string {
		
		if(session_status()!= PHP_SESSION_ACTIVE)
			session_start();

		//Превращает 32 рандомно сгенерированных байта в строку из 64 символов
		$token = bin2hex(random_bytes(32));

		$_SESSION[$token] = 'anti_csrf';
		return $token;

	}

	function check_token (array $form_data) {

		if (empty($form_data['__token']))
			throw new \Page403Exception();
		$token = $form_data['__token'];

		if(empty($_SESSION[$token]))
			throw new \Page403Exception();

		$val = $_SESSION[$token];
		unset ($_SESSION[$token]);

		if($val != 'anti_csrf')
			throw new \Page403Exception();
	}


}