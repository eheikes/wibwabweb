<?php while (have_posts()) : the_post(); ?>
	<?php base_post_before(); ?>
	<article <?php post_class() ?> id="post-<?php the_ID(); ?>">
		<?php base_post_inside_before(); ?>
		<header>
			<h1><?php the_title(); ?></h1>
		</header>
		<?php the_content(); ?>
		<?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
		<?php base_post_inside_after(); ?>
		<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
	</article>
	<?php base_post_after(); ?>
<?php endwhile; ?>