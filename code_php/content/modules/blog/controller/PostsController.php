<?php 

class PostsController extends Controller{

	function index(){

		$perPage = 5;
		$this->loadModel('Post');
		$condition = array('online' => 1,'type' => 'post', 'channel_id' => $this->request->channel->id);
		$d['posts'] = $this->Post->find(array(
			'conditions' => $condition,
			'order' => 'created DESC',
			'limit' => ($perPage*($this->request->page-1)).','.$perPage
		));
		$d['page'] = ceil($this->Post->findCount($condition) / $perPage);

		// Si on trouve pas le post
		if(empty($d['posts'])){
			$this->e404('Aucun article trouvé');
		}
		$this->set($d);
	}

	function view($id, $slug){
		if(empty($id)){
			$this->e404("Aucun slug trouvé");
		}
		$this->loadModel('Post');
		$post = $this->Post->findFirst(array(
			'conditions' => array(
				'id' => $id,
				'online' => 1,
				'type' => 'post',
				'channel_id' => $this->request->channel->id
			)
		));
		// Si on trouve pas le post
		if(empty($post)){
			$this->e404('Aucun article trouvé');
		}
		// Si slug pas bon, on redirige
		if($slug != $post->slug){
			$this->Session->setFlash('Vous êtes maintenant redirigé.', 'success');
			$this->redirect("posts/view/id:{$id}/slug:$post->slug", 301);
		}

		$this->set('post', $post);
	}
	
}

?>