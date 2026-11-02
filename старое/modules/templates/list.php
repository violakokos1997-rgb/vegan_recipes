<?php require \Helpers\get_fragment_path('__header') ?>
<?php require \Helpers\get_fragment_path('__filter_form') ?>
<?php $gets = \Helpers\get_GET_params(['page','filter'], ['ref'=> 'index']) ?>	



<div class="cats">
  	 <?php foreach ($cats as $cat) { ?>
       			  <a href="/cats/<?php echo $cat['slug'], $gets ?>" class="cat-item">
          	  		<?php echo $cat['name']; ?>
        	  	 </a>
   		 <?php } ?>
</div>
  
 <div id="dishes">
		<?php
			foreach($recps as $recipe){
		?>
		<a href="/recipe/<?php echo $recipe['slug'], $gets ?>" class="dish">
			<img src="<?php echo \Settings\IMAGE_PATH . $recipe['filename'] ?>" title="<?php echo $recipe['title']?>">
			<h4><?php echo $recipe['title']?></h4>
			<h5>Опубликовано:<br>
			<?php echo \Helpers\get_formatted_timestamp($recipe['uploaded'])?></h5>
		</a>
		<?php } ?>
	
</div>

	

<?php require \Helpers\get_fragment_path('__paginator') ?>
<?php require \Helpers\get_fragment_path('__footer') ?>