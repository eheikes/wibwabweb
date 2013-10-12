<?php
	//
	// This file modifies the kses filter settings
	//   * see wp-includes/kses.php
	//
	// Copyright (c) 2010-2012 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//
	//

	namespace ArrowQuick;
	
	// Code adapted from http://mu.wordpress.org/forums/topic/5931
	function ModifyKsesPostTags($tags)
	{
	    global $allowedposttags;
	    
		// Allow additional tags.
		$more_tags = array(
			'iframe' => array(
				'src' => array(),
				'width' => array(),
				'height' => array(),
				'frameborder' => array(),
				'scrolling' => array(),
				'marginheight' => array(),
				'marginwidth' => array(),
			),
			'input' => array(
				'type' => array(),
				'src' => array(),
				'name' => array(),
				'value' => array(),
				'alt' => array(),
			),
			'option' => array(
				'value' => array(),
			),
			'select' => array(
				'name' => array(),
			),
		);
		$allowedposttags = array_merge($allowedposttags, $more_tags);
		
		foreach ($allowedposttags as $tag => $attr)
		{
			// Allow "class" and "id" attributes on all tags.
			$attr['class'] = array();
			$attr['id']    = array();
			$allowedposttags[$tag] = $attr;
	    }
//		echo var_dump($allowedposttags); die;
	    return $allowedposttags;
	}
	add_filter('init', __NAMESPACE__.'\\ModifyKsesPostTags');

?>