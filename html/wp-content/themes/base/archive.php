<?php get_header(); ?>
	<?php base_content_before(); ?>
	<div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
		<?php base_main_before(); ?>
		<div id="main" class="<?php echo MAIN_CLASSES; ?>" role="main">
			<div class="page-header">
				<h1><?php
					$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
					if ($term) {
						echo $term->name;
					} elseif (is_day()) {
						printf(__('Daily Archives: %s', 'base'), get_the_date());
					} elseif (is_month()) {
						printf(__('Monthly Archives: %s', 'base'), get_the_date('F Y'));
					} elseif (is_year()) {
						printf(__('Yearly Archives: %s', 'base'), get_the_date('Y'));
					} elseif (is_author()) {
						global $post;
						$author_id = $post->post_author;
						printf(__('Author Archives: %s', 'base'), get_the_author_meta('user_nicename', $author_id));
					} else {
						single_cat_title();
					}
				?></h1>
			</div>
			<?php base_loop_before(); ?>
			<?php get_template_part('loop', 'category'); ?>
			<?php base_loop_after(); ?>
		</div><!-- /#main -->
		<?php base_main_after(); ?>
		<?php base_sidebar_before(); ?>
		<aside id="sidebar" class="<?php echo SIDEBAR_CLASSES; ?>" role="complementary">
			<?php base_sidebar_inside_before(); ?>
			<?php get_sidebar(); ?>
			<?php base_sidebar_inside_after(); ?>
		</aside><!-- /#sidebar -->
		<?php base_sidebar_after(); ?>
	</div><!-- /#content -->
	<?php base_content_after(); ?>
<?php get_footer(); ?>