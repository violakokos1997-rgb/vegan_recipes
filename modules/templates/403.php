<?php http_response_code(403) ?>
<?php require \Helpers\get_fragment_path('__header') ?>
<h2>Доступ запрещен</h2>
<p style="text-align: center; color: #ec0076!important; font-size: 20pt;">У вас нет прав на доступ к этой странице или выполнения этой операции.</p>
<div class="cats"><p><a href="/">На главную</a></p></div>
<?php require \Helpers\get_fragment_path('__footer')?>