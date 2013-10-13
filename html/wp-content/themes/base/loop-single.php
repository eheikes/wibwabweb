<?php
	// based on the hNews microformat as recommended by http://www.readability.com/publishers/guidelines/
	while (have_posts()) : the_post();
?>
	<?php base_post_before(); ?>
	<article <?php post_class() ?> id="post-<?php the_ID(); ?>">
		<?php base_post_inside_before(); ?>
		<header>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php base_entry_meta(); ?>
		</header>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
		<footer>
			<?php wp_link_pages(array('before' => '<nav id="page-nav"><p>' . __('Pages:', 'base'), 'after' => '</p></nav>')); ?>
			<?php $tags = get_the_tags(); if ($tags) { ?><p><?php the_tags(); ?></p><?php } ?>
		</footer>
		<?php comments_template(); ?>
		<?php base_post_inside_after(); ?>
	</article>
	<?php base_post_after(); ?>
<?php endwhile; ?>