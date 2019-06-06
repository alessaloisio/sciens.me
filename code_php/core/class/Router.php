<?php 

/**
* Router
*/
class Router{

	static $routes = array();
	static $module;
	static $subdomain;
	static $prefix;
	
	static function parse($url, $request){

		/*GET SUBDOMAINE*/
		if(htmlspecialchars($_SERVER['HTTP_HOST']) != HOST_NAME and htmlspecialchars($_SERVER['HTTP_HOST']) != HOST){
			$subdomain = explode('.', htmlspecialchars($_SERVER['HTTP_HOST']))[0];
		}else{
			$subdomain = False;
		}
		$request->subdomain = $subdomain;
		self::$subdomain = $request->subdomain;

		$url = trim($url,'/');

		
		// On est à la base d'un sous-domaine
		if(empty($url) and $subdomain){
			$request->module = 'redirect';
			new Controller($request);
			die();
		}
		
		/*
		* On récupère l'url de redirection
		*/
		if(empty($url)){
			$url = Router::$routes[0]['url']; 
		}else{
			$match = false; 
			foreach(Router::$routes as $v){
				if(!$match && preg_match($v['redirreg'],$url,$match)){
					$url = $v['origin'];
					foreach($match as $k=>$v){
						$url = str_replace(':'.$k.':',$v,$url); 
					} 
					$match = true; 
				}
			}
		}

		$params = explode('/',$url);

		/*
		* On récupère les modules
		*/
		$modules = array_slice(scandir(MODULES), 2);
		// On vérifie si le premier params est dans les modules
		$request->module = in_array($params[0], $modules) ? $params[0] : 'base';
		self::$module = $request->module;
		// On enlève le module du tableau
		if($request->module != 'base'){
			$params = array_slice($params, 1);
		};

		// Si on est dans le dashboard ou l'admin, j'ai besoin d'un préfixe pour les modules
		if($request->module === 'dashboard' or $request->module === 'admin'){
			$modules = array_diff($modules, array("base", "admin", "dashboard"));
			if(isset($params[0]) and in_array($params[0], $modules)){
				$request->prefix = $params[0];
				self::$prefix = $params[0];
				$params = array_slice($params, 1);
			}
		}

		$request->controller = isset($params[0]) ? $params[0] : 'pages';
		$request->action = isset($params[1]) ? $params[1] : 'index';
		$request->params = array_slice($params, 2);

		//debug($request);debug(BASE_URL);die();

		// Si on est dans le module base ou admin et qu'il y a un sous domaine
		if(in_array($request->module, array('base', 'admin')) and $request->subdomain != False){
			// Si on est dans un sous domaine et qu'on veut afficher le module base ou admin
				header("Location: ".Router::url($request->url, false, true));die();
		}

		// Si on est a la base et qu'on demande d'afficher un module different de admin et base
		if(!in_array($request->module, array('base', 'admin')) and !$request->subdomain){
			header("Location: ".Router::url());die();
		}

		return true;
	}

	static function connect($redir, $url){
		$r = array();
		$r['params'] = array();
		$r['url'] = $url; 

		$r['originreg'] = preg_replace('/([a-z0-9]+):([^\/]+)/','${1}:(?P<${1}>${2})',$url);
		$r['originreg'] = str_replace('/*','(?P<args>/?.*)',$r['originreg']);
		// ON remplace les slash pour la regex
		$r['originreg'] = '/^'.str_replace('/','\/',$r['originreg']).'$/'; 
		// MODIF les regex par les valeurs
		$r['origin'] = preg_replace('/([a-z0-9]+):([^\/]+)/',':${1}:',$url);
		$r['origin'] = str_replace('/*',':args:',$r['origin']); 

		$params = explode('/',$url);
		foreach($params as $k=>$v){
			if(strpos($v,':')){
				$p = explode(':',$v);
				$r['params'][$p[0]] = $p[1]; 
			}
		} 

		$r['redirreg'] = $redir;
		$r['redirreg'] = str_replace('/*','(?P<args>/?.*)',$r['redirreg']);
		foreach($r['params'] as $k=>$v){
			$r['redirreg'] = str_replace(":$k","(?P<$k>$v)",$r['redirreg']);
		}
		$r['redirreg'] = '/^'.str_replace('/','\/',$r['redirreg']).'$/';

		$r['redir'] = preg_replace('/:([a-z0-9]+)/',':${1}:',$redir);
		$r['redir'] = str_replace('/*',':args:',$r['redir']); 

		self::$routes[] = $r;
	}

	static function url($url = '', $auto=true, $base=false){
		$beforeUrl = 'http://';

		// Demande de redirection à la base de l'url
		if(empty($url)){
			return $beforeUrl.BASE_URL;
		}

		// Si on a un sous-domaine, on le rejoute au début de l'url
		if(self::$subdomain and HOST_NAME == BASE_URL and !$base){
			$beforeUrl .= self::$subdomain.'.';
		}

		
		if($auto){
			// Permet de rajouter le module automatiquement à l'url
			if(self::$module != 'base'){
				$url = self::$module.'/'.trim($url,'/');
			}else{
				$url = trim($url,'/');
			}
			 
			foreach(self::$routes as $v){
				if(preg_match($v['originreg'],$url,$match)){
					$url = $v['redir']; 
					foreach($match as $k=>$w){
						$url = str_replace(":$k:",$w,$url); 
					}
				}
			}
		}else{
			$url = trim($url,'/');
		}

		if($base == false){
			return $beforeUrl.BASE_URL.'/'.$url;
		}else{
			return $beforeUrl.HOST_NAME.'/'.$url;
		}
		 

	}



}

?>
