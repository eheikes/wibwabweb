<?php
	//
	// This file defines various changes to the login page.
	//
	// Copyright (c) 2011 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//
	//

	namespace ArrowQuick\Login;
	
	function IncludeCSS()
	{
		echo <<<CSS
<style type='text/css'>
	h1 a { background: url(//wibwabweb.com/wp-content/themes/wibwabweb/images/wibwabweb-logo.png) 50% 50% no-repeat !important; }
	#login { margin-top:3em; /* tighten it up */ }
	#bookmark-reminder { width:320px; margin:1em auto; }
	#bookmark-reminder div { text-align:right; font-size:smaller; }
</style>
CSS;
	}
	
	add_action('login_head', __NAMESPACE__.'\\IncludeCSS');
?>