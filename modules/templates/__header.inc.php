<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
        <base href="<?php echo ($_SERVER['SERVER_NAME'] == 'localhost') ? '/diplom/' : '/'; ?>">
	<link href="/diplom/style.css" rel="stylesheet" type="text/css">
	<title><?php echo ((isset($site_title)) ? $site_title . ' :: ' : '') ?>
		<?php echo \Settings\SITE_NAME ?>
	</title>
	
</head>
<body>
	<header>
	
		<h1 class="pos1" ><a href="/diplom/"><?php echo \Settings\SITE_NAME ?></a></h1>
		

	</header>
<?php	
			$user_name = '';
			if ($__current_user && is_array($__current_user)) {
				if(!empty($__current_user['name1']) && !empty($__current_user['name2']))
					$user_name = $__current_user['name1'] . ' ' . $__current_user['name2'];
				else if(!empty($__current_user['name1']))
					$user_name = $__current_user['name1'];
				else
				$user_name = $__current_user['name'] ?? ' ';
		} ?>

		<!-- Кнопка для открытия мобильного меню -->
		<button class="mobile-menu-toggle" id="mobileMenuToggle">☰ Меню</button>
<br>
		<section  class="mobile-menu" id="mobileMenu">
			<?php if($__current_user) { ?>
				<a href="/diplom/users/<?php echo $__current_user['name'] ?>">
				<?php echo $user_name ?></a>
				<a href="/diplom/users/<?php echo $__current_user['name'] ?>/favorites">❤️ Избранное</a>
				<?php if(!empty($__current_user['admin'])) { ?>
   					 <a href="/diplom//admin/users">Управление пользователями</a>
				<?php } ?>
				<a href="/diplom/users/<?php echo $__current_user['name'] ?>/account/edit">Изменить данные</a>
      			<a href="/diplom/users/<?php echo $__current_user['name'] ?>/account/editpassword">Сменить пароль</a>
				<a href="/diplom/logout" >Выйти</a>
				<a href="/diplom/users/<?php echo $__current_user['name'] ?>/account/delete">Удалить аккаунт</a>
				<?php } else { ?>
					<a href="/diplom/register">Зарегистрироваться</a>
					<a href="/diplom/login">Войти</a>
				<?php } ?>
		</section>
<br>
<script>
    // Тоггл мобильного меню при клике на кнопку
    const toggleBtn = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (toggleBtn && mobileMenu) {
        toggleBtn.addEventListener('click', function() {
            if (mobileMenu.style.display === 'none' || mobileMenu.style.display === '') {
                mobileMenu.style.display = 'flex';
            } else {
                mobileMenu.style.display = 'none';
            }
        });
    }
</script>