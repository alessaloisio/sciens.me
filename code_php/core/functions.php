<?php 

function debug($var){

	if(Config::$debug>0){
		$debug = debug_backtrace(); 
		echo '<p>&nbsp;</p><p><a href="#" onclick="$(this).parent().next(\'ol\').slideToggle(); return false;"><strong>'.$debug[0]['file'].' </strong> l.'.$debug[0]['line'].'</a></p>'; 
		echo '<ol style="display:none;">'; 
		foreach($debug as $k=>$v){ if($k>0){
			echo '<li><strong>'.$v['file'].' </strong> l.'.$v['line'].'</li>'; 
		}}
		echo '</ol>'; 
		echo '<pre>';
		print_r($var);
		echo '</pre>'; 
	}
}

function getToken($user){
	return sha1($user->lastLogin.'-'.$user->email.'___'.$user->created.'-'.Config::$salt);
}

function cryptPassword($password){
	return sha1($password.'----'.Config::$salt);
}

function dateNow(){
	return (new \DateTime())->format('Y-m-d H:i:s');
}

?>