<?php require \Helpers\get_fragment_path('__header') ?>
	

<h1 id="recept-title">Добавление рецепта</h1>
<?php require \Helpers\get_fragment_path('__recipe_form') ?>
<?php $ret= '/users/' . $username . \Helpers\get_GET_params(['page', 'filter']) ?>
<div class="cats">
	<a href="<?php echo $ret ?>" class="cat-item" id="btn">Назад</a>
</div>
<?php require \Helpers\get_fragment_path('__footer') ?>