<form class="bigform" method="post" enctype="multipart/form-data" >
	<label for="recipe_category">Категория</label>
	<select id="recipe_category" name="category">
		<?php foreach ($categories as $category) { ?>
			<option value="<?php echo $category['id'] ?>"
				<?php if($form['category'] == $category['id']) { ?> selected <?php } ?>>
				<?php echo $category['name'] ?>
			</option>
		<?php } ?>
	</select>
 <label for="recipe_title">Название</label>
    <input type="text" id="recipe_title" name="title"
  	  value="<?php echo $form['title'] ?>">
   	 <?php \Helpers\show_errors('title', $form) ?>

    <label for="recipe_description">Описание</label>
    	<textarea id="recipe_description" name="description"><?php echo $form['description'] ?></textarea>

	<label for="recipe_ingredients">Ингредиенты</label>
	<textarea id="recipe_ingredients" name="ingredients"><?php echo $form['ingredients'] ?? '' ?></textarea>

    <label for="recipe_instruction">Инструкция приготовления</label>
    	<textarea id="recipe_instruction" name="instruction"><?php echo $form['instruction'] ?></textarea>

    <label for="recipe_file">Файл с изображением</label>
    	<input type="file" id="recipe_file" name="recipe">
   	 <?php \Helpers\show_errors('recipe', $form) ?>
	<input type="hidden" name="__token" value="<?php echo $form['__token'] ?>">
    <input type="submit" value="Отправить">
</form>