<?php
//
// Various tweaks to prettify things.
//

//
// Returns additional CSS classes for <body>.
//
function base_body_class() {
	$term = get_queried_object();

	if (is_single()) {
		$cat = get_the_category();
	}

	if (!empty($cat)) {
		return $cat[0]->slug;
	} elseif (isset($term->slug)) {
		return $term->slug;
	} elseif (isset($term->page_name)) {
		return $term->page_name;
	} elseif (isset($term->post_name)) {
		return $term->post_name;
	} else {
		return;
	}
}

//
// We don't need to self-close these tags in html5:
// <img>, <input>
//
function base_remove_self_closing_tags($input) {
	return str_replace(' />', '>', $input);
}
add_filter('get_avatar',          'base_remove_self_closing_tags');
add_filter('comment_id_fields',   'base_remove_self_closing_tags');
add_filter('post_thumbnail_html', 'base_remove_self_closing_tags');

//
// Add a "thumbnail" class to attachment links.
//
function base_attachment_link_class($html) {
	$postid = get_the_ID();
	$html = str_replace('<a', '<a class="thumbnail"', $html);
	return $html;
}
add_filter('wp_get_attachment_link', 'base_attachment_link_class', 10, 1);

//
// Remove CSS from Recent Comments widget.
//
function base_remove_recent_comments_style() {
	global $wp_widget_factory;
	if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
		remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
	}
}
add_action('wp_head', 'base_remove_recent_comments_style', 1);

//
// Remove CSS from gallery.
//
function base_gallery_style($css) {
	return preg_replace("!<style type='text/css'>(.*?)</style>!s", '', $css);
}
add_filter('gallery_style', 'base_gallery_style');

//
// Replace gallery_shortcode().
//
function base_gallery_shortcode($attr) {
	global $post, $wp_locale;

	static $instance = 0;
	$instance++;

	// Allow plugins/themes to override the default gallery template.
	$output = apply_filters('post_gallery', '', $attr);
	if ($output != '') {
		return $output;
	}

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if (isset($attr['orderby'])) {
		$attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
		if (!$attr['orderby']) {
			unset($attr['orderby']);
		}
	}

	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'icontag'    => 'li',
		'captiontag' => 'p',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => ''
	), $attr));

	$id = intval($id);
	if ('RAND' == $order) {
		$orderby = 'none';
	}

	if (!empty($include)) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ($_attachments as $key => $val) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif (!empty($exclude)) {
		$exclude = preg_replace('/[^0-9,]+/', '', $exclude);
		$attachments = get_children(array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
	} else {
		$attachments = get_children(array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
	}

	if (empty($attachments)) {
		return '';
	}

	if (is_feed()) {
		$output = "\n";
		foreach ($attachments as $att_id => $attachment)
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	$captiontag = tag_escape($captiontag);
	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = $gallery_div = '';
	if (apply_filters('use_default_gallery_style', true)) {
		$gallery_style = "";
	}
	$size_class = sanitize_html_class($size);
	$gallery_div = "<ul id='$selector' class='thumbnails gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
	$output = apply_filters('gallery_style', $gallery_style . "\n\t\t" . $gallery_div);

	$i = 0;
	foreach ($attachments as $id => $attachment) {
		$link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

		$output .= "
			<{$icontag} class=\"gallery-item\">
			$link
		";
		if ($captiontag && trim($attachment->post_excerpt)) {
			$output .= "
				<{$captiontag} class=\"gallery-caption hidden\">
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>
			";
		}
		$output .= "</{$icontag}>";
		if ($columns > 0 && ++$i % $columns == 0) {
			$output .= '';
		}
	}

	$output .= "</ul>\n";

	return $output;
}
remove_shortcode('gallery');
add_shortcode('gallery', 'base_gallery_shortcode');

//
// Generic walker for navigation.
//
class Base_Nav_Walker extends Walker_Nav_Menu {
	function check_current($val) {
		return preg_match('/(current-)/', $val);
	}

	function start_el(&$output, $item, $depth, $args) {
		global $wp_query;
		$indent = ($depth) ? str_repeat("\t", $depth) : '';

		$slug = sanitize_title($item->title);
		$id = apply_filters('nav_menu_item_id', 'menu-' . $slug, $item, $args);
		$id = strlen($id) ? '' . esc_attr( $id ) . '' : '';

		$class_names = $value = '';
		$classes = empty($item->classes) ? array() : (array) $item->classes;

		$classes = array_filter($classes, array(&$this, 'check_current'));

		$class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
		$class_names = $class_names ? ' class="' . $id . ' ' . esc_attr($class_names) . '"' : ' class="' . $id . '"';

		$output .= $indent . '<li' . $class_names . '>';

		$attributes  = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
		$attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target    ) .'"' : '';
		$attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn       ) .'"' : '';
		$attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url       ) .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
	}
}

//
// Navigation walker for primary navigation.
//
class Base_Navbar_Nav_Walker extends Walker_Nav_Menu {
	function check_current($val) {
		return preg_match('/(current-)|active|dropdown/', $val);
	}

	function start_lvl(&$output, $depth) {
		$output .= "\n<ul class=\"dropdown-menu\">\n";
	}

	function start_el(&$output, $item, $depth, $args) {
		global $wp_query;
		$indent = ($depth) ? str_repeat("\t", $depth) : '';

		$slug = sanitize_title($item->title);
		$id = apply_filters('nav_menu_item_id', 'menu-' . $slug, $item, $args);
		$id = strlen($id) ? '' . esc_attr( $id ) . '' : '';

		$li_attributes = '';
		$class_names = $value = '';

		$classes = empty($item->classes) ? array() : (array) $item->classes;
		if ($args->has_children) {
			$classes[]      = 'dropdown';
			$li_attributes .= ' data-dropdown="dropdown"';
		}
		$classes[] = ($item->current) ? 'active' : '';
		$classes = array_filter($classes, array(&$this, 'check_current'));

		$class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
		$class_names = $class_names ? ' class="' . $id . ' ' . esc_attr($class_names) . '"' : ' class="' . $id . '"';

		$output .= $indent . '<li' . $class_names . $li_attributes . '>';

		$attributes  = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"'    : '';
		$attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target    ) .'"'    : '';
		$attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn       ) .'"'    : '';
		$attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url       ) .'"'    : '';
		$attributes .= ($args->has_children)      ? ' class="dropdown-toggle" data-toggle="dropdown"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
		$item_output .= ($args->has_children) ? ' <b class="caret"></b>' : '';
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
	}

	function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) {
		if (!$element) { return; }

		$id_field = $this->db_fields['id'];

		// display this element
		if (is_array($args[0])) {
			$args[0]['has_children'] = !empty($children_elements[$element->$id_field]);
		} elseif (is_object($args[0])) {
			$args[0]->has_children = !empty($children_elements[$element->$id_field]);
		}
		$cb_args = array_merge(array(&$output, $element, $depth), $args);
		call_user_func_array(array(&$this, 'start_el'), $cb_args);

		$id = $element->$id_field;

		// descend only when the depth is right and there are childrens for this element
		if (($max_depth == 0 || $max_depth > $depth+1) && isset($children_elements[$id])) {
			foreach ($children_elements[$id] as $child) {
				if (!isset($newlevel)) {
					$newlevel = true;
					// start the child delimiter
					$cb_args = array_merge(array(&$output, $depth), $args);
					call_user_func_array(array(&$this, 'start_lvl'), $cb_args);
				}
				$this->display_element($child, $children_elements, $max_depth, $depth + 1, $args, $output);
			}
			unset($children_elements[$id]);
		}

		if (isset($newlevel) && $newlevel) {
			// end the child delimiter
			$cb_args = array_merge(array(&$output, $depth), $args);
			call_user_func_array(array(&$this, 'end_lvl'), $cb_args);
		}

		// end this element
		$cb_args = array_merge(array(&$output, $element, $depth), $args);
		call_user_func_array(array(&$this, 'end_el'), $cb_args);
	}
}

function base_nav_menu_args($args = '') {
	$args['container']  = false;
	$args['depth']      = 2;
	$args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';
	/* BUG Can't get this to work yet.
	if (!$args['walker']) {
		$args['walker'] = new Base_Nav_Walker();
	}
	*/
	return $args;
}
add_filter('wp_nav_menu_args', 'base_nav_menu_args');

//
// Add "first" and "last" and numbered classes to widgets.
// http://wordpress.org/support/topic/how-to-first-and-last-css-classes-for-sidebar-widgets
//
function base_widget_first_last_classes($params) {
	global $my_widget_num;
	$this_id = $params[0]['id'];
	$arr_registered_widgets = wp_get_sidebars_widgets();

	if (!$my_widget_num) {
		$my_widget_num = array();
	}

	if (!isset($arr_registered_widgets[$this_id]) || !is_array($arr_registered_widgets[$this_id])) {
		return $params;
	}

	if (isset($my_widget_num[$this_id])) {
		$my_widget_num[$this_id] ++;
	} else {
		$my_widget_num[$this_id] = 1;
	}

	$class = 'class="widget-' . $my_widget_num[$this_id] . ' ';

	if ($my_widget_num[$this_id] == 1) {
		$class .= 'widget-first ';
	} elseif ($my_widget_num[$this_id] == count($arr_registered_widgets[$this_id])) {
		$class .= 'widget-last ';
	}

	$params[0]['before_widget'] = preg_replace('/class=\"/', "$class", $params[0]['before_widget'], 1);

	return $params;
}
add_filter('dynamic_sidebar_params', 'base_widget_first_last_classes');
