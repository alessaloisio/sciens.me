<!DOCTYPE html>
<html>
<head>
	<title><?php echo isset($title_layout) ? $title_layout : 'DASHBOARD'; ?></title>
</head>
<body>
<header><h3><a href=<?php echo Router::url('/'); ?>>HEADER</a></h3></header>

<?php echo $this->Session->flash(); ?>

<div id="content"><?php echo $content_layout; ?></div>

<footer><h3>FOOTER</h3></footer>
</body>
</html>