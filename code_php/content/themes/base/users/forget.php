<?php $title_layout = 'Mot de passe oublié'; ?>

<h2>Mot de passe oublié</h2>

<form method="post" action="<?php echo Router::url('users/forget'); ?>">
	<?php echo $this->Form->input('email', 'Email'); ?>
	<?php echo $this->Form->input('submit', 'Réinitialiser'); ?>
</form>




