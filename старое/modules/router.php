<?php
$request_path= $_GET['route'];
if ($request_path && $request_path[-1] == '/' )
	$request_path = substr($request_path, 0, strlen($request_path)-1);


$result=[];
$res=[];

if(preg_match('/^users\/(\w+)$/u', $request_path, $res)===1){
	$ctr=new \Controllers\Recipes();
	$ctr->by_user($res[1]);
}

else if(preg_match('/^cats\/(\w+)$/', $request_path, $result)===1){
	$ctr=new \Controllers\Recipes();
	$ctr->by_cat($result[1]);
}

else if(preg_match('/^recipe\/([\w-]+)$/u', $request_path, $result)===1)
{
	$ctr = new \Controllers\Recipes();
	$ctr->recipe($result[1]);
}



else if(preg_match('/^users\/(\w+)\/recipes\/add$/u', $request_path, $result) ===1){
	$ctr = new \Controllers\Recipes();
	$ctr->add($result[1]);
}

else if(preg_match('/^users\/(\w+)\/recipes\/([\w-]+)\/edit$/u', $request_path, $result) ===1){
	$ctr = new \Controllers\Recipes();
	$ctr->edit($result[1], $result[2]);
}

else if(preg_match('/^users\/(\w+)\/recipes\/([\w-]+)\/delete$/u', $request_path, $result) ===1){
	$ctr = new \Controllers\Recipes();
	$ctr->delete($result[1], $result[2]);
}

else if(preg_match('/^recipe\/([\w-]+)\/comments\/(\d+)\/edit$/u', $request_path, $result) ===1){
	 $recipe_slug = $result[1];
	$comment_index= (integer)$result[2];
	$ctr=new \Controllers\Comments();
	$ctr->edit($recipe_slug, $comment_index);
}
else if(preg_match('/^recipe\/([\w-]+)\/comments\/(\d+)\/delete$/u', $request_path, $result) ===1){
	 $recipe_slug = $result[1];
	$comment_index= (integer)$result[2];
	$ctr=new \Controllers\Comments();
	$ctr->delete($recipe_slug, $comment_index);
}

else if(preg_match('/^users\/(\w+)\/account\/delete$/u', $request_path, $result) ===1){
	$ctr = new \Controllers\Account();
	$ctr->delete($result[1]);
}

 else if (preg_match('/^users\/(\w+)\/account\/edit$/u',
    $request_path, $result) === 1) {
    $ctr = new \Controllers\Account();
    $ctr->edit($result[1]);
} 

else if (preg_match('/^users\/(\w+)\/account\/editpassword$/u',
    $request_path, $result) === 1) {
    $ctr = new \Controllers\Account();
    $ctr->edit_password($result[1]);
} 

else if($request_path == 'login'){
	$ctr= new \Controllers\Login();
	$ctr->login();
}

else if($request_path == 'logout'){
	$ctr= new \Controllers\Login();
	$ctr->logout();
}

else if($request_path == 'register') {
	$ctr = new \Controllers\Login();
	$ctr->register();
}

else if($request_path =='register/complete'){
	$ctr = new \Controllers\Login();
	$ctr->register_complete();
}


else if(preg_match('/^users\/([^\/]+)\/account\/activation\/([^\/]+)$/u', $request_path, $result)===1){
	$ctr = new \Controllers\Account();
	$ctr->activate($result[1], $result[2]);
}


else if($request_path == ''){
	$ctr = new \Controllers\Recipes();
	$ctr->list();
}

else {
	throw new Page404Exception();
}