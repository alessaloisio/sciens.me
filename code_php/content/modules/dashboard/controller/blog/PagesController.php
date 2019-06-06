<?php 

class PagesController extends Controller{

	function index(){

	}

	function getMenu(){
		$this->loadModel('Post');
		// Les pages du menu
		$pages = array(
			'0' => array('blog', 'Blog'),
			'1' => array('store', 'Boutique'),
			'2' => array('forum', 'Forum'),
			'3' => array('config', 'Configuration')
		);

		// Création du menu
		$html = '<div id="nav"><ul>';
		for ($i=0; $i < count($pages); $i++) {
			$li = '<li>';
			if(Router::url($pages[$i][0]) === 'http://'.BASE_URL.$_SERVER['REQUEST_URI']){
				$li = '<li class="active">';
			}
			$html .= $li.'<a href="'.Router::url($pages[$i][0]).'">'.$pages[$i][1].'</a></li>';
		}
		$html .= '</ul></div>';

		return $html;
	}

}

?>