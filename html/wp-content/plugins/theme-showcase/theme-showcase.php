<?php
/*
Plugin Name: Theme Showcase
Plugin URI: http://wibwabweb.com
Description: Display themes located in wp-content/themes in a showcase gallery with theme screenshots and preview links.  Enter <strong>[showcase]</strong> in a post or page to display your theme screenshots.
Version: 1.0
Author: ArrowQuickSolutions
Author URI: http://arrowquick.com

Based on the poorly-written and out-of-date WordPress Theme Showcase Plugin
Original Author: Brad Williams
Original Author URI: http://webdevstudios.com/support/wordpress-plugins/
*/

/*

Example WordPress Theme Preview URI:
http://blog.wp/index.php?preview_theme=WordPress%20Default

*/

// Define current version
define( 'TS_VERSION', '1.7' );

// Set this to the user level required to preview themes.
$preview_theme_user_level = 0;
// Set this to the name of the GET variable you want to use.
$preview_theme_query_arg = 'preview_theme';

// Hook for adding admin menus
add_action('admin_menu', 'ts_menu');

function preview_theme_stylesheet($stylesheet)
{
    global $user_level, $preview_theme_user_level, $preview_theme_query_arg;

    get_currentuserinfo();

    if ($user_level  < $preview_theme_user_level)
    {
        return $stylesheet;
    }

	if (isset($_GET[$preview_theme_query_arg]))
	{
	    $theme = $_GET[$preview_theme_query_arg];
	}

    if (empty($theme))
    {
        return $stylesheet;
    }

    $theme = get_theme($theme);

    if (empty($theme))
    {
        return $stylesheet;
    }

    return $theme['Stylesheet'];
}

function preview_theme_template($template)
{
    global $user_level, $preview_theme_user_level, $preview_theme_query_arg;

    get_currentuserinfo();

    if ($user_level  < $preview_theme_user_level)
    {
        return $template;
    }

	if (isset($_GET[$preview_theme_query_arg]))
	{
	    $theme = $_GET[$preview_theme_query_arg];
	}

    if (empty($theme))
    {
        return $template;
    }

    $theme = get_theme($theme);

    if (empty($theme))
    {
        return $template;
    }

    return $theme['Template'];
}

// Main function to display the themes.
// Accepted parameters:
//   (blank) -- show non-legacy themes
//   all -- show all (selected) themes
//   legacy -- only show legacy themes
function ts_thumbnails($atts, $content = null, $code = "")
{
	$atts = (array)$atts;

	if (!is_admin())
	{
		// Get themes
		$themes = get_themes();
		$allowed_themes = get_site_option('allowedthemes');

		// Get options
		$options      = get_option('ts_themes');
		$disp_preview = get_option('ts_disp_preview');
		$disp_desc    = get_option('ts_disp_desc');
		$form = "<div class='theme-showcase'>\n";
	
		if (1 < count($themes))
		{
			$theme_names = array_keys($themes);
			natcasesort($theme_names);
			
			$counter = 0;
	
			foreach ($theme_names as $theme_name)
			{
				$template = $themes[$theme_name]['Template'];
				$stylesheet = $themes[$theme_name]['Stylesheet'];
				$title = $themes[$theme_name]['Title'];
				$version = $themes[$theme_name]['Version'];
				$description = $themes[$theme_name]['Description'];
				$author = $themes[$theme_name]['Author'];
				$screenshot = $themes[$theme_name]['Screenshot'];
				$stylesheet_dir = $themes[$theme_name]['Stylesheet Dir'];
				$template_dir = $themes[$theme_name]['Template Dir'];
				$tags = $themes[$theme_name]['Tags'];

				// Check if the theme is available network-wide.
				if (!@$allowed_themes[$stylesheet])
				{
					continue;
				}
				
				// Check if the theme should be displayed.
				if (in_array("all", $atts))
				{
					// display all themes
				}
				elseif (in_array("legacy", $atts)) // legacy themes only
				{
					if (!in_array("legacy", $tags)) continue;
				}
				else // no legacy themes
				{
					if (in_array("legacy", $tags)) continue;
				}

				$screenshot_url = $themes[$theme_name]['Theme Root URI'] .'/' .$stylesheet .'/' .$screenshot;

				if (!is_array($options) // no options set yet
				or  in_array($theme_name, $options)) // theme was selected
				{
					if ($screenshot)
					{
						$form .= "<div class='cell'>\n";
						
						// Check if preview is displayed
						if ($disp_preview)
						{
							$form .= "<h3><a href='" . get_bloginfo('url') . "/?preview_theme=" . $theme_name . "' target='_blank'>" .  esc_html($title) . "</a></h3>\n";
							$form .= "<a href='" . get_bloginfo('url') . "/?preview_theme=" . $theme_name . "' target='_blank'><img src= '" . $screenshot_url . "' alt='" . esc_attr($title . " screenshot") . "' /></a>\n";
							$form .= "<p><a href='" . get_bloginfo('url') . "/?preview_theme=" . $theme_name . "' target='_blank'>Preview Theme</a></p>\n";
						}
						else
						{
							$form .= "<h3>" .  esc_html($title) . "</h3>\n";
							$form .= "<img src= '" . $screenshot_url . "' alt='" . esc_attr($title . " screenshot") . "' />\n";
						}
						
						// Check if description is displayed.
						if ($disp_desc)
						{
							$form .= "<p>" . esc_html($description) . "</p>\n";
						}
						
						$form .= "</div>\n";
						
						$counter++;
					}
				}
			}
		}
		
		if ($counter == 0)
		{
			$form .= "<p>No designs available.</p>\n";
		}
		
		$form .= "</div>\n"; // end div.theme-showcase
		
		return $form;		
	}
}

function ts_menu()
{
	add_options_page('Theme Showcase Options', 'Theme Showcase', 'update_plugins', __FILE__, 'ts_options');
}

// Save the plugin settings.
function update_options()
{
	check_admin_referer('ts_check');
	
	if (isset($_POST['disp_preview']))
	{
		$disp_preview = $_POST['disp_preview'];
	}
	else
	{
		$disp_preview = '';
	}
	
	if (isset($_POST['disp_desc']))
	{
		$disp_desc = $_POST['disp_desc'];
	}
	else
	{
		$disp_desc = '';
	}
	
	update_option('ts_disp_preview', $disp_preview);
	update_option('ts_disp_desc', $disp_desc);
}

// Display options page.
function ts_options()
{
	if (isset($_POST['update_ts_options'])
	and $_POST['update_ts_options'])
	{
		update_options();

		echo "<div class=\"updated\">\n"
			. "<p>"
				. "<strong>"
				. __('Settings saved.')
				. "</strong>"
			. "</p>\n"
			. "</div>\n";
	}
	
	echo "<div class='wrap'>\n";
	echo "<h2>Theme Showcase Options</h2>\n";
	
	// Load options
	$disp_theme  = get_option('ts_disp_preview');
	$disp_desc   = get_option('ts_disp_desc');
	
	echo '<div class="theme-showcase">';
	echo '<form method="post">';
	if (function_exists('wp_nonce_field')) wp_nonce_field('ts_check');
	echo '<input type="hidden" name="update_ts_options" value="1">';
		
	if ($disp_theme)
	{
		$disp_preview = "CHECKED";	
	}
	else
	{
		$disp_preview = "";	
	}

	if ($disp_desc)
	{
		$disp_desc = "CHECKED";	
	}
	else
	{
		$disp_desc = "";	
	}
				
	echo '<p><input type="checkbox" name="disp_preview" '.esc_attr($disp_preview).'>&nbsp;Show Preview Link?';
	echo '<p><input type="checkbox" name="disp_desc" '.esc_attr($disp_desc).'>&nbsp;Show Description?';
	echo '<p class="submit">'
	   . '<input type="submit"'
	   . ' value="' . esc_attr(__('Save Changes')) . '"'
	   . ' /></p>';
	echo '</form>';

	echo "</div>\n";	// end div.theme-showcase
	echo "</div>\n";	// end div.wrap
}

add_filter('stylesheet', 'preview_theme_stylesheet');
add_filter('template', 'preview_theme_template');
add_shortcode('showcase', 'ts_thumbnails');

register_activation_hook(__FILE__, 'ts_install');

function ts_install()
{
	//set default options when installed
	update_option('ts_disp_preview', 'on');
	update_option('ts_disp_desc', 'on');
}
?>