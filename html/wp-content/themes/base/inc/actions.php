<?php
//
// Default actions for framework hooks.
//

function base_print_stylesheets() {
	echo "<link rel='stylesheet' href='" . get_template_directory_uri() . "/css/h5bp.css?v=20120315'>\n";
	echo "<link rel='stylesheet' href='" . get_template_directory_uri() . "/css/less.css?v=20120315'>\n";
	echo "<link rel='stylesheet' href='" . get_template_directory_uri() . "/css/base.css?v=20120315'>\n";
	if (is_child_theme()) {
		echo "<link rel='stylesheet' href='" . get_stylesheet_uri() . "'>\n";
	}
}
add_action('base_stylesheets', 'base_print_stylesheets');
