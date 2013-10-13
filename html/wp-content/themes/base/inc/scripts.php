<?php
//
// JS modifications for the theme.
//

function base_scripts() {
	
	// jQuery is manually included.
	if (!is_admin()) {
		wp_deregister_script('jquery');
		wp_register_script('jquery', '', '', '', false);
	}

	// Include JS for comment form.
	// see http://codex.wordpress.org/Migrating_Plugins_and_Themes_to_2.7/Enhanced_Comment_Display#Javascript_Comment_Functionality
	if (is_single() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}

	if (is_multisite() || is_child_theme()) {
		$base = get_template_directory_uri();
	} else {
		$base = '';
	}

	// Example
	//wp_register_script('base_script', $base . '/js/script.js', false, null, false);
	//wp_enqueue_script('base_script');
}
add_action('wp_enqueue_scripts', 'base_scripts', 100);
