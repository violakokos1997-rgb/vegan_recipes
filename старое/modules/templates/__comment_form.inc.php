<form class="bigform" method="post">
	<input type="hidden" name="__token" value="<?php echo $form['__token'] ?>">
	<label for="comment_contents">Содержание</label><textarea id="comment_contents" name="contents"><?php echo $form['contents']?></textarea>
	<?php \Helpers\show_errors('contents', $form) ?>
	<input type="submit" value="Отправить">
</form>