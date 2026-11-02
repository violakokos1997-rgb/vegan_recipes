<?php require \Helpers\get_fragment_path('__header'); ?>
<?php $gets = \Helpers\get_GET_params(['page','filter'], ['ref'=> 'favorites']); ?>

<h1 id="title">❤️ Моё избранное</h1>

<div id="dishes">
 
    <?php 
    //Флаг: есть ли рецепты
    $has_recipes = false; 

     foreach($favorites as $recipe) { 
         $has_recipes = true;
        ?>
        <a href="/diplom/recipe/<?php echo $recipe['slug'], $gets ?>" class="dish">
            <img src="<?php echo \Settings\IMAGE_PATH . $recipe['filename'] ?>" title="<?php echo $recipe['title'] ?>">
            <h4><?php echo $recipe['title'] ?></h4>
            <h5>Автор: <?php echo $recipe['user_name'] ?></h5>
        </a>

       


        
    <?php } 
    if(!$has_recipes) { ?>
        <p style="text-align: center; width: 100%; color: #7a9a7a; font-size: 18px; padding: 40px 0;">
            У вас пока нет избранных рецептов.<br>
            Добавьте их, нажав ❤️ на странице рецепта.
        </p>
    <?php } ?>
    
  
</div>

<div class="cats">
    <a href="/diplom/" class="cat-item" id="btn">На главную</a>
</div>

<?php require \Helpers\get_fragment_path('__footer'); ?>