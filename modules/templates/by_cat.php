<?php require \Helpers\get_fragment_path('__header') ?>
<?php require \Helpers\get_fragment_path('__filter_form') ?>
<?php $gets = \Helpers\get_GET_params(['page', 'filter'], ['ref'=>'cat']) ?>

<?php
	switch($_GET['ref'] ?? ''){
		case 'index':
			$ref = '/';
			$ref .= \Helpers\get_GET_params(['page','filter']);
			break;
		case 'recipe':
			if ($one_record && isset($one_record['slug'])) {
				$ref = '/recipe/' . $one_record['slug'] . '/';
				$ref .= \Helpers\get_GET_params(['page','filter', 'cat']);}
			else
				$ref='/';
			break;
		default:
			$ref = '/';
			
			break;

}
?>
<div class="cats">
  	 <?php foreach ($all_cats as $c) { ?>
       			  <a href="/cats/<?php echo $c['slug'], $gets ?>" class="cat-item">
          	  		<?php echo $c['name']; ?>
        	  	 </a>
   		 <?php } ?>
</div>
<h1 class="name_category"><?php echo $cat['name']?></h1>
 <div id="dishes">
		<?php
			foreach($recps as $recipe){
		?>
		<a href="/diplom/recipe/<?php echo $recipe['slug'], $gets ?>" class="dish">
			<img src="<?php echo \Settings\IMAGE_PATH . $recipe['filename'] ?>" title="<?php echo $recipe['title']?>">
			<h4><?php echo $recipe['title']?></h4>
			<h5>Опубликовано:<br>
			<?php echo \Helpers\get_formatted_timestamp($recipe['uploaded'])?></h5>
		</a>
		<?php } ?>
	
</div>


<?php require \Helpers\get_fragment_path('__paginator') ?>


<p></p>
<div class="cats">
	<a href="/diplom/<?php echo $ref ?>" class="cat-item" id="btn" >Назад</a>
</div>


<?php require \Helpers\get_fragment_path('__footer') ?>