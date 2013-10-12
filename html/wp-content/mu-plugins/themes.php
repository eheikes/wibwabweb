<?php
    //
    // This file defines changes to themes and their rendering.
    //
    // Copyright (c) 2012 ArrowQuick Solutions LLC.
    // Licensed under the GNU General Public License version 2
    //   (http://www.gnu.org/licenses/).
    //

	namespace ArrowQuick;
	
	// Replace the "aq-wibwabweb" language with English.
	function ModifyHtmlLanguage($langAttribs)
	{
		return str_replace("aq-wibwabweb", "en", $langAttribs);
	}
	add_filter('language_attributes', __NAMESPACE__.'\\ModifyHtmlLanguage');
?>