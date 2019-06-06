<h2>Vous avez <?php echo count($posts); ?> article(s).</h2>

<table>
   <tr>
       <th>ID</th>
       <th>En ligne ?</th>
       <th>Titre</th>
       <th>OPTIONS</th>
   </tr>

	<?php foreach ($posts as $k => $v): ?>
		<tr>
			<td><?php echo $v->id; ?></td>
			<td>
				<?php if($v->online == 1): ?>
					<?php echo "Publier"; ?>
				<?php else: ?>
					<?php echo "Brouillon"; ?>
				<?php endif; ?>
			</td>
			<td><?php echo $v->title; ?></td>
			<td>
				<a href="<?php echo Router::url('blog/posts/edit/'.$v->id); ?>">Editer</a>
				<a onclick="return confirm('Etes vous sur de vouloir supprimer cet article')" href="<?php echo Router::url('blog/posts/delete/'.$v->id); ?>">Supprimer</a>
			</td>
		</tr>
	<?php endforeach ?>
</table>

<a href="<?php echo Router::url('blog/posts/edit/'); ?>">Ajouter un article</a>



