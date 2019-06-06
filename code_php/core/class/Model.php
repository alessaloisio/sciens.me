<?php 

/**
* Model
*/
class Model{

	static $connections = array();
	public $conf = 'default';
	public $table = false;
	public $request;
	public $db;
	public $primaryKey = 'id';
	static $channel = false;
	public $id;
	public $validate = false;
	public $errors = array();
	public $form;


	function __construct($request){

		$this->request = $request;

		// Init la table
		if($this->table === false){
			$this->table = $this->request->module.'_'.strtolower(get_class($this)).'s';
		}

		// On se connecte à la base

		if($_SERVER['SERVER_NAME'] === 'localhost'){
			$this->conf = 'local';
		}
			
		$confdb = Config::$bdd[$this->conf];

		if(isset(Model::$connections[$this->conf])){
			$this->db = Model::$connections[$this->conf];
			return true;
		}

		try{
			$pdo = new PDO('mysql:host='.$confdb['host'].';dbname='.$confdb['database'].';',$confdb['login'], $confdb['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
			if(Config::$debug >= 1){$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);}
			Model::$connections[$this->conf] = $pdo;
			$this->db = $pdo;
		}catch(PDOException $e){
			if(Config::$debug >= 1){
				die($e->getMessage());
			}else{
				die('Le site est en maintenance réessayer plus tard.');
			}
		}	

		// Permet de récupérer les infos de la chaîne
		if(!self::$channel){
			$channel = $this->findFirst(array(
				'table' => 'channels',
				'fields' => 'id_channel id, name, slug, id_user, basePage',
				'conditions' => array(
					'slug' => $request->subdomain
				)
			));

			if(!empty($channel)){
				self::$channel = $channel;
			}
		}
	}

	/*
	* Permet de retourner des valeurs MYSQL -> SELECT
	*/
	public function find($req = array()){
		$sql = 'SELECT ';

		if(isset($req['fields'])){
			if(is_array($req['fields'])){
				$sql .= implode(', ',$req['fields']);
			}else{
				$sql .= $req['fields']; 
			}
		}else{
			$sql.='*';
		}

		if(isset($req['table'])){
			$sql .= ' FROM '.$req['table'].' as '.get_class($this).' ';
		}else{
			$sql .= ' FROM '.$this->table.' as '.get_class($this).' ';
		}

		// Liaison
		if(isset($req['join'])){
			foreach($req['join'] as $k=>$v){
				$sql .= 'LEFT JOIN '.$k.' ON '.$v.' '; 
			}
		}

		// Construction de la condition
		if(isset($req['conditions'])){
			$sql .= 'WHERE ';
			if(!is_array($req['conditions'])){
				$sql .= $req['conditions']; 
			}else{
				$cond = array(); 
				foreach($req['conditions'] as $k=>$v){

					if(!is_numeric($v)){$v = $this->db->quote($v);}
					if($v == $this->db->quote('notnull')){
						$cond[] = "$k IS NOT NULL";
					}else{
						$cond[] = "$k=$v";
					}
				}
				$sql .= implode(' AND ',$cond);
			}
		}
		

		if(isset($req['order'])){
			$sql .= ' ORDER BY '.$req['order'];
		}


		if(isset($req['limit'])){
			$sql .= ' LIMIT '.$req['limit'];
		}

		$pre = $this->db->prepare($sql); 
		$pre->execute(); 
		return $pre->fetchAll(PDO::FETCH_OBJ);
	}

	public function findFirst($req){
		return current($this->find($req));
	}

	public function findCount($conditions){
		$res = $this->findFirst(array(
			'fields' => 'COUNT('.$this->primaryKey.') as count',
			'conditions' => $conditions
			));
		return $res->count;  
	}

	public function delete($id){
		$sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = $id";
		$this->db->query($sql);
	}

	public function save($data){

		$key = $this->primaryKey;
		$fields = array();
		$d = array();

		foreach ($data as $k => $v) {
			if($k != $key){
				$fields[] = "$k=:$k";
			}
			$d[":$k"] = $v;
		}

		if(isset($data->$key) and !empty($data->$key)){
			$sql = 'UPDATE '.$this->table.' SET '.implode(', ', $fields).' WHERE '.$key.'=:'.$key;
			$this->id = $data->$key;
			$action = 'update';
		}else{
			$sql = 'INSERT INTO '.$this->table.' SET '.implode(', ', $fields);
if(isset($d[':id'])){unset($d[':id']);}
			$action = 'insert';
		}

		//debug($sql);

		$pre = $this->db->prepare($sql);
		$pre->execute($d);

		if($action == 'insert'){
			$this->id = $this->db->lastInsertId();
		}

		return true;

	}

	public function validates($data){

		foreach ($this->validate as $k => $v) {
			if(isset($data->$k)){
				if(!isset($data->$k)){
					$this->errors[$k] = $v['message'];
				}else{
					if($v['rule'] == 'notEmpty'){
						if(empty($data->$k)){
							$this->errors[$k] = $v['message'];
						}
					}
					//email
					elseif($v['rule'] == 'email'){
						if(empty($data->$k) || !filter_var($data->$k, FILTER_VALIDATE_EMAIL)){
							$this->errors[$k] = $v['message'];
						}
					}
					//password
					elseif($v['rule'] == 'password'){
						if(!empty($data->$k)){
							if(strlen($data->$k) <= 6 or strlen($data->$k) >= 16){
								$this->errors[$k] = $v['lenght'];
							}
						}else{
							$this->errors[$k] = $v['empty'];
						}
					}
					//preg_match
					elseif(!preg_match('/^'.$v['rule'].'$/', $data->$k)){
						$this->errors[$k] = $v['message'];
					}
				}
			}
		}

		// On ajoute les erreurs au formulaire
		if(isset($this->Form)){
			$this->Form->errors = $this->errors;
		}

		//debug($this->errors);

		// Si pas d'erreurs on retourne true
		if(empty($this->errors)){
			return true;
		}

		return false;
	}
}

?>
