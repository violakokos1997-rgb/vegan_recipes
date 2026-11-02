<?php require \Helpers\get_fragment_path('__header') ?>

<br><br>

<h2 id="recept-title">Удаление пользователя</h2>
<form  class="bigform" method="post" style="width: 500px; margin: 0 auto;">

	
	<p style="text-align: center; font-size: 16pt; ">Вы уверены, что хотите удалить аккаунт?</p>
	<input type="hidden" name="__token" value="<?php echo $__token ?>">
    <input type="submit" value="Удалить" class="cat-item" id="back"><br>
</form>

<br><br>
<div class="cats"><a href="/" class="cat-item" id="btn"  >На главную</a></div>
<?php require \Helpers\get_fragment_path('__footer') ?>
