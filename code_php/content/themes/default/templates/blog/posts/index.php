
<h1>BLOG</h1>

<?php foreach ($posts as $k => $v): ?>
	<div class="post">
		<h2><?php echo $v->title; ?></h2>
		<p><?php echo $v->content; ?></p>
		<a href=<?php echo Router::url("posts/view/id:{$v->id}/slug:$v->slug"); ?>>Lire la suite &rarr;</a>
	</div>
<?php endforeach ?>


<!-- PAGINATION -->

<?php if($page > 1): ?>
	<div class="pagination">
		<ul>
			<?php for ($i=1; $i <= $page; $i++): ?>
			<li <?php if($i == $this->request->page) echo 'class=active'; ?>>
				<?php if($i == $this->request->page): ?>
					<?php echo $i; ?>
				<?php else: ?>
					<a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
				<?php endif; ?>
			</li>
			<?php endfor; ?>
		</ul>
	</div>
<?php endif; ?>

