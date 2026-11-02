<?php http_response_code(503) ?>
<?php require \Helpers\get_fragment_path('__header') ?>
<div style="text-align: center;">
<h2>Внутренняя ошибка сервера </h2>
<pre style="color: #ec0076!important;"><?php echo $message?>


<?php echo $file, 'line', $line?></pre>
</div>
<p><a href="/'>На главную</a></p>


<?php require \Helpers\get_fragment_path('__footer') ?>