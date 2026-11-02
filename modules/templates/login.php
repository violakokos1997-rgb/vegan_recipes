<?php require \Helpers\get_fragment_path('__header') ?>
<h1 id="recept-title">Вход</h1>
<form class="bigform" method="post">
	<label for="user_name">Имя</label>
	<input type="text" id="user_name" name="name" value="<?php echo $form['name'] ?>">
	<?php \Helpers\show_errors('name', $form) ?>
	
	<label for="user_password">Пароль</label>
	<input type="password" id="user_password" name="password">
	<?php \Helpers\show_errors('password', $form) ?>
	<input type="submit" value="Войти">
</form>
<div class="cats"><a href="/" class="cat-item" id="btn" >На главную</a></div>
<?php require \Helpers\get_fragment_path('__footer') ?>