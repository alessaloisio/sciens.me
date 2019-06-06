<?php 

class UsersController extends Controller{
	
	// Page d'inscription
	function register(){
		$this->redIfLog();

		if($this->request->data){
			$this->loadModel('User');

			if($this->User->validates($this->request->data)){
				$data = $this->request->data;
				$data->password = cryptPassword($data->password);
				$data->password2 = cryptPassword($data->password2);

				if($data->password == $data->password2){
					// On commence la sauvegarde
					unset($data->password2);
					$data->created = dateNow();
					$data->token = sha1($data->email.'-'.$data->created.'-'.Config::$salt);

					// On sauvergarde
					$this->User->save($data);

					// On envoie d'email afin d'activer
					$urlActive = Router::url('users/active').'/'.$data->token;
					// faut créer une class ... http://php.net/manual/fr/function.mail.php

					$to      = $data->email;
					$subject = 'Activation de votre compte';
					$message = 'Bonjour '.$data->name.' ! Voici votre lien d\'activation '.$urlActive;
					$headers = 'From: contact@sciens.me' . "\r\n" .
					'Reply-To: contact@sciens.me' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();

					mail($to, $subject, $message, $headers);


					$this->Session->setFlash('Nous vous avons envoyé un email de confirmation', 'success');
					$this->redirect('/');
				}else{
					$this->Session->setFlash('Les mots de passe ne sont pas identique.', 'error');
				}

				// On 
				$this->request->data->password = '';
				$this->request->data->password2 = '';
			}
		}
	}

	// Page pour activer le compte
	function active($token){
		$this->redIfLog();

		if($token){
			$token = htmlspecialchars($token);

			$this->loadModel('User');
			$user = $this->User->findFirst(array(
				'conditions' => array(
					'token' => $token
				)
			));

			if($user){
				// le connecter et ajouter le lastLogin et supprimer le token
				unset($user->password);
				// Last login
				$lastLogin =  dateNow();

				// On met à jour quelques infos de l'utilisateur
				$this->User->save((object) array(
					'id_user' => $user->id_user,
					'lastLogin' => $lastLogin,
					'token' => NULL
				));

				// On enregistre l'utilisateur dans la session
				$user->token = NULL;
				$user->lastLogin = $lastLogin;
				$this->Session->add('user', $user);
				$this->Session->setFlash("Vous êtes maintenant connecté.", 'success');
				
				$this->redirect('/');
			}else{
				$this->Session->setFlash('Il y a un problème lors de l\'activation.', 'error');
				$this->redirect('/');
			}
		}else{
			$this->redirect('/');
		}
		die();
	}

	// Page pour connecter l'utilisateur
	function login(){
		// Permet de sauvergader le referer dans la session si il est différent
		if($this->request->referer){

			if(!$this->Session->isLogged()){
				// SI il est pas connecté, on enregistre la redirection
				$this->Session->add('referer', $this->request->referer);
			}else{
				// Si il est déjà connecté on renvoie l'url avec le token
				if($this->Session->user('token')){
					header('Location: '.$this->request->referer.'?scode='.$this->Session->user('token'));
					die();
				}else{
					$this->redirect('/');
				}
			}
		}		

		$this->redIfLog();

		// On envoie un requete 
		if($this->request->data){
			$this->loadModel('User');
			$data = $this->request->data;
			if($this->User->validates($data)){
				$data->password = cryptPassword($data->password);
				// Si lastLogin == NULL, compte pas activer
				$user = $this->User->findFirst(array(
					'conditions' => array(
						'email' => $data->email,
						'password' => $data->password,
						'lastLogin' => "notnull"
					)
				));

				if($user){
					unset($user->password);
					// Last login
					$lastLogin =  dateNow();
					$token = getToken($user);

					// On met à jour quelques infos de l'utilisateur
					$this->User->save((object) array(
						'id_user' => $user->id_user,
						'lastLogin' => $lastLogin,
						'token' => $token,
						'phpsessid' => session_id()
					));

					// On enregistre l'utilisateur dans la session
					$user->token = $token;
					$this->Session->add('user', $user);
					$this->Session->setFlash("Vous êtes maintenant connecté.", 'success');

					// Permet de récupérer les infos de la chaine de l'utilisateur
					if($user->yt_verified == 1){
						$userChannel = $this->User->findFirst(array(
							'fields' => array('channels.id_channel id', 'channels.slug', 'channels.basePage'),
							'conditions' => array(
								'User.id_user' => $user->id_user
							), 
							'join' => array('channels' => 'User.id_user = channels.id_user')
						));
						$user->channel = $userChannel;
					}

					// REDIRECTION
					// Redirection vers le domaine référant avec le code
					
					if($this->Session->read('referer')){
						header('Location: '.$this->Session->read('referer').'?scode='.$token);
					}elseif($user->yt_verified != 1){
						header("Location: http://".HOST_NAME);
					}else{
						header("Location: http://".$user->channel->slug.'.'.HOST_NAME.'/'.$user->channel->basePage);
					}
				}else{
					$this->Session->setFlash("Nous n'avons pas réussi à vous connecter (vérifier vos emails).", 'error');
				}		
			}
			
			$this->request->data->password = '';
		}
	}

	// Déconnecter l'utilisateur
	function logout(){
		$this->Session->delete('user');
		$this->Session->setFlash('Vous êtes maintenant déconnecté.', 'success');
		if($this->Session->read('referer')){
			$referer = $this->Session->read('referer');
			$this->Session->delete('referer');
			header('Location: '.$referer);
		}else{
			$this->redirect('/');
		}
	}

	// Envoyer un email pour les mots de passes oubliés
	function forget(){
		$this->redIfLog();

		if($this->request->data){


			$this->loadModel('User');
			$user = $this->User->findFirst(array(
				'conditions' => array(
					'email' => $this->request->data->email
				)
			));

			if($user){
				$token = getToken($user);

				// On met à jour quelques infos de l'utilisateur
				$this->User->save((object) array(
					'id_user' => $user->id_user,
					'token' => $token
				));

				//Mail
				$urlReset = Router::url('users/reset').'/'.$token;

				$to      = $user->email;
				$subject = 'Réinitialiser votre mot de passe';
				$message = 'Bonjour '.$user->name.' ! Voici le lien pour modifier votre mot de passe '.$urlReset;
				$headers = 'From: contact@sciens.me' . "\r\n" .
				'Reply-To: contact@sciens.me' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();

				mail($to, $subject, $message, $headers);
				
				$this->Session->setFlash("Vous avez reçu un email avec les instructions afin de réinitialiser votre mot de passe.", 'success');
				$this->redirect('/');

			}else{
				$this->Session->setFlash("L'adresse email est invalide.", 'error');
				$this->redirect('users/forget');
			}
		}
	}

	// Réinitialiser le mot de passe de l'utilisateur
	function reset($token=null){
		$this->redIfLog();

		if($token){
			$this->loadModel('User');
			$user = $this->User->findFirst(array(
				'conditions' => array(
					'token' => $token
				)
			));

			if($user){
				$data = $this->request->data;
				if($data and !empty($data->password) and !empty($data->password2)){
					if($this->User->validates($data)){
						$data->password = cryptPassword($data->password);
						$data->password2 = cryptPassword($data->password2);

						if($data->password === $data->password2){
							// le connecter et ajouter le lastLogin et supprimer le token
							unset($user->password);
							// Last login
							$lastLogin =  dateNow();

							// On met à jour quelques infos de l'utilisateur
							$this->User->save((object) array(
								'id_user' => $user->id_user,
								'lastLogin' => $lastLogin,
								'password' => $data->password,
								'token' => NULL
							));

							// On enregistre l'utilisateur dans la session
							$user->token = NULL;
							$user->lastLogin = $lastLogin;

							$this->Session->add('user', $user);
							$this->Session->setFlash("Votre mot de passe a été modifié avec succès.", 'success');
							
							$this->redirect('/');
						}else{
							$this->Session->setFlash('Les mots de passes sont différents.', 'error');
						}
					}
					$this->request->data->password = NULL;
					$this->request->data->password2 = NULL;
				}
			}else{
				$this->redirect('/');
			}
		}else{
			$this->redirect('/');
		}
	}

	// Editer les informations de l'utilisateur
	function edit(){
		
		if($this->Session->read('user')){
			$this->loadModel('User');
			$user = $this->User->findFirst(array(
				'conditions' => array(
					'id_user' => $this->Session->user('id_user')
				)
			));

			if($user){
				$this->request->data = $this->Session->read('user');
				debug($user);
			}else{
				$this->Session->setFlash('Il y a un problème', 'error');
			}
		}

	}

	/*
	*
	*	SOCIAL CONNECTIONS
	*
	*/



}

?>
