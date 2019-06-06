<?php $title_layout = 'Réinitialiser votre mot de passe'; ?>

<h2>Réinitialiser votre mot de passe</h2>

<form method="post" action="">
	<?php echo $this->Form->input('password', 'Mot de passe', array('type' => 'password')); ?>
	<?php echo $this->Form->input('password2', 'Confirmer le mot de passe', array('type' => 'password')); ?>
	<?php echo $this->Form->input('submit', 'Modifier'); ?>
</form>