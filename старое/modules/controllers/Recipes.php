<?php
namespace Controllers;
class Recipes extends BaseController{

	private function check_user(string $recipe_slug){
		$recipes = new \Models\Recipe();
		$recipe = $recipes->get_or_404($recipe_slug, 'slug', 'user');
		if(!($this->current_user && ($this->current_user['id'] == $recipe['user'] || $this->current_user['admin'])))
			throw new \Page403Exception();
	}


	function list(){
		$cats= new \Models\Category();
		$cats->select();
		$recps=new \Models\Recipe();

		$w = '';
		$p = [];

		if(isset($_GET['filter']) && !empty($_GET['filter'])) {

			//Допустим, пользователь ввёл: "картошка, лук, морковь
		 	$filter = $_GET['filter'];

			// Разбиваем на отдельные ингредиенты (по запятым или пробелам)
			// Результат: ['картошка', 'лук', 'морковь']
  			$ingredients = preg_split('/[ ,]+/', $filter);
			foreach ($ingredients as $ing) {
				// Если пустое то пропускаем этот ингредиент
				if (empty(trim($ing))) continue;

				//Если в $w уже есть какой-то текст - добавляем OR в конец
				//(title LIKE ? ...) OR (title LIKE ? ...) OR (title LIKE ? ...)
			        if ($w) $w .= ' AND ';
	
				//Добавляем в строку поиска условие, которое ищет текущий ингредиент во всех полях
				$w .= '(title LIKE ? OR description LIKE ? OR instruction LIKE ? OR ingredients LIKE ?)';
				$like = '%' . trim($ing) . '%';
					$p[] = $like;
					$p[] = $like;
					$p[] = $like;
					$p[] = $like;
			}
		}
		
		$count_rec = $recps->get_record('COUNT(*) AS cnt' , NULL, $w, $p);
		$paginator = new \Paginator($count_rec['cnt']);

		$recps->select('recipes.id, title, filename, slug, uploaded', 
				[], $w, $p, '' , $paginator->first_record_num, \Settings\RECORDS_ON_PAGE);
		$ctx = ['cats'=>$cats, 'recps'=>$recps, 'paginator'=>$paginator];
		$this-> render('list', $ctx);
	}


	function recipe(string $slug){
		$recps= new \Models\Recipe();
		$item = $recps->get_record('recipes.*, categories.name AS cat_name, categories.slug AS cat_slug, users.name AS user_name', ['categories', 'users'], 'recipes.slug = ?', [$slug]);
		if (!$item) {
       			 throw new \Page404Exception();
    			}		

		//список комментариев
		$comment = new \Models\Comment();
		$comment->select('comments.id AS id, user, users.name AS user_name, contents, recipe, uploaded', ['users'], 'recipe = ?',[$item['id']], '');
		
		if ($_SERVER['REQUEST_METHOD'] =='POST') {


			if(!$this->current_user)
				throw new \Page403Exception();

			\Helpers\check_token($_POST);

			/*
				чистка и проверка данных из формы
				get_normalized_data($_POST) получает данные, которые пришли из формы. 
				При отправке формы браузер помещает их в суперглобальный массив $_POST.
				 Это стандарт 
			*/

			$comment_form = \Forms\Comment::get_normalized_data($_POST);

			//если нет ошибок
			if(!isset($comment_form['__errors'])) {

				//подготовка данных для БД
				$comment_form = \Forms\Comment::get_prepared_data($comment_form);

				//привязка комментария к текущему рецепту(добавление ключа рецепт со значением id рецепта)
				$comment_form['recipe']=$item['id'];

			//возможная проблема

				$comment_form['user'] = $this->current_user['id'];

				//создание модели комментария и сохранение нового комментария в БД
				$comments = new \Models\Comment();
				$comments->insert($comment_form);

				$recipes = new \Models\Recipe();
				$user = $recipes->get($slug, 'slug', 'recipes.id, users.name, users.email, users.emailme', ['users']);
				

				if($user['emailme']) {
					$values = ['title' => \Settings\SITE_NAME, 'name'=> $user['name'],
						'url'=>'http://' . $_SERVER['SERVER_NAME'] . '/' . $slug];


				\Helpers\send_mail($user['email'], 'notification_subject', 'notification_body', $values);
				}

				//перенаправление по тому же адресу но без метода POST
				\Helpers\redirect('/recipe/' . $slug . \Helpers\get_GET_params(['page', 'filter', 'ref']));
			}
		}
		//если метод не POST
		else
			/*возвращение массива с начальными значениями
			(либо из БД, либо значения прописанные по умолчанию, если таковых нет то пустые строки)*/

			$comment_form = \Forms\Comment::get_initial_data(['uploaded' => time()]);

		$comment_form['__token'] = \Helpers\generate_token();

		$ctx=[	'item' => $item, 
			'site_title'=> $item['title'], 
			'comment'=> $comment, 
			'form'=>$comment_form
			];
		$this->render('recipe', $ctx);
	}


	function by_cat(string $slug){
		$cats = new \Models\Category();
		$cat = $cats->get_or_404($slug, 'slug', 'id, name');
		$w = 'category = ?';
		$p = [$cat['id']];

		if(isset($_GET['filter']) && !empty($_GET['filter'])) {
		$w .= ' AND (title LIKE ? OR description LIKE ? OR instruction LIKE ?)';
		$f = '%' . $_GET['filter'] . '%';
		$p[] = $f;
		$p[] = $f;
		$p[] = $f;
		}
		$recps= new \Models\Recipe();
		$pict_count_rec = $recps->get_record('COUNT(*) AS cnt' , NULL, $w, $p);
		
		$recipe_slug = $_GET['recipe_slug']?? null;
		$one_record = null;

		if ($recipe_slug) {
		$one_record = $recps->get_record('recipes.*, 
				categories.name AS cat_name, 
				categories.slug AS cat_slug,
				 users.name AS user_name', 
				['categories', 'users'], 
				'recipes.slug = ?', [$recipe_slug]);
		}

		$paginator = new \Paginator($pict_count_rec['cnt'], ['filter']);

		$recps->select('recipes.id, title, filename, uploaded, slug, ' .
				'users.name AS user_name, ' .
				'(SELECT COUNT(*) FROM comments WHERE ' .
				'comments.recipe=recipes.id) AS comment_count',
				['users'], $w, $p, '', $paginator->first_record_num, \Settings\RECORDS_ON_PAGE);
		 $all_cats = new \Models\Category();
    		$all_cats->select(); 
	
		$ctx=['cat'=>$cat, 'all_cats'=>$all_cats, 'recps'=>$recps,
			 'one_record'=> $one_record,
			 'paginator' =>$paginator,
			 'site_title'=>$cat['name'] . ' :: Категории' ];

		$this->render('by_cat', $ctx);
	}

	function by_user(string $name){
		$users = new \Models\User();
		$user = $users->get_or_404($name, 'name', 'id, name');

		$w = 'user = ?';
		$p = [$user['id']];

		if(isset($_GET['filter']) && !empty($_GET['filter'])) {
		$w .= ' AND (title LIKE ? OR description LIKE ? OR instruction LIKE ?)';
		$f = '%' . $_GET['filter'] . '%';
		$p[] = $f;
		$p[] = $f;
		$p[] = $f;
		}

		$recps= new \Models\Recipe();
		$count_rec = $recps->get_record('COUNT(*) AS cnt' , NULL, $w, $p);
		$paginator = new \Paginator($count_rec['cnt'], ['filter']);

		$count_rec = $count_rec['cnt'] > 0;

		$recps->select('recipes.id, title, categories.name AS category, recipes.slug AS slug, filename, uploaded, ' .
				'(SELECT COUNT(*) FROM comments WHERE ' .
				'comments.recipe=recipes.id) AS comment_count, recipes.user',
				['users', 'categories'], $w, $p, '', $paginator->first_record_num, \Settings\RECORDS_ON_PAGE);
		
		
		$ctx=['user'=>$user, 'recps'=>$recps, 'count_rec'=> $count_rec, 'paginator'=>$paginator, 'site_title'=>$user['name'] . ' :: Пользователи'];
		$this->render('by_user', $ctx);
	}

	function add(string $username){

		if($_SERVER['REQUEST_METHOD'] == 'POST') {

			if(!$this->current_user)
				throw new \Page403Exception();

			\Helpers\check_token($_POST);

			

			/* Возвращает массив с чистыми данными, 
			если есть ошибка добавляет в массив ключ __errors и значение ошибки*/

			//данные рецепта (название, описание, инструкция, категория, файл картинки)
			$recipe_form = \Forms\Recipe::get_normalized_data($_POST);

			//Если ошибок нет
			if(!isset($recipe_form['__errors'])){

				//подготовка данных для сохранения в форме, преобразование в формат понятный БД
				$recipe_form = \Forms\Recipe::get_prepared_data($recipe_form);

				//поиск пользователя по имени
				$users = new \Models\User();
				$user = $users->get_or_404($username, 'name', 'id');

				//Добавляет в массив $recipe_form поле user (ID пользователя)
				$recipe_form['user'] = $user['id'];

				//Сохраняет картинку в БД. 
				//При этом сработает before_insert в модели Recipe, который сохранит файл на диск
				$recipes = new \Models\Recipe();
				$recipes->insert($recipe_form);

				//После успешного сохранения — перенаправляет на страницу пользователя
				\Helpers\redirect('/users/' . $username);
			}	
		} else
			//Получаем массив с начальными значениями для формы
			//(либо из БД, либо знач.прописанные по умолчанию , либо пустые строки)

			$recipe_form = \Forms\Recipe::get_initial_data();


		$recipe_form['__token'] = \Helpers\generate_token();

		$categories = new \Models\Category();

		// SELECT * FROM categories ORDER BY name - необходим для выпадающего списка на форме,
		// чтобы пользователь выбрал категорию

		$categories->select();

		$ctx = ['site_title'=>'Добавление рецепта',
			 'username'=>$username, 'form'=> $recipe_form, 
			'categories'=>$categories];
		$this->render('recipe_add', $ctx);

	}

	function edit(string $username, string $recipe_slug) {

	$this->check_user($recipe_slug);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		\Helpers\check_token($_POST);

            $recipe_form =  \Forms\Recipe2::get_normalized_data($_POST);

		//если ошибок нет
            if (!isset($recipe_form['__errors'])) {

		//подготавливаем данные
                $recipe_form = \Forms\Recipe2::get_prepared_data($recipe_form);
                $recipes = new \Models\Recipe();

		//обновляем данные 
                $recipes->update($recipe_form, $recipe_slug, 'slug');
                \Helpers\redirect('/users/' . $username . \Helpers\get_GET_params(['page', 'filter']));
            }
        } else {
            $recipes = new \Models\Recipe();
            $recipe = $recipes->get_or_404($recipe_slug, 'slug', '*');
            $recipe_form = \Forms\Recipe2::get_initial_data($recipe);
        }

	 $recipe_form['__token'] = \Helpers\generate_token();
        $categories = new \Models\Category();
        $categories->select();
        $ctx = ['form' => $recipe_form, 'categories' => $categories,
            'username' => $username, 'site_title' => 'Правка рецепта'];
        $this->render('recipe_edit', $ctx);
    }

    function delete(string $username, string $recipe_slug) {

	$this->check_user($recipe_slug);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		\Helpers\check_token($_POST);

            $recipes = new \Models\Recipe();
            $recipes->delete($recipe_slug, 'slug');
            \Helpers\redirect('/users/' . $username .
                \Helpers\get_GET_params(['page', 'filter']));
        } else {
            $recipes = new \Models\Recipe();
            $recipe = $recipes->get_or_404($recipe_slug, 'slug',
                'title, uploaded');
            $ctx = ['recipe' => $recipe, 'username' => $username,
                'site_title' => 'Удаление изображения', '__token'=>\Helpers\generate_token()];
            $this->render('recipe_delete', $ctx);
        }
    }

}