<?php 

class Post extends Model{

	public $table = 'blog_posts';
	
	var $validate = array(
		'title' => array(
			'rule' => 'notEmpty',
			'message' => 'Vous devez présicer un titre.'
		),
		'slug' => array(
			'rule' => '([a-z0-9\-]+)',
			'message' => "L'URL n'est pas valide."
		)
	);
}

?>