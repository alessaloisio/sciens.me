<?php 

/**
* Dispatcher
*/
class Dispatcher{

	var $request;
	
	function __construct(){
		// On récupère l'url
		$this->request = new Request();
		// On parse l'url
		Router::parse($this->request->url, $this->request);
		/*
		* CONTROLLER
		*/
		// On charge le controlleur et la méthode

		$controller = $this->loadController();

		// CONNECTER ACCES
		// Si module dashboard
		if(isset($controller->request->channel)){
			if($controller->request->module == "dashboard" and !$controller->Session->isLogged() or $controller->request->module == "dashboard" and $controller->Session->isLogged() and $controller->request->channel->id_user != $controller->Session->read('user')->id_user){
				$this->error('Vous ne pouvez pas acceder à cette page.');
			}
		}

		if(!in_array($this->request->action, array_diff(get_class_methods($controller), get_class_methods('Controller')))){
			$this->error('Le controller '.$this->request->controller.' n\'a pas de méthode '.$this->request->action.'.');
		}

		// Permet d'appeller la méthode selon l'action, en donnat les autres paramètres
		call_user_func_array(array($controller, $this->request->action), $this->request->params);
		// On rend automatique la vue
		$controller->render($this->request->action);	
	}

	function loadController(){
		$name  = ucfirst($this->request->controller).'Controller';
		if(isset($this->request->prefix)){
			$file = MODULES.DS.$this->request->module.DS.'controller'.DS.$this->request->prefix.DS.$name.'.php';
		}else{
			$file = MODULES.DS.$this->request->module.DS.'controller'.DS.$name.'.php';
		}
		
		// Vérifie si le controller existe
	    if(file_exists($file)){
	        require($file);
			$controller = new $name($this->request);
			$controller->Session = new Session($this->request);
			$controller->Form = new Form($controller);
			return $controller;
	    }else{
	        $this->error('Le controller '.$this->request->controller.' n\'existe pas.');
	    }
	}

	function error($msg){
		$controller = new Controller($this->request);
		$controller->e404($msg);
	}
}

?>