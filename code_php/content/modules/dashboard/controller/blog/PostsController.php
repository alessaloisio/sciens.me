<?php 
class PostsController extends Controller{

	function index(){
		$this->loadModel('Post');

		$posts = $this->Post->find(array(
			'conditions' => array(
				'channel_id' => $this->request->channel->id,
				'type' => 'post'
			)
		));

		$this->set('posts', $posts);
	}

	function edit($id = null){
		//Gérer les erreurs + le formulaire.
		$this->loadModel('Post');
		
		$d['id'] = '';

		if($this->request->data){
			if($this->Post->validates($this->request->data)){
				$this->request->data->type = 'post';
				$this->request->data->channel_id = $this->request->channel->id;
				$this->request->data->created = dateNow();
				// user_id

				$this->Post->save($this->request->data);
				$id = $this->Post->id;
			}else{
				$this->Session->setFlash('Merci de corriger vos informations.', 'error');
			}
		}

		if($id){
			$this->request->data = $this->Post->findFirst(array(
				'conditions' => array(
					'id' => $id,
					'channel_id' => $this->request->channel->id,
					'type' => 'post'
				)
			));
			$d['id'] = $id;
		}

		$this->set($d);
	}

	function delete($id){
		$this->loadModel('Post');
		// Vérification que l'id est bien à l'utilisateur....
		$this->Post->delete($id);
		$this->Session->setFlash('Votre article a été supprimer.', 'success');
		$this->redirect('blog/posts');
	}

}
?>