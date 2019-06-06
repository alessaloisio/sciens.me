<?php 

/**
* Session
*/
class Session{

	public function __construct($request){
		if(!isset($_SESSION)){
			ini_set("session.save_path","/home/aloisioa/tmp/sessions");
			// Permet de garder le cookie pour les sous domaine
			ini_set('session.cookie_domain', '.'.HOST_NAME);
			ini_set('session_name', 'PHPSESSID');

			session_start();
		}
	}

	public function setFlash($msg, $type='error'){
		$_SESSION['flash'] = array(
			'message' => $msg,
			'type' => $type
		);
	}

	public function flash(){
		if(!empty($_SESSION['flash']['message'])){
			$html = '<div class="alert-message '.$_SESSION['flash']['type'].'"><p>'.$_SESSION['flash']['message'].'</p></div>';
			$_SESSION['flash'] = array();
			return $html;
		}
		return false;
	}

	public function add($key, $value){
		$_SESSION[$key] = $value;
	}

	public function delete($key){
		if(isset($_SESSION[$key])){
			unset($_SESSION[$key]);
		}
	}

	public function read($key){
		if(isset($_SESSION[$key])){
			return $_SESSION[$key];
		}
		return false;
	}

	public function isLogged(){
		if(!empty($this->read('user')->id_user)){
			return true;
		}
		return false;
	}

	public function user($key){
		if(!empty($_SESSION['user'])){
			return $this->read('user')->$key;
		}
		return false;
	}


	
}

?>
