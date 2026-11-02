<?php
namespace Controllers;

// КЛАСС LOGIN — управляет входом и выходом пользователе
// Автоматически запускается сессия через конструктор родителя
/* Два основных действия:
    1. Login() — показать форму входа и обработать отправку формы
    2. logout() — показать форму выхода и завершить сессию
*/

class Login extends BaseController {


/*
 МЕТОД LOGIN — страница входа, вызывается из router.php
	 * 
	 * Обрабатывает два случая:
	 *   1. GET-запрос — просто показать пустую форму входа
	 *   2. POST-запрос — обработать отправленную форму, проверить данные,
	 *      и если всё правильно — войти в систему
	 * Роутер вызывает: 
	 *   $ctr = new \Controllers\Login();
	 *   $ctr->login();  ← этот метод
*/
	function Login() {

		if($_SERVER['REQUEST_METHOD'] == 'POST') {

			//ПОЛУЧАЕМ И ОЧИСТИЩАЕМ ДАННЫЕ ИЗ ФОРМЫ
			/*  get_normalized_data() делает:
			    - Удаляет HTML-теги (защита от XSS-атак)
			    - Проверяет, заполнены ли обязательные поля
			    - Приводит данные к нужным типам
			*/
			$login_form = \Forms\Login::get_normalized_data($_POST);

			//Если ошибок нет 
			if(!isset($login_form['__errors'])) {

				// get_prepared_data() преобразует данные в формат для БД
				// В данном случае для полей 'name' и 'password' — просто копирует

				$login_form = \Forms\Login::get_prepared_data($login_form);


				/* Проверяем имя и пароль
				   Возвращает:
				   - ID пользователя (число) — если всё правильно
				   - FALSE — если ошибка (имя не найдено, пароль неверен, неактиве
				*/

				$user_id = \Forms\Login::verify_user($login_form);
				if($user_id){
					session_start();
					$_SESSION['current_user'] = $user_id;
					\Helpers\redirect('/users/' . $login_form['name']);
				}
			}
		}
		//ПОЛЬЗОВАТЕЛЬ ПРОСТО ОТКРЫЛ СТРАНИЦУ (GET)
		// Получаем пустую форму с начальными значениями
		// Поля 'name' и 'password' будут пустыми строками
		 else
			$login_form = \Forms\Login::get_initial_data();

		// 'form' — массив с данными формы (и возможными ошибками)
		$ctx=['form'=> $login_form, 'site_title'=>'Вход'];
		$this->render('login', $ctx);

	}

	function logout(){
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			unset($_SESSION['current_user']);
			session_destroy();
			\Helpers\redirect('/');
		}else{
			$ctx=['site_title'=>'Выход'];
			$this->render('logout', $ctx);
		}
	}



/*Этот метод вызывается, когда пользователь открывает страницу /register или отправляет форму регистрации*/

	function register() {


             if ($_SERVER['REQUEST_METHOD'] == 'POST') {


		//Функция, которая проверяет, что форма отправлена с нашего сайта

		   \Helpers\check_token($_POST); 

		//Очищаем данные от HTML-тегов, проверяем обязательные поля

           	  $reg_form = \Forms\Register::get_normalized_data($_POST);

            	  if (!isset($reg_form['__errors'])) { //если ошибок нет


			//get_prepared_data - Преобразует данные в формат для БД (например, хеширует пароль, преобразует временную отметку)
			//reg_form - Массив с данными, готовыми для вставки в БД

               		 $reg_form = \Forms\Register::get_prepared_data($reg_form);
                	
			//Создаём объект для работы с таблицей users в БД
               		 $users = new \Models\User();

			//Вставляем данные в таблицу users
			//Пользователь сохраняется, но активным ещё не становится (active = 0). Он не может войти!
               		 $users->insert($reg_form);


			// Формирование письма для активации(с сылкой для активации)
               		 $values = ['title'=> \Settings\SITE_NAME,
			 		'name'=> $reg_form['name'],
			 		'url'=>'http://' . $_SERVER['SERVER_NAME'] . 
					'/users/' . $reg_form['name'] . '/account/activation/' . hash_hmac('ripemd256', $reg_form['name'], \Settings\SECRET_KEY) . '/'];

			//Отправка письма
			//Имя файла с темой письма (activation_subject.txt)
			//Имя файла с телом письма (activation_body.txt)
			//values - Данные для подстановки вместо %title%, %name%, %url%

			\Helpers\send_mail($reg_form['email'], 'activation_subject', 'activation_body', $values);
			
			//Редирект на страницу "Регистрация завершена"
			\Helpers\redirect('/register/complete/');
          	  }

		//Если были ошибки — обновляем CSRF-токен

	   	 $reg_form['__token'] = \Helpers\generate_token();

        }

	//БЛОК GET (пользователь просто открыл страницу регистрации)

	 else{

            $reg_form = \Forms\Register::get_initial_data(); //Возвращает массив с пустыми полями (name = '', email = '', ...)
	    $reg_form['__token'] = \Helpers\generate_token(); //Создаёт новый CSRF-токен для формы
	}

	//Контекст — данные, которые будут переданы в шаблон для отображения
        $ctx = ['form' => $reg_form, 'site_title' => 'Регистрация'];

	//Подключает шаблон register.php и передаёт туда переменные
        $this->render('register', $ctx);

	/*
		В шаблоне register.php появляются переменные:

		$form['name'] — имя пользователя

		$form['email'] — email

		$form['__token'] — CSRF-токен (вставляется в скрытое поле)

		$form['__errors'] — ошибки (если есть)
        */

    }


	function register_complete(){
	
		$ctx=['site_title'=> 'Регистрация завершена'];
		$this->render('register_complete', $ctx);

	}

}