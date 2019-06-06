<?php 

class PagesController extends Controller{

	function index(){
		$this->layout = False;
		$this->set('message', "Voici la boutique en ligne");
		$this->render('index');
	}
	
}

?>