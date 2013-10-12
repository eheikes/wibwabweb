<?php
	//
	// This file defines shortcodes available to ALL sites.
	//
	// Copyright (c) 2009-2011 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//
	//
	
	namespace ArrowQuick\Shortcodes;
	
	// Embed the search form (searchform.php).
	function SiteSearchShortcode($atts)
	{
		ob_start();
		get_search_form();
		$output = ob_get_clean();
		return $output;
	}
	add_shortcode('site-search', __NAMESPACE__.'\\SiteSearchShortcode');

?>