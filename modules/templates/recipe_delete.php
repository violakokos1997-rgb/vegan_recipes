<?php require \Helpers\get_fragment_path('__header') ?>
<?php $ref = '/users/' . $username .  \Helpers\get_GET_params(['page', 'filter']) ?>
<!--<h1 id="recept-title">Удаление рецепта</h1>
<div class="delete-container">-->
<form class="bigform" method="post" style="width: 500px; margin: 0 auto; text-align: center;">
	<h2>Удаление рецепта</h2>
	<p style="text-align: center; font-size: 16pt; ">Вы уверены, что хотите удалить этот рецепт?</p>
	<p style="color: green;">&quot;<?php echo $recipe['title'] ?>&quot;</p>
	<p style="text-align: center; font-size: 16pt; color: green;" >Опубликовано пользователем: <?php echo $username ?></p>
	<p style="color: green;">Дата и время публикации:</p>
	<p style="color: green;"><?php echo \Helpers\get_formatted_timestamp($recipe['uploaded']) ?></p>
	
		  <input type="hidden" name="__token" value="<?php echo $__token ?>">
  		  <input type="submit" value="Удалить" class="cat-item" id="delete1">
		
	</form>

<br>
<br>
	<div class="cats"><a href="<?php echo $ref ?>" class="cat-item" id="btn">Назад</a></div>

<br><br>
<?php require \Helpers\get_fragment_path('__footer') ?>
