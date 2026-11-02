<?php
namespace Controllers;
class Comments extends BaseController {

	private function check_user(int $comment_index){
		$comments = new \Models\Comment();
		$comment = $comments->get_or_404($comment_index, 'id', 'user');
		if(!($this->current_user && ($this->current_user['id'] == $comment['user'] || $this->current_user['admin'])))
			throw new \Page403Exception();
	}


    function edit(string $recipe_slug, int $comment_index) {

	$this->check_user($comment_index);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		\Helpers\check_token($_POST);

	    //чистим и проверяем введенные новые данные
            $comment_form = \Forms\Comment::get_normalized_data($_POST);

	    //если ошибки нет 
            if (!isset($comment_form['__errors'])) {

		//возвращаем подготовленные данные для редактирования в БД
                $comment_form =\Forms\Comment::get_prepared_data($comment_form);

                $comments = new \Models\Comment();

		/*
			массив с новыми значениями - $comment_form, например ['user'=>'admin', 'contents'=>'вкусно!']
			индекс исправляемого комментария - $comment_index
		*/
                $comments->update($comment_form, $comment_index);
                \Helpers\redirect('/recipe/' . $recipe_slug .
                    \Helpers\get_GET_params(['page', 'filter', 'ref']));
            }
        } 
	
	//Если метод GET - просто показываем форму со значениями по умолчанию
	else {
            $comments = new \Models\Comment();
            $comment = $comments->get_or_404($comment_index);
            $comment_form =\Forms\Comment::get_initial_data($comment);
        }

	$comment_form['__token']=\Helpers\generate_token();
        $users = new \Models\User();
        $users->select('*', NULL, '', NULL, 'name');
        $ctx = ['form' => $comment_form, 
		'users' => $users,
                'recipe' => $recipe_slug,
                'site_title' => 'Правка комментария'];
        $this->render('comment_edit', $ctx);
    }

    function delete(string $recipe_slug, int $comment_index) {

	$this->check_user($comment_index);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	    \Helpers\check_token($_POST);

            $comments = new \Models\Comment();
            $comments->delete($comment_index);
            \Helpers\redirect('/recipe/' . $recipe_slug .
                \Helpers\get_GET_params(['page', 'filter',
                'ref']));
        } 

	else {
            $comments = new \Models\Comment();
            $comment = $comments->get_or_404($comment_index,
                'comments.id',
                'users.name AS user_name, contents, uploaded',
                ['users']);
            $ctx = ['comment' => $comment,
                'recipe' => $recipe_slug, //из роутера
		'__token' => \Helpers\generate_token(),
                'site_title' => 'Удаление комментария'];
            $this->render('comment_delete', $ctx);
        }
    }
}
