<?php
	//
	// Changes to the "generator" value for pages and feeds.
	//
	// Copyright (c) 2009-2011 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//
	//
	
	namespace ArrowQuick;
	
	function ReplaceGenerator($txtSoFar, $type = 'html')
	{
		// For admin pages, display the normal Wordpress info.
		if (is_admin())
		{
			return $txtSoFar;
		}
		
		// For public-facing pages, replace WP with WibWabWeb.
		switch ($type)
		{
			case 'html':
				$txtSoFar = '<meta name="generator" content="'
						  . esc_attr(QS_NAME) . ' ' . esc_attr(QS_VERSION) . '">';
				break;
			case 'xhtml':
				$txtSoFar = '<meta name="generator" content="'
						  . esc_attr(QS_NAME) . ' ' . esc_attr(QS_VERSION) . '" />';
				break;
			case 'atom':
				$txtSoFar = '<generator uri="http://' . esc_attr(QS_SITE) . '/" version="'
						  . esc_attr(QS_VERSION) . '">' . esc_html(QS_NAME) . '</generator>';
				break;
			case 'rss2':
				$txtSoFar = '<generator>http://' . esc_attr(QS_SITE) . '/?v='
						  . esc_attr(QS_VERSION) . '</generator>';
				break;
			case 'rdf':
				$txtSoFar = '<admin:generatorAgent rdf:resource="http://' . esc_attr(QS_SITE)
						  . '/?v=' . esc_attr(QS_VERSION) . '" />';
				break;
			case 'comment':
				$txtSoFar = '<!-- generator="' . esc_attr(QS_NAME) . '/' . esc_attr(QS_VERSION)
						  . '" -->';
				break;
			case 'export':
				$txtSoFar = '<!-- generator="' . esc_attr(QS_NAME) . '/' . esc_attr(QS_VERSION)
						  . '" created="'. date('Y-m-d H:i') . '"-->';
				break;
			default:
				break;
		}
		
		return $txtSoFar;
	}

	// Register all the filters & actions with Wordpress.
	add_filter('get_the_generator_html',
	           __NAMESPACE__.'\\ReplaceGenerator',
			   999,
			   2
	);
	add_filter('get_the_generator_xhtml',
	           __NAMESPACE__.'\\ReplaceGenerator',
			   999,
			   2
	);
	add_filter('get_the_generator_atom',
	           __NAMESPACE__.'\\ReplaceGenerator',
			   999,
			   2
	);
	add_filter('get_the_generator_rss2',
	           __NAMESPACE__.'\\ReplaceGenerator',
			   999,
			   2
	);
	add_filter('get_the_generator_rdf',
	           __NAMESPACE__.'\\ReplaceGenerator',
			   999,
			   2
	);
	add_filter('get_the_generator_comment',
	           __NAMESPACE__.'\\ReplaceGenerator',
			   999,
			   2
	);
	add_filter('get_the_generator_export',
	           __NAMESPACE__.'\\ReplaceGenerator',
			   999,
			   2
	);

	// Include the generator in the admin pages.
	add_action('admin_head', 'wp_generator');
?>