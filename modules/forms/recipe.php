<?php
namespace Forms;
class Recipe extends \Forms\Form{
	protected const FIELDS = ['title' => ['type' => 'string'],
				'instruction'=>['type'=> 'string'], 
				'description'=> ['type'=> 'string', 'optional' => TRUE],
				'ingredients'=>['type'=> 'string', 'optional'=>TRUE, 'initial'=>'Ингредиенты не указаны'],
				'category'=>['type'=>'integer']
					];
	//сообщает о том, что посетитель обязан указать файл с изображением
	protected const IS_PICTURE_OPTIONAL = FALSE;

	//допустимые расширения
	private const EXTENSIONS = ['gif', 'jpg', 'jpeg', 'jpe', 'png', 'svg'];

	protected static function after_normalize_data(&$data, &$errors) {

		$file = $_FILES['recipe'];
		$error = $file['error'];
	
		if ($error == UPLOAD_ERR_NO_FILE) {

			/*Если поле обязательное добавляем ошибку, если нет не добавляем ничего
			     if выполняется когда внутри true, 
				true возможно только тогда 
					когда "это изображение не обязательно?" = ("Нет, обязательно) т.е.false
			*/
			if(!static::IS_PICTURE_OPTIONAL)

				//Ключ 'recipe' — имя поля формы (файл с изображением)

				$errors['recipe'] = 'Укажите файл с изображением';

		} else if (!in_array(pathinfo($file['name'], PATHINFO_EXTENSION), self::EXTENSIONS))
			$errors['recipe'] = 'Укажите файл с изображением в формате GIF, JPEG, PNG или SVG';

		else if ($error == UPLOAD_ERR_OK){}

		else if ($error == UPLOAD_ERR_INT_SIZE || $error == UPLOAD_ERR_FORM_SIZE)
			$errors['recipe'] = 'Укажите файл размером не более 2 Мб';

		//Если произошла другая ошибка: частичная отправка, или нет временной папки и т.д.

		else
			$errors['recipe'] = 'Файл не был отправлен';
	}
}