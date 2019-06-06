<?php $title_layout = 'Page de connexion'; ?>

<h2></h2>

<form method="post" action="<?php echo Router::url('users/login'); ?>">
	<?php echo $this->Form->input('email', 'Email'); ?>
	<?php echo $this->Form->input('password', 'Mot de passe', array('type' => 'password')); ?>
	<?php echo $this->Form->input('submit', 'Se connecter'); ?>
</form>

<div class="social">
	<a href="">Se connecter avec Google</a>
	<a href="">Se connecter avec Facebook</a>
	<a href="">Se connecter avec Twitter</a>
</div>

<a href="<?php echo Router::url('users/register'); ?>">Créer un compte</a>
<a href="<?php echo Router::url('users/forget'); ?>">Mot de passe oublié ?</a>



