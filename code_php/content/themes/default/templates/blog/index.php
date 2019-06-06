<!DOCTYPE html>
<html>
<head>
	<title><?php echo isset($title_layout) ? $title_layout : 'BLOG'; ?></title>
</head>
<body>
<header><h3><a href=<?php echo Router::url("posts"); ?>>HEADER</a></h3></header>
<?php debug($_SESSION); ?>

<?php if($this->Session->isLogged()): ?>
	<a href="<?php echo Router::url('users/logout', false); ?>">Déconnexion</a>
<?php else: ?>
	<a href="/users/login">Connexion</a>
	<a href="/users/register">Inscription</a>
<?php endif; ?>
<?php echo $this->Session->flash(); ?>

<ul class="nav">
<?php $pagesMenu = $this->request($this->request, 'Pages', 'getMenu'); ?>
<?php foreach ($pagesMenu as $k => $v) : ?>	
	<li><a href=<?php echo Router::url("pages/view/id:{$v->id}/slug:$v->slug"); ?>><?php echo $v->title ?></a></li>
<?php endforeach ?>
</ul>


<div id="content"><?php echo $content_layout; ?></div>

<footer><h3>FOOTER</h3></footer>
</body>
</html>