<?php $title_layout = 'Page d\'inscription'; ?>

<h2></h2>

<form method="post" action="<?php echo Router::url('users/register'); ?>">
	<?php echo $this->Form->input('email', 'Email'); ?>
	<?php echo $this->Form->input('name', 'Prénom'); ?>
	<?php echo $this->Form->input('surname', 'Nom'); ?>
	<?php echo $this->Form->input('password', 'Mot de passe', array('type' => 'password')); ?>
	<?php echo $this->Form->input('password2', 'Confirmer le mot de passe', array('type' => 'password')); ?>
	<?php echo $this->Form->input('submit', "S'enregistrer"); ?>
</form>




