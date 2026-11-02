<?php require \Helpers\get_fragment_path('__header') ?>
<h1 id="recept-title">Правка сведений о пользователе</h1>
<form class="bigform" method="post">
    <label for="user_email">Адрес электронной почты</label>
    <input type="text" id="user_email" name="email"
    value="<?php echo $form['email'] ?>">
    <?php \Helpers\show_errors('email', $form) ?>
    <label for="user_name1">Настоящее имя</label>
    <input type="text" id="user_name1" name="name1"
    value="<?php echo $form['name1'] ?>">
    <?php \Helpers\show_errors('name1', $form) ?>
    <label for="user_name2">Настоящая фамилия</label>
    <input type="text" id="user_name2" name="name2"
    value="<?php echo $form['name2'] ?>">
    <?php \Helpers\show_errors('name2', $form) ?>
   
 <div style="display: flex; align-items: center; gap: 10px;">
        <label for="user_emailme">Получать оповещения о новых комментариях?</label>
        <input type="checkbox" id="user_emailme" name="emailme" value="1" <?php if ($form['emailme']) echo 'checked' ?>>
    </div>

	<input type="hidden" name="__token" value="<?php echo $form['__token'] ?>">
    <input type="submit" value="Отправить">
</form>
<div class="cats"><a href="/" class="cat-item"  id="btn" >На главную</a></div>
<?php require \Helpers\get_fragment_path('__footer') ?>
