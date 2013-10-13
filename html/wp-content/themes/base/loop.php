<?php // If there are no posts to display, such as an empty archive page. ?>
<?php if (!have_posts()): ?>
	<div class="alert alert-block fade in">
		<a class="close" data-dismiss="alert">&times;</a>
		<p><?php _e('Sorry, no results were found.', 'base'); ?></p>
	</div>
	<?php get_search_form(); ?>
<?php endif; ?>

<?php // Start loop ?>
<?php while (have_posts()) : the_post(); ?>
	<?php $excerpt = is_home() || is_archive() || is_search(); ?>
	<?php base_post_before(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php base_post_inside_before(); ?>
		<header>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php base_entry_meta(); ?>
		</header>
		<div class="entry-content">
			<?php if ($excerpt) { ?>
				<?php the_excerpt(); ?>
			<?php } else { ?>
				<?php the_content(); ?>
			<?php } ?>
		</div>
		<?php if (!$excerpt): ?>
			<footer>
				<?php $tags = get_the_tags(); if ($tags) { ?><p><?php the_tags(); ?></p><?php } ?>
			</footer>
		<?php endif; ?>
		<?php base_post_inside_after(); ?>
	</article>
	<?php base_post_after(); ?>
<?php endwhile; // end loop ?>

<?php // Display navigation to next/previous pages when applicable. ?>
<?php if ($wp_query->max_num_pages > 1): ?>
	<nav id="post-nav" class="pager">
		<div class="previous"><?php next_posts_link(__('&larr; Older posts', 'base')); ?></div>
		<div class="next"><?php previous_posts_link(__('Newer posts &rarr;', 'base')); ?></div>
	</nav>
<?php endif; ?>