<?php $title_layout = "Éditer votre profile"; ?>

<h2>Éditer votre profile</h2>


<form method="post" action="<?php echo Router::url('users/edit'); ?>">

	<?php echo $this->Form->input('name', 'Prénom'); ?>
	<?php echo $this->Form->input('surname', 'Nom'); ?>
	<?php echo $this->Form->input('gender', 
		array(
			'male'=>'Homme',
			'female'=>'Femme'
		), 
		array('type'=>'radio', 'label' => 'Sexe')); 
	?>

	
	
	<?php echo $this->Form->input('password', 'Mot de passe', array('type'=>'password')); ?>
	<?php echo $this->Form->input('password2', 'Confirmer votre mot de passe', array('type'=>'password')); ?>

	<?php echo $this->Form->input('submit', 'Modifier'); ?>

</form>