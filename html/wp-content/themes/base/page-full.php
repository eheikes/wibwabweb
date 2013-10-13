<?php
/*
Template Name: Full Width
*/
get_header(); ?>
	<?php base_content_before(); ?>
	<div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
		<?php base_main_before(); ?>
		<div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
			<?php base_loop_before(); ?>
			<?php get_template_part('loop', 'page'); ?>
			<?php base_loop_after(); ?>
		</div><!-- /#main -->
		<?php base_main_after(); ?>
	</div><!-- /#content -->
	<?php base_content_after(); ?>
<?php get_footer(); ?>