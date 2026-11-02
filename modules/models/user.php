<?php
namespace Models;
class User extends \Models\Model {
	protected const TABLE_NAME = 'users';
	protected const DEFAULT_ORDER = 'name';
	protected const RELATIONS = ['recipes' => ['external' => 'id', 'primary' => 'user']];
	
	protected function before_delete($value, $key_field = 'id') {
        	if ($key_field != 'id') {
           		 $users = new \Models\User();
           		 $user = $users->get($value, $key_field, 'id');
           		 $value = $user['id'];
        	}
       		 $recipes = new \Models\Recipe();
       		 $recipes2 = new \Models\Recipe();
      		  $recipes->select('id', NULL, 'user = ?', [$value]);
       		 foreach ($recipes as $recipe)
          		  $recipes2->delete($recipe['id']);
   	 }

}