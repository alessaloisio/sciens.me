<?php 

class User extends Model{

	public $table = 'users';
	public $primaryKey = 'id_user';

	var $validate = array(
		'email' => array(
			'rule' => 'email',
			'message' => 'Votre adresse email n\'est pas valide.'
		),

		'name' => array(
			'rule' => '[a-zA-z]+',
			'message' => "Le prénom doit être un format alphanumérique"
		),

		'surname' => array(
			'rule' => '[a-zA-z]+',
			'message' => "Le nom doit être un format alphanumérique"
		),
		
		'password' => array(
			'rule' => 'password',
			'empty' => "Vous devez présicer un mot de passe.",
			'lenght' => "Le mot de passe doit comporter entre 6 et 16 caractères."
		),

		'password2' => array(
			'rule' => 'password',
			'empty' => "Vous devez présicer un mot de passe.",
			'lenght' => "Le mot de passe doit comporter entre 6 et 16 caractères."
		)
	);

}

?>