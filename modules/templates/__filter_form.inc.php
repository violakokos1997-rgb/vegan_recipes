<?php
	if(isset($_GET['filter']) && !empty($_GET['filter']))
		$f = $_GET['filter'];
	else
		$f = '';
?>
<form id="filter_form" method="get">
	<input type="text" name="filter" placeholder="Фильтрация" value="<?php echo $f ?>">
	<input type="submit" value="Искать">
</form>