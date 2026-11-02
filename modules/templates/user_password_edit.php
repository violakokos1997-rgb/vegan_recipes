<?php require \Helpers\get_fragment_path('__header') ?>
<h1 id="recept-title">Смена пароля</h1>
<form class="bigform" method="post">
    <label for="user_password1">Пароль</label>
    <input type="password" id="user_password1"
    name="password1">
    <?php \Helpers\show_errors('password1', $form) ?>
    <label for="user_password2">Подтверждение пароля</label>
    <input type="password" id="user_password2"
    name="password2">
    <?php \Helpers\show_errors('password2', $form) ?>
	<input type="hidden" name="__token" value="<?php echo $form['__token'] ?>">
    <input type="submit" value="Отправить">
</form>
<div class="cats"><a href="/" class="cat-item" id="btn" >На главную</a></div>
<?php require \Helpers\get_fragment_path('__footer') ?>
