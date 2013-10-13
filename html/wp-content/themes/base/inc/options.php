<?php
//
// Theme options page.
// Some code adapted from Image Widget (http://wordpress.org/extend/plugins/image-widget/).
//

//
// Register the options.
//
function base_theme_options_init() {
	
	// Create the options if they don't exist yet.
	if (base_get_theme_options() === false) {
		add_option('base_theme_options', base_get_default_theme_options());
	}

	// Register a callback sanitization function.
	register_setting(
		'base_options',
		'base_theme_options',
		'base_theme_options_validate'
	);
	
}
add_action('admin_init', 'base_theme_options_init');

//
// Filter the capability for the options page.
//
function base_options_page_capability($capability) {
	return 'edit_theme_options';
}
add_filter('option_page_capability_base_options', 'base_options_page_capability');

//
// Add the Options page to the admin menu.
//
function base_theme_options_add_page() {
	$hook_suffix = add_theme_page(
		__("Customization", 'base'),
		__("Customization", 'base'),
		'edit_theme_options',
		'theme_options',
		'base_theme_options_render_page'
	);
}
add_action('admin_menu', 'base_theme_options_add_page', 50);

//
// Retrieves the theme options.
//
function base_get_theme_options() {
	return get_option('base_theme_options', base_get_default_theme_options());
}

//
// Define the default options.
//
function base_get_default_theme_options() {
	$default_theme_options = array(
		'logo_type'              => "text",
		'show_tagline_in_header' => true,
	);

	return apply_filters('base_default_theme_options', $default_theme_options);
}

//
// Add necessary JS and CSS to Theme Options page.
//
function base_theme_options_scripts() {
	wp_enqueue_script('media-upload'); // for Logo
	wp_enqueue_script('thickbox'); // for Logo

	wp_register_script('base-theme-options', get_template_directory_uri()."/js/theme-options.js", array('jquery','media-upload','thickbox'));
	wp_enqueue_script('base-theme-options');
}
function base_theme_options_styles() {
	wp_enqueue_style('thickbox'); // for Logo
}
if (isset($_GET['page']) && $_GET['page'] == 'theme_options') {
	add_action('admin_print_scripts', 'base_theme_options_scripts');
	add_action('admin_print_styles', 'base_theme_options_styles');
}

//
// Modify the Media thickbox for the Theme Options page.
//
if ($pagenow == "media-upload.php"
or  $pagenow == "async-upload.php") {
	add_filter('gettext', 'base_theme_replace_text_in_thickbox', 1, 3);
}

//
// Somewhat hacky way of replacing "Insert into Post" with "Use for Logo".
//
function base_theme_replace_text_in_thickbox($translatedText, $sourceText, $domain) {
	if (is_base_theme_options_context() ) {
		if ($sourceText == "Insert into Post") {
			return __("Use for Logo", 'base');
		}
	}
	return $translatedText;
}

//
// Test to see if the Media uploader is being used for the Theme Options page
//   or for other regular uploads.
//
function is_base_theme_options_context() {
	if (strpos(@$_SERVER['HTTP_REFERER'], 'theme_options') !== false ) {
		return true;
	} elseif (strpos(@$_REQUEST['_wp_http_referer'], 'theme_options') !== false ) {
		return true;
	} elseif (isset($_REQUEST['theme_options'])) {
		return true;
	}
	return false;
}

//
// Prints out the Options page.
//
function base_theme_options_render_page() { ?>

	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php printf(__("%s Customization", 'base'), get_current_theme()); ?></h2>
		<?php settings_errors(); ?>

		<form method="post" action="options.php">
			<?php
				settings_fields('base_options');
				$base_options = base_get_theme_options();
				$base_default_options = base_get_default_theme_options();
			?>
			
			<table class="form-table">

				<?php
					// Use of Media uploader from:
				    //   http://www.webmaster-source.com/2010/01/08/using-the-wordpress-uploader-in-your-plugin-or-theme/
					//   http://wordpress.org/extend/plugins/image-widget/
				?>
				<tr valign="top">
					<th scope="row"><?php _e("Logo", 'base'); ?></th>
					<td>
						<fieldset><legend class="screen-reader-text"><span><?php _e("Logo", 'base'); ?></span></legend>
							<select name="base_theme_options[logo_type]" id="logo_type">
								<option value="text"  <?php if ($base_options['logo_type'] == "text")  echo "selected='selected'"; ?>><?php echo _e("Use company name", 'base'); ?></option>
								<option value="image" <?php if ($base_options['logo_type'] == "image") echo "selected='selected'"; ?>><?php echo _e("Use image", 'base'); ?></option>
							</select>
							<input name="base_theme_options[logo_image]" type="hidden" value="" />
							<input id="logo_image_button" type="button" value="<?php _e("Choose Image...", 'base'); ?>" />
							<br/><small class="description"><?php printf(__("Show your company&#8217;s name as text, or upload a logo.", 'base')); ?></small>
							<div id="logo_preview">
								<img src="<?php echo (@$base_options['logo_image']); ?>" alt="" style="max-width: 100%;" />
							</div>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e("Show tagline in header?", 'base'); ?></th>
					<td>
						<fieldset><legend class="screen-reader-text"><span><?php _e("Show tagline in header?", 'base'); ?></span></legend>
							<select name="base_theme_options[show_tagline_in_header]" id="show_tagline_in_header">
								<option value="yes" <?php if ($base_options['show_tagline_in_header']) echo "selected='selected'"; ?>><?php echo _e('Yes', 'base'); ?></option>
								<option value="no"  <?php if (!$base_options['show_tagline_in_header']) echo "selected='selected'"; ?>><?php echo _e('No', 'base'); ?></option>
							</select>
							<br /><small class="description"><?php printf(__("Displays your company&#8217;s tagline with your logo.", 'base')); ?></small>
						</fieldset>
					</td>
				</tr>

			</table>
			
			<?php submit_button(); ?>
		</form>
	</div>

<?php }

//
// Sanitizer function for options.
//
function base_theme_options_validate($input) {
	$output = $defaults = base_get_default_theme_options();

	if (isset($input['show_tagline_in_header'])) {
		if ($input['show_tagline_in_header'] === 'yes') {
			$input['show_tagline_in_header'] = true;
		}
		if ($input['show_tagline_in_header'] === 'no') {
			$input['show_tagline_in_header'] = false;
		}
		$output['show_tagline_in_header'] = $input['show_tagline_in_header'];
	}

	if (isset($input['logo_type'])) {
		$output['logo_type'] = ($input['logo_type'] == "image" ? "image" : "text");
	}

	if (isset($input['logo_image'])) {
		$output['logo_image'] = $input['logo_image'];
	}

	return apply_filters('base_theme_options_validate', $output, $input, $defaults);
}
