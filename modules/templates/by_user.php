<?php require \Helpers\get_fragment_path('__header') ?>

<?php require \Helpers\get_fragment_path('__filter_form') ?>
<?php $gets = \Helpers\get_GET_params(['page','filter'], ['ref'=> 'user']) ?>


<h1 id="recept-title">Пользователь : <?php echo $user['name']?></h1>

<?php if($__current_user && $__current_user['id'] == $user['id']) { ?>

<p><a href="<?php echo '/diplom/users/' . $user['name'] . '/recipes/add' . $gets ?>" id="add">Добавить рецепт</a></p>

<?php } ?>

 <div id="dishes" class="user-dishes">
		<?php 
			if($count_rec === FALSE){ ?>
				<h4>Опубликованных рецептов нет</h4>
		<?php } else {
			 	foreach($recps as $recipe){ ?>
					<div class="recipe-card">
						<a href="/diplom/recipe/<?php echo $recipe['slug'], $gets ?>" class="dish" >
							<img src="<?php echo \Settings\IMAGE_PATH . $recipe['filename'] ?>" title="<?php echo $recipe['title']?>">
							<h4><?php echo $recipe['title']?></h4>
							<h5>Опубликовано:<br>
							<?php echo $recipe['uploaded']?>
						
							</h5>
							
						</a>
					
						<?php if($__current_user && ($__current_user['id'] == $recipe['user'] || $__current_user['admin'])) { ?>

						<p style="text-align: center;"><a href="/diplom/users/<?php echo $user['name']?>/recipes/<?php echo $recipe['slug']?>/edit" id="links" >Изменить рецепт</a></p>
						<p style="text-align: center;"><a href="/diplom/users/<?php echo $user['name']?>/recipes/<?php echo $recipe['slug']?>/delete" id="links_2" >Удалить рецепт</a></p>
					
						<?php } ?>
						
				</div>
		<?php }} ?>
	
</div>

<br>
<div class="cats"><a href="/diplom/" class="cat-item" id="btn" >На главную</a></div>
<?php require \Helpers\get_fragment_path('__paginator') ?>
<?php require \Helpers\get_fragment_path('__footer') ?>