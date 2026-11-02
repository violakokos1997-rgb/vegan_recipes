<?php require \Helpers\get_fragment_path('__header') ?>
<?php $gets = \Helpers\get_GET_params(['page','filter'], ['ref'=> 'recipe', 'recipe_slug' => $item['slug']]) ;?>

<?php
	switch($_GET['ref'] ?? ''){
		case 'index':
			$ref = '/';
			$ref .= \Helpers\get_GET_params(['page','filter']);
			break;
		case 'cat':
			$ref = '/cats/' . $item['cat_slug'] . '/';
			$ref .= \Helpers\get_GET_params(['page','filter']);
			break;
		case 'user':
			$ref = '/users/' . $item['user_name'] . '/';
			$ref .= \Helpers\get_GET_params(['page','filter']);
			break;
		default:
			$ref = '/';
			$ref .= \Helpers\get_GET_params(['page','filter']);
			break;
}
?>

<h1 id="title"><?php echo $item['title'] ?></h1>
<div></div>
<div class="recipe-container">
	
	<img id="recipe" src="<?php echo \Settings\IMAGE_PATH . $item['filename'] ?>" >

	<h3 >Категория : </h3>
	<h4><a href="/cats/<?php echo $item['cat_slug'], $gets?>" id="url"><?php echo $item['cat_name']?></a></h4>

	<h3 >Описание блюда :</h3>
	<h4><?php echo $item['description']?></h4>

	<?php  if(empty($item['ingredients']) OR ($item['ingredients']) == NULL){ ?>
		<h3>Ингредиенты не указаны</h3>
	<?php } else { ?>
	<h3>Ингредиенты: </h3>
	<h4><?php echo $item['ingredients']; }?></h4>


	<h3>Инструкция приготовления :</h3>
	<h4><?php echo $item['instruction']?></h4>

	<h3>Автор рецепта : </h3>
	<h4><a href="/users/<?php echo $item['user_name']?>" id="url">
		<?php echo $item['user_name']?></a></h4>

	
	<?php $has_record = false; 
	$u2 =\Helpers\get_GET_params(['page', 'filter', 'ref']) ;
	
	foreach ($comment as $c) {
			if(!$has_record){ ?>
				<h3>Комментарии к рецепту : </h3>
				<?php $has_record = true; } 
			$u1 = '/recipe/' . $item['slug'] . '/comments/' . $c['id'] ?>
			
    			<h4 id="cmnt" ><?php echo $c['user_name'] ?> : <?php echo $c['contents'] . '  ' . \Helpers\get_formatted_timestamp($c['uploaded']) ?></h4>

			<?php if($__current_user && ($__current_user['id'] == $c['user'] || $__current_user['admin'])) { ?>

			<p style="text-align: center"><a href="<?php echo $u1 . '/edit' . $u2 ?>">Исправить |</a>
			<a href="<?php echo $u1 . '/delete' . $u2 ?>">Удалить</a></p>
	<?php }} if(!$has_record) { ?>
	<h4>Коментариев нет. Будьте первыми !</h4>
	<?php } ?>
	<?php if($__current_user) { ?>
	<h3>Добавить комментарий</h3>
	<?php require \Helpers\get_fragment_path('__comment_form') ?>
	<?php } ?>

</div>
<p></p>
<div class="cats">
	<a href="<?php echo $ref ?>" class="cat-item" id="btn" >Назад</a>
</div>
<?php require \Helpers\get_fragment_path('__footer') ?>