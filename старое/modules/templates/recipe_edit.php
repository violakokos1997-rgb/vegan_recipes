<?php require \Helpers\get_fragment_path('__header') ?>
	<style>
		
		header{
			background-color: rgba(255, 211, 211, 0.9);

		}
		body {
    			background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.6)), url('/images/fon.jpeg') !important;
   			 background-size: cover !important;
   			 background-attachment: fixed !important;
	
		}

		#recept-title{
			background: rgba(255, 255, 255, 0.5);
			text-shadow: 1px 1px #3E2A1F;
		}

		form.bigform {
			background-color: rgba(255, 255, 255,  0.5);
		}
		form.bigform label {
			font-size: 20pt;
    			color: #3E2A1F !important;  /* тёмно-коричневый, как у тебя везде */
  			  font-weight: bold;
		}

		form.bigform input[type="text"],
		form.bigform select,
		form.bigform textarea {
  			 background: rgba(255, 255, 255, 0.6);
   			 color: #3E2A1F;
		}



	</style>
<h1 id="recept-title">Правка рецепта</h1>
<?php require \Helpers\get_fragment_path('__recipe_form') ?>
<?php $ret = '/users/' . $username . \Helpers\get_GET_params(['page', 'filter']) ?>
<div class="cats"><a href="<?php echo $ret ?>" class="cat-item" id="btn" >Назад</a></div>
<?php require \Helpers\get_fragment_path('__footer') ?>
