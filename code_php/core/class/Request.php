<?php 

/**
* Request
*/
class Request{

	public $url;
	public $page = 1;
	public $data = false; 	
	public $referer = false; 	
	
	function __construct(){
		// INIT XSS Library
		require_once('XSS.php');
		$filter = new Filter();

		$allowed_protocols = array('http', 'ftp', 'mailto');
		$allowed_tags = array('p', 'a', 'i', 'b', 'em', 'span', 'strong', 'ul', 'ol', 'li', 'table', 'tr', 'td', 'thead', 'th', 'tbody');

		$filter->addAllowedProtocols($allowed_protocols);
		$filter->addAllowedTags($allowed_tags);


		// L'url demander
		$this->url = isset($_SERVER['PATH_INFO']) ? htmlspecialchars($_SERVER['PATH_INFO']) : '/';
		
		// Récupérer l'url précédent
		if(isset($_SERVER['HTTP_REFERER'])){
			$url = parse_url(htmlspecialchars($_SERVER['HTTP_REFERER']));
			if(!is_numeric(strpos($url['host'], HOST_NAME))){
				$this->referer = $filter->xss($url['scheme'].'://'.$url['host'].$url['path']);
			}
		}
		
		// Permet de gérer la pagination
		if(isset($_GET['page'])){
			if(is_numeric($_GET['page'])){
				if($_GET['page'] > 0){
					$this->page = $filter->xss(round($_GET['page']));
				}
			}
		}

		// Gère le système de connexion à travers les domaines de mon serveurs.
		if(isset($_GET['scode'])){
			$token = htmlspecialchars($_GET['scode']);
			$this->token = $filter->xss($token);
		}

		// Si des données ont été postées ont les entre dans data
		if(!empty($_POST)){
			// On parse chaque élément pour le mettre dans l'objet data
			$this->data = new stdClass(); 
			foreach($_POST as $k=>$v){
				// On sécurise les variables
				$k = $filter->xss($k);
				$v = $filter->xss($v);

				$this->data->$k=$v;
			}
		}

	}

}

?>