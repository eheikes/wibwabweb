<?php

/**
 * @author Deanna Schneider
 * @copyright 2008
 * @description Use WordPress Shortcode API for more features
 * @Docs http://codex.wordpress.org/Shortcode_API
 */

class cets_EmbedGmaps_shortcodes {
	
	var $count = 1;
	
	// register the new shortcodes
	function cets_EmbedGmaps_shortcodes() {
	
		add_shortcode( 'cetsEmbedGmap', array(&$this, 'show_Gmap') );
			
	}

	
	function show_Gmap( $atts ) {
	
		global $cets_EmbedGmaps;
	
		extract(shortcode_atts(array(
			'src' 		=> get_option('cets_embedGmaps_src','http://maps.google.com/?ie=UTF8&ll=37.0625,-95.677068&spn=55.586984,107.138672&t=h&z=4'),
			'height' => get_option('cets_embedGmaps_height', 425),
			'width' => get_option('cets_embedGmaps_width',350),
			'frameborder' => get_option('cets_embedGmaps_frameborder',0),
			'marginheight' => get_option('cets_embedGmaps_marginheight',0),
			'marginwidth' => get_option('cets_embedGmaps_marginwidth',0),
			'scrolling' => get_option('cets_embedGmaps_scrolling','no')
		), $atts ));
		
		// clean up the url
		$src = str_replace("'", "\\'", clean_url($src));
		
		
		
		//if it's not a link to maps.google.com, don't allow it
                //AQ lukef.  #1250.  in PHP4, substr_count can only have 2 parameters.  Removed the offset.
                if (phpversion() >= '5.1.0') {
                    if (substr_count($src, 'http://maps.google.com', 0) == 0) return;
                } else {
                    if (substr_count($src, 'http://maps.google.com') == 0) return;
                }
		
		
		// makes sure all the other attributes are valid
		if (!is_numeric($height)) $height = get_option('cets_embedGmaps_height', 425);
		if (!is_numeric($width)) $width = get_option('cets_embedGmaps_width',350);
		if (!is_numeric($frameborder)) $frameborder = get_option('cets_embedGmaps_frameborder',0);
		if (!is_numeric($marginheight)) $marginheight = get_option('cets_embedGmaps_marginheight',0);
		if (!is_numeric($marginwidth)) $marginwidth = get_option('cets_embedGmaps_marginwidth',0);
		if ($scrolling != 'auto' && $scrolling != 'yes') $scrolling = get_option('cets_embedGmaps_scrolling','no');
		
		// take the link and make the iframe embed stuff.
		$return = '<iframe width="' . $width . '" height="' . $height . '" frameborder="' . $frameborder . '" scrolling="' . $scrolling . '" marginheight="' . $marginheight . '" marginwidth="' . $marginwidth . '" src="' . $src . '&amp;output=embed"></iframe><br /><small><a href="' . $src . '&amp;source=embed" target="_new" style="color:#0000FF;text-align:left">View larger map</a> </small>';
		
		return $return;
		
	}

	
}

// let's use it
$cets_EmbedGmapsShortcodes = new cets_EmbedGmaps_Shortcodes;	

?>