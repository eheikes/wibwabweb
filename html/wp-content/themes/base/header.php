<?php
	$theme_options = base_get_theme_options();
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta charset="utf-8">

	<title><?php wp_title('&laquo;', true, 'right'); bloginfo('name'); ?></title>

	<meta name="viewport" content="width=device-width">

	<?php base_stylesheets(); ?>

	<script src="<?php echo get_template_directory_uri(); ?>/js/modernizr-2.5.3.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="<?php echo get_template_directory_uri(); ?>/js/jquery-1.7.1.min.js"><\/script>')</script>

	<?php wp_head(); ?>
</head>

<body <?php body_class(base_body_class()); ?>>

	<?php base_header_before(); ?>
	<header id="banner" class="clearfix" role="banner">
		<?php base_header_inside(); ?>
		<div class="brand">
			<a href="<?php echo home_url(); ?>/"><?php
				if (@$theme_options['logo_type'] == "image") {
					echo "<img src='" . @$theme_options['logo_image'] . "' alt='" . get_bloginfo('name') . "'>";
				} else {
					bloginfo('name');
				}
			?></a>
			<?php if (@$theme_options['show_tagline_in_header']): ?>
				<div class="tagline"><?php bloginfo('description'); ?></div>
			<?php endif; ?>
		</div>
		<nav id="nav-main" role="navigation">
			<?php wp_nav_menu(array('theme_location' => 'primary_navigation', 'container' => false)); ?>
		</nav>
		<?php base_header_inside_after(); ?>
	</header>
	<?php base_header_after(); ?>

	<?php base_wrap_before(); ?>
	<div id="doc" class="<?php echo WRAP_CLASSES; ?> clearfix" role="document">