<?php
namespace Models;
class Recipe extends \Models\Model {
	protected const TABLE_NAME = 'recipes';
	protected const DEFAULT_ORDER = 'uploaded DESC';
	protected const RELATIONS = ['categories' => ['external' => 'category', 'primary' => 'id'],
					'users' => ['external' => 'user', 'primary' => 'id']];

	protected function before_insert(&$fields){
		$filename = \Helpers\save_file($_FILES['recipe']);
		$fields['filename'] = $filename;
		$fields['ingredients'] = $_POST['ingredients'] ?? '';
		
		if (empty($fields['ingredients'])) {
       			 $fields['ingredients'] = NULL;
   		 }
        	/*$fields['title'] = str_replace(['&#34;', '&quot;'], '"', $fields['title']);
        	$fields['title'] = str_replace('"', '', $fields['title']);*/

		$fields['slug'] = \Helpers\generate_slug($fields['title']);
	}
	protected function before_update(&$fields, $value, $key_field = 'id') {
		if($_FILES['recipe']['error'] != UPLOAD_ERR_NO_FILE) {
			$this->before_delete($value, $key_field);
			$this->before_insert($fields);
		}
	}
	protected function before_delete($value, $key_field = 'id') {
		$rec = $this->get_or_404($value, $key_field, 'filename');
		\Helpers\delete_file($rec['filename']);
	}

}