<?php
	//
	// This file makes changes to the Dashboard page.
	//
	// Copyright (c) 2011 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//

	namespace ArrowQuick\Dashboard;
	
	function UpdateDashboard()
	{
		global $wp_meta_boxes;

		// Remove some of the default widgets.
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
		
		// Add the AQ widgets.
		UpdateNewsFeeds();	
	}

	function UpdateNewsFeeds()
	{
		global $wp_meta_boxes;

		// Retrieve the current options.
		$current_opts = get_option('dashboard_widget_options');

		// Use the "primary" (WP Dev Blog) widget for WibWabWeb news.
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
		$new_opts['dashboard_primary'] = array(
			'link'         => "http://www.wibwabweb.com/blog/",
			'url'          => "http://www.wibwabweb.com/feed/",
			'title'        => QS_NAME . " Blog",
			'items'        => 3,
			'show_summary' => 1,
			'show_author'  => 0,
			'show_date'    => 1,
		);
		if ($current_opts['dashboard_primary']['url'] != $new_opts['dashboard_primary']['url'])
		{
			// Manually flush the cache.
			delete_transient('dash_' . md5('dashboard_primary'));
		}
		wp_add_dashboard_widget('dashboard_primary', $new_opts['dashboard_primary']['title'], 'wp_dashboard_primary'); // Note: no configuration callback

		// Use the "secondary" (Planet) widget for smallbiz planet newsfeed.
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
		// TODO see above (primary) for code

		// Update the widget options.
		update_option('dashboard_widget_options', $new_opts);
	}
	
	add_action('wp_dashboard_setup',
	           __NAMESPACE__.'\\UpdateDashboard',
			   999
	);

?>