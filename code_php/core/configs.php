<?php

class Config{

	static $debug = 1;

	static $bdd = array(
		'local' => array(
			'host' => 'localhost',
			'database' => 'sciensme',
			'login' => 'root',
			'password' => ''
		),
		'default' => array(
			'host' => '',
			'database' => '',
			'login' => '',
			'password' => ''
		)
	);

	static $salt = "";
}


/*
*	Configurer les routes
*/

// Base
Router::connect('/', 'pages/index');

// Blog
Router::connect('blog', 'blog/posts');
Router::connect('blog/:slug-:id', 'blog/posts/view/id:([0-9]+)/slug:([a-z0-9\-]+)');
Router::connect(':slug-:id', 'blog/pages/view/id:([0-9]+)/slug:([a-z0-9\-]+)');

// Dashboard
Router::connect('dashboard', 'dashboard/home/index');

?>
