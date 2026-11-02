<?php
namespace Forms;
class Login extends \Forms\Form {

	//КОНСТАНТА FIELDS — описывает поля формы входа

	protected const FIELDS = ['name' => ['type' => 'string'],
					'password' => ['type' => 'string']
				];
	/*
	МЕТОД verify_user — проверяет учётные данные пользователя
	 * 
	 * array &$data — ссылка на массив с данными из формы
	 *   Вход: $data = ['name' => 'admin', 'password' => '12345']
	 *   Выход (если ошибка): добавляется $data['__errors'] с описанием ошибок

	 *   Вызывается в контроллере Login:
	 *   $user_id = \Forms\Login::verify_user($login_form);
	*/
	static function verify_user(&$data) {

		// Пустой массив для сбора ошибок
		// Если после проверок он останется пустым — ошибок нет
		$errors =[];

		// СОЗДАЁМ ОБЪЕКТ МОДЕЛИ User для работы с таблицей users
		$users = new \Models\User();

		/*
			 ИЩЕМ ПОЛЬЗОВАТЕЛЯ ПО ИМЕНИ
		    get($value, $key_field, $fields) — метод модели Model
		    $value = $data['name'] — имя, которое ввёл пользователь
		    $key_field = 'name' — ищем по полю 'name' в БД
	            $fields = 'id, password, active' — какие поля нам нужны
		 Результат: либо массив с данными пользователя, либо FALSE (если не найден)

		*/

		$user = $users->get($data['name'], 'name', 'id, password, active');

		if(!$user)  //если пользователь не найден
			$errors['name'] = 'Неверное имя пользователя';

		else{   //если пользователь найден

			if(!$user['active']) //проверяем если пользователь не активен

				$errors['name']='Этот пользователь неактивен';

			else{ //если активен проверяем пароль

					/*
						password_verify() — встроенная функция PHP
						  Берёт введённый пароль (в чистом виде) и хеш из БД
						  Возвращает TRUE, если пароль подходит
					*/

				if(!password_verify($data['password'], $user['password']))

					$errors['password'] = 'Неверный пароль';

				else //если все верно - возвращаем айди пользователя

					return $user['id'];
			}

		}

		//ЕСЛИ МЫ ЗДЕСЬ — БЫЛА ОШИБКА  
		// В массив $data (который передан по ссылке) добавляем ключ '__errors'

		$data['__errors'] = $errors;

		// Возвращаем FALSE — сигнал, что аутентификация не удалась
		return FALSE;

	}
}