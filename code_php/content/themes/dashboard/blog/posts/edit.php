<?php if(!empty($this->request->data)): ?>
	<h2>Éditer votre article</h2>
<?php else: ?>
	<h2>Ajouter un article</h2>
<?php endif; ?>

<form method="post" action="<?php echo Router::url('blog/posts/edit/'.$id); ?>">

	<?php echo $this->Form->input('id', 'hidden'); ?>
	<?php echo $this->Form->input('title', 'Titre'); ?>
	<?php echo $this->Form->input('slug', 'URL'); ?>
	<?php echo $this->Form->input('content', 'Contenu', array('type' => 'textarea', 'rows' => 10, 'cols' => 80)); ?>
	<?php echo $this->Form->input('online', 'En ligne', array('type' => 'checkbox')); ?>
	<?php 
		if(!empty($this->request->data)){
			echo $this->Form->input('submit', 'Modifier');
		}else{
			echo $this->Form->input('submit', 'Envoyer');
		} 
	?>

</form>

