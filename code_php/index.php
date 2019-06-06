<?php

$startTime = microtime(true);

/*
* On définit les constantes
*/
define('ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
define('CORE', ROOT.DS.'core');
define('CONTENT', ROOT.DS.'content');
define('MODULES', CONTENT.DS.'modules');
define('THEMES', CONTENT.DS.'themes');

// URL-HOST
define('HOST', '92.222.40.232');
define('HOST_NAME', 'sciens.me');

$domain = htmlspecialchars($_SERVER['HTTP_HOST']);

// Permet d'enlever le sous domaine pour la base de l'url
if(count(explode('.', $domain)) > 2 and $domain != HOST){
	$domain = preg_replace("/^(.*?)\.(.*)$/","$2", $domain);
	if(HOST_NAME != $domain){
		header('Location: http://'.$domain);
		die();
	}
}

if(!is_file(explode('/', trim(htmlspecialchars($_SERVER['SCRIPT_NAME']), '/'))[0])){
	define('BASE_URL', $domain.'/'.explode('/', trim(htmlspecialchars($_SERVER['SCRIPT_NAME']), '/'))[0].'/');
}else{
	define('BASE_URL', $domain);
}

// Inclus tous les fichiers
require(CORE.DS.'includes.php');

// On commence le traitement
new Dispatcher();

// Récupérer le temps de chargement
if(Config::$debug >= 1){
	echo '<div id="time" style="position:fixed;bottom:0;background-color:#900;color:#fff;line-height:30px;height:30px;left:0;right:0;padding-left:10px;">';
	echo 'Page générée en '.round((microtime(true) - $startTime), 5).' secondes.';
	echo '</div>';
}
?>
