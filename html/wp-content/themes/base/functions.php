<?php
//
// General configuration.
//
define('WRAP_CLASSES',         'container');
define('CONTAINER_CLASSES',    'clearfix');
define('MAIN_CLASSES',         '');
define('SIDEBAR_CLASSES',      '');
define('FULLWIDTH_CLASSES',    '');

//
// Load library files.
//
require_once locate_template('/inc/actions.php');
require_once locate_template('/inc/cleanup.php');
require_once locate_template('/inc/hooks.php');
require_once locate_template('/inc/options.php');
require_once locate_template('/inc/scripts.php');
require_once locate_template('/inc/widgets.php');

//
// Send extra HTTP headers.
//
function base_send_headers() {
	if (headers_sent()) return;
	
	// Make sure IE uses the latest rendering engine.
	// Should only be sent for webpages (not images, etc.).
	// http://html5boilerplate.com/docs/html/#make-sure-the-latest-version-of-ie-is-used
	header("X-UA-Compatible: IE=edge,chrome=1");
}
add_action('send_headers', 'base_send_headers');

//
// General theme setup.
//
function base_setup() {

	// Set the maximum 'Large' image width to the maximum grid width.
	// http://wordpress.stackexchange.com/q/11766
	global $content_width;
	if (!isset($content_width)) $content_width = 896;

	// http://codex.wordpress.org/Post_Thumbnails
	add_theme_support('post-thumbnails', array('post'));
	set_post_thumbnail_size(150, 150, false);

	// http://codex.wordpress.org/Post_Formats
	// add_theme_support('post-formats', array('aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat'));

	// http://codex.wordpress.org/Function_Reference/register_nav_menus
	register_nav_menus(array(
		'primary_navigation' => __('Primary Navigation', 'base')
	));
}
add_action('after_setup_theme', 'base_setup');

//
// Register the sidebars.
//
function base_register_sidebars() {
	$sidebars = array('Sidebar', 'Footer');

	foreach($sidebars as $sidebar) {
		register_sidebar(
			array(
				'id'            => 'base-' . sanitize_title($sidebar),
				'name'          => __($sidebar, 'base'),
				'description'   => __($sidebar, 'base'),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>'
			)
		);
	}
}
add_action('widgets_init', 'base_register_sidebars');

//
// Returns post entry meta information.
//
function base_entry_meta() {
	$html = "";
	$html .= '<time class="updated" datetime="' . get_the_time('c') . '" pubdate>' . sprintf(__('Posted on %s', 'base'), get_the_date()) . '</time>';
	//$html .= '<p class="byline author vcard">' . __('Written by', 'base') . ' <a href="'. get_author_posts_url(get_the_author_meta('id')) . '" rel="author" class="fn">' . get_the_author() . '</a></p>';
	echo apply_filters('base_entry_meta', $html);
}

//
// Replace "[...]" excerpt truncation with simply "...".
//
function base_excerpt_more($more) {
	return "...";
}
add_filter('excerpt_more', 'base_excerpt_more');

//
// Add a menu item's name to its CSS classes.
//
function base_nav_menu_css($classes = array(), $item) {
	
	$name = @$item->title;
	$name = strtolower($name); // convert to lowercase
	$name = preg_replace("#\W+#", "-", $name); // replace non-alphanumerics with a hyphen
	$name = trim($name, "-"); // remove any leading/trailing hyphens
	if ($name != "") {
		$classes[] = $name;
	}

	return $classes;
}
add_filter('nav_menu_css_class', 'base_nav_menu_css', 10, 2);
