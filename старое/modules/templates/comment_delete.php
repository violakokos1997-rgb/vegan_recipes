<?php require \Helpers\get_fragment_path('__header') ?>

<form class="bigform" method="post" style="width: 500px; margin: 0 auto;">
	<h2>Удаление комментария</h2>
	<p style="text-align: center; font-size: 16pt; ">Вы уверены, что хотите удалить этот комментарий?</p>


	<label for="comment user" style="margin: 0 auto">Пользователь</label>
	<div class="form-field-display"><?php echo $comment['user_name']; ?></div>
	
	
	<label for="comment_contents" style="margin: 0 auto">Содержание</label><textarea id="comment_contents" name="contents" readonly><?php echo $comment['contents']?></textarea>


	<div class="cats">
		<input type="hidden" name="__token" value="<?php echo $__token ?>">
   		<input type="submit" value="Да, удалить">
    		<a href="/recipe/<?php echo $recipe,\Helpers\get_GET_params(['page', 'filter', 'ref']);?>"   class="cat-item" >Отмена</a>
	</div>
	
</form>

<?php require \Helpers\get_fragment_path('__footer') ?>
