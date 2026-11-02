<?php
namespace Controllers;
class Account extends BaseController {
    private function check_user(string $username) {
        $users = new \Models\User();
        $user = $users->get_or_404($username, 'name', 'id');
        if (!($this->current_user &&
            ($this->current_user['id'] == $user['id'] ||
            $this->current_user['admin'])))
            throw new \Page403Exception();
    }

    function delete(string $username) {
        $this->check_user($username);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		\Helpers\check_token($_POST);

            $users = new \Models\User();
            $users->delete($username, 'name');
            unset ($_SESSION['current_user']);
            session_destroy();
            \Helpers\redirect('/');
        } else {
            $ctx = ['site_title' => 'Удаление пользователя' , '__token'=> \Helpers\generate_token()];
            $this->render('user_delete', $ctx);
        }
    }

	function edit(string $username) {
       		$this->check_user($username);
        	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			\Helpers\check_token($_POST);

            		$user_form =\Forms\User::get_normalized_data($_POST);

           		if (!isset($user_form['__errors'])) {
               			 $user_form = \Forms\User::get_prepared_data($user_form);
              			 $users = new \Models\User();
               			 $users->update($user_form, $username, 'name');
                		 \Helpers\redirect('/users/' . $username);
           			 }

			$user_form['__token'] = \Helpers\generate_token();

       		} else {
          		$users = new \Models\User();
            		$user = $users->get_or_404($username, 'name');
           		$user_form = \Forms\User::get_initial_data($user);
			$user_form['__token'] = \Helpers\generate_token();
       			 }
			
        	$ctx = ['form' => $user_form,
           		 	'site_title' => 'Правка пользователя'];
       		$this->render('user_edit', $ctx);
   	 }

	function edit_password(string $username) {
		$this->check_user($username);
       		 if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			\Helpers\check_token($_POST);

           		 $password_form = \Forms\Password::get_normalized_data($_POST);
           		 if (!isset($password_form['__errors'])) {
               		 $password_form = \Forms\Password::get_prepared_data($password_form);
               		 $users = new \Models\User();
                	 $users->update($password_form, $username, 'name');
                	 \Helpers\redirect('/users/' . $username);
            		}
        	} else
           		 $password_form = \Forms\Password::get_initial_data();

		$password_form['__token'] = \Helpers\generate_token();
       		$ctx = ['form' => $password_form,
            		'site_title' => 'Правка пароля пользователя'];
       		 $this->render('user_password_edit', $ctx);
    }

	function activate(string $username, string $token) {

		//Ищем пользователя по имени
      		  $users = new \Models\user();
        	  $user = $users->get($username, 'name');

		//если не находим
        	  if (!$user)
           		 $s = 'Пользователя нет в списке';

		//если пользователь уже активен
       		 else {
           		 if ($user['active'])
              	         $s = 'Пользователь уже активирован';
            	 else {


			//  ВЫЧИСЛЯЕМ ПРАВИЛЬНЫЙ ТОКЕН ЗАНОВО
       		       // Берём имя пользователя и секретный ключ, хешируем

               		 $t = hash_hmac('ripemd256', $username, \Settings\SECRET_KEY);

                	 if ($t != $token)
                   	 	$s = 'Неверный интернет-адрес';

               		 else {
                  		  $users->update(['active' => TRUE], $user['id']);
                  		  $s = 'Активация прошла успешно';
               			 }
           		 }
       		 }
       		 $ctx = ['message' => $s, 'site_title' => 'Активация пользователя'];
      	        $this->render('activate', $ctx);
    }


	
 	/* Страница управления пользователями (только для админа)*/
 	
	function users() {
  		  // Проверка: только администратор
    		if (!$this->current_user || empty($this->current_user['admin'])) {
       				 throw new \Page403Exception();
   			 }
    
   			 $users = new \Models\User();
    		$users->select('*', NULL, '', NULL, 'name');
    
    		$ctx = [
        			'users' => $users,
       				 'site_title' => 'Управление пользователями'
    			];
    			$this->render('users', $ctx);
	}

	function delete_user(string $username) {
   		 // Проверка: только администратор
    	if (!$this->current_user || empty($this->current_user['admin'])) {
       			 throw new \Page403Exception();
    		}
    
    	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      		  \Helpers\check_token($_POST);
        		$users = new \Models\User();
       		 	$users->delete($username, 'name');
       		 \Helpers\redirect('/admin/users');
    	} 
		else {
        	$users = new \Models\User();
       		 $user = $users->get_or_404($username, 'name');
       		 $ctx = [
           				'user' => $user,
            			'site_title' => 'Удаление пользователя',
           				 '__token' => \Helpers\generate_token()
        		];
        	$this->render('user_delete', $ctx);
    }
}




}


