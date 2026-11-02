<?php require \Helpers\get_fragment_path('__header') ?>
<h2 id="edit">Правка комментария</h2>

<form class="bigform" method="post" style="width: 500px; margin: 0 auto;">

	<input type="hidden" name="__token" value="<?php echo $form['__token'] ?>">
	
	<label for="comment_contents" style="margin: 0 auto">Содержание</label><textarea id="comment_contents" name="contents"><?php echo $form['contents']?></textarea>
	<div class="cats">
	<input type="submit" value="Сохранить">
	<a href="/recipe/<?php echo $recipe . \Helpers\get_GET_params(['page', 'filter', 'ref']);?>"   class="cat-item"  >Назад</a>
</div>		
	
	
</form>



<?php require \Helpers\get_fragment_path('__footer') ?>
