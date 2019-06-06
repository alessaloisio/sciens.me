<?php 

class PagesController extends Controller{

	function view($id, $slug){
		if(empty($id)){
			$this->e404("Aucun slug trouvé");
		}
		$this->loadModel('Post');
		$page = $this->Post->findFirst(array(
			'conditions' => array(
				'id' => $id,
				'online' => 1,
				'type' => 'page',
				'channel_id' => $this->request->channel->id
			)
		));
		// Si on trouve pas le page
		if(empty($page)){
			$this->e404('Aucune page trouvée');
		}

		// Si slug pas bon, on redirige
		if($slug != $page->slug){
			$this->redirect("pages/view/id:{$id}/slug:$page->slug", 301);
		}

		$this->set('page', $page);
	}

	function getMenu(){
		$this->loadModel('Post');
		return $this->Post->find(array(
			'conditions' => array(
				'online' => 1,
				'type' => 'page',
				'channel_id' => $this->request->channel->id
			)
		));
	}
	
}

?>