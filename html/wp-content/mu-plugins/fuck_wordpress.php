<?php
	//
	// This file removes stuff from the Wordpress Gestapo (Automattic).
	//
	// Copyright (c) 2009-2011 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//
	//

	// Stop fucking up URLs. (http://justintadlock.com/archives/2010/07/08/lowercase-p-dangit)
	// Copyright (c) Tom Lany (http://tomlany.net/2010/05/wordpress-to-wordpress/)
	foreach (array('the_content', 'the_title', 'comment_text') as $filter)
	{
		$priority = has_filter($filter, 'capital_P_dangit');
		if ($priority !== false)
		{
			remove_filter($filter, 'capital_P_dangit', $priority);
		}
	}

	// The "WordPress.com Stats Smiley Remover" removes the smiley face
	//   placed in the footer of sites by the WordPress.com Stats plugin.
	// Copyright (c) 2008, Chrsitopher Ross
	// http://thisismyurl.com/downloads/wordpress/plugins/wordpress-com-stats-smiley-remover/
	function thisismyurl_wpsmileyremover_header_code_function()
	{
		echo '<style type="text/css"><!-- img#wpstats{display:none;} --></style>'."\n";
	}
	add_action('wp_head','thisismyurl_wpsmileyremover_header_code_function');

?>