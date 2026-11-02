<?php
namespace Models;

class Favorite extends \Models\Model {
    protected const TABLE_NAME = 'favorites';
    protected const DEFAULT_ORDER = 'uploaded DESC';
    protected const RELATIONS = [
        'users' => ['external' => 'user', 'primary' => 'id'],
        'recipes' => ['external' => 'recipe', 'primary' => 'id']
    ];

    function deleteRecipe(int $user_id, int $recipe_id) {
    $this->run(
        'DELETE FROM favorites WHERE user = ? AND recipe = ?',
        [$user_id, $recipe_id]
    );

    }
}