<?php 

/**
* Controller
*/
class Controller{

	public $request;
	private $vars = array();
	public $layout = 'index';
	public $themes = 'default';
	private $rendered = False;
	
	function __construct($request=null){
		if($request){
			$this->request = $request;

			// On rajoute le chaine dans request
			if($request->subdomain){
				// On appel le modèle 
				new Model($request);
				$this->request->channel = Model::$channel;
				// Si on a rien, on envoie l'erreur
				if(!$this->request->channel){
					$this->e404("Nous n'avons pas trouvé d'élément pour ".$this->request->subdomain.".");
				}

				// Si on est à la base de la chaine on redirige vers le module qu'il veut voir à la base
				if($request->url === "/" and $request->subdomain and $request->module === "redirect"){
					header('Location: '.Router::url($request->channel->basePage, false));
				}
			}
			
			// Si on récupère un token de connexion, on essaye de connecter le client
			if(isset($request->token) and !empty($request->token)){
				// récupérer l'id de l'utilisateur avec ce token
				$model = new Model($request);
				$model->table = "users";
				$model->primaryKey = "id_user";

				$user = $model->findFirst(array(
					'conditions' => array(
						'token' => $request->token
					)
				));

				if($user){
					if(isset($_SESSION)){
						session_destroy();
					}
					session_id($user->phpsessid);
					session_start();

					$request->token = false;
					// renouveller le lastLogin et le token
					$lastLogin = dateNow();
					$token = getToken($user);

					// On met à jour quelques infos de l'utilisateur
					$model->save((object) array(
						'id_user' => $user->id_user,
						'lastLogin' => $lastLogin,
						'token' => NULL,
						'phpsessid' => NULL
					));

					$this->redirect();
				}	
			}
			
			if(isset($_SESSION['referer'])){
				unset($_SESSION['referer']);					
			}
		}
	}

	public function render($view){
		// Auto rendu
		if($this->rendered){ return false; }

		// On extrait les variables a envoyé à la vue
		extract($this->vars);
		// On récupère les modules
		$modules = array_slice(scandir(THEMES.DS.$this->themes.DS.'templates'), 2);
		
		/*
		*	GESTION DES ERREURS
		*/
		if(strpos($view, '/') === 0){
			require(THEMES.DS.'base'.$view.'.php');
			return False;
		}

		/*
		*	GESTION DES THEMES ET LAYOUT
		*/

		if($this->layout == False and !in_array($this->request->module, $modules)){
			// Pas de layout && pas un module
			require(THEMES.DS.$this->request->module.DS.$this->request->controller.DS.$view.'.php');
		}elseif($this->layout == False and in_array($this->request->module, $modules)){
			// Pas de layout && un module
			require(THEMES.DS.$this->themes.DS.'templates'.DS.$this->request->module.DS.$this->request->controller.DS.$view.'.php');
		}elseif($this->layout != False and !in_array($this->request->module, $modules)){
			// Avec de layout && pas un module
			ob_start();
			if(isset($this->request->prefix)){
				$file = THEMES.DS.$this->request->module.DS.$this->request->prefix.DS.$this->request->controller.DS.$view.'.php'; 
			}else{
				$file = THEMES.DS.$this->request->module.DS.$this->request->controller.DS.$view.'.php'; 
			}
			require($file);
			$content_layout = ob_get_clean();
			require(THEMES.DS.$this->request->module.DS.$this->layout.'.php');
		}elseif($this->layout != False and in_array($this->request->module, $modules)){
			// Avec de layout && un module
			ob_start();
			require(THEMES.DS.$this->themes.DS.'templates'.DS.$this->request->module.DS.$this->request->controller.DS.$view.'.php');
			$content_layout = ob_get_clean();
			require(THEMES.DS.$this->themes.DS.'templates'.DS.$this->request->module.DS.$this->layout.'.php');
		}

		$this->rendered = True;
	}

	public function set($key, $value=null){
		if(is_array($key)){
			$this->vars += $key;
		}else{
			$this->vars[$key] = $value;
		}
	}

	public function loadModel($name){
		if(!isset($this->$name)){
			require_once(MODULES.DS.$this->request->module.DS.'model'.DS.$name.'.php');
			$this->$name = new $name($this->request);
			if(isset($this->Form)){
				$this->$name->Form = $this->Form;
			}
		}else{
			return False;
		}
	}

	public function e404($msg){
		header('HTTP/1.0 404 Not Found');
		$this->set('msg', $msg);
		$this->render('/errors/404');
		die();
	}

	private function request($req, $controller, $action){
		// On reprend la requete et on remplace le controller et l'action
		$req->controller = $controller;
		$req->action = $action;
		$controller .= 'Controller';

		if(isset($req->prefix)){
			require_once(MODULES.DS.$req->module.DS.'controller'.DS.$req->prefix.DS.$controller.'.php');
		}else{
			require_once(MODULES.DS.$req->module.DS.'controller'.DS.$controller.'.php');
		}
		$c = new $controller($req);
		return $c->$action();
	}

	public function redirect($url, $code=null){
		if($code == 301){
			header("HTTP/1.1 301 Moved Permanently");
		}
		header("Location: ".Router::url($url));
		die();
	}

	public function redIfLog(){
		if($this->Session->isLogged()){
			$this->redirect('/');
			die();
		}
		return true;
	}

}

?>
