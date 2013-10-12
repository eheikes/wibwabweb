<?php
	//
	// This file contains routines specific to when a new site is created.
	// Note "activation" means creating a new (trial) site in Wordpress
	//    parlance (not moving a trial account to a paid account).
	//
	// Copyright (c) 2011 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//
	
	namespace ArrowQuick\Activation;
	
	// Sets up initial pages for a new site.
	function AddInitialPages($blogID, $userID, $password, $title, $meta)
	{
		switch_to_blog($blogID);

		// Change the title of the sample page to "Home".
		// The correct content is already added from Network Admin -> Settings.
		wp_update_post(array(
			'ID'         => 2, // first page is always #2
			'post_title' => 'Home',
			'post_name'  => 'home',
		));
		
		// Create an "About" page.
		wp_insert_post(array(
			'post_title'   => 'About',
			'post_name'    => 'about',
			'post_content' => 'This is a page where you can describe yourself, your mission, and your approach to serving your customers.',
			'post_type'    => 'page',
			'menu_order'   => 1,
			'post_status'  => 'publish',
			'post_author'  => $userID,
		));

		// Create a "Contact" page.
		wp_insert_post(array(
			'post_title'   => 'Contact',
			'post_name'    => 'contact',
			'post_content' => 'This is a page where you customers can find information on how to contact you (address, phone, email). You can also <a href="http://www.wibwabweb.com/help/contact-form/">create a contact form</a> so they can reach you.',
			'post_type'    => 'page',
			'menu_order'   => 2,
			'post_status'  => 'publish',
			'post_author'  => $userID,
		));

		restore_current_blog();
	}
	
	// Add the customer to our Constant Contact list.
	function AddToMailingList($blogID, $userID, $password, $title, $meta)
	{
		$user = get_userdata($userID);
		$ctct = new \ConstantContact;
		$ctct->AddContact($user->user_email);
	}

	// Configures blog settings for a new site.
	function ConfigureBlog($blogID, $userID, $password, $title, $meta)
	{
		switch_to_blog($blogID);

		// Set the storage quota.
		$plans = \ArrowQuick\GetSubscriptionPlans();
		update_option('blog_upload_space', $plans['TRIAL']['storage']);

		restore_current_blog();
	}

	// Configures initial settings for a new site.
	// Stats plugin requires the definition of the QS_STATS_API constant.
	function ConfigureSettings($blogID, $userID, $password, $title, $meta)
	{
		switch_to_blog($blogID);
		$current_site = get_current_site();

		// Set the homepage to the "Home" page.
		update_option('show_on_front', "page");
		update_option('page_on_front', "2"); // the first page is always #2

		// Set CryptX plugin settings (selects "Text scrambled by AntiSpamBot").
		$cryptx_opts = array(
			"at"                   => " [at] ",
			"dot"                  => " [dot] ",
			"alt_linktext"         => "",
			"alt_linkimage"        => "",
			"http_linkimage_title" => "",
			"alt_uploadedimage"    => plugins_url("cryptx/images/mail.gif"),
			"alt_linkimage_title"  => "",
			"opt_linktext"         => "4",
			"c2i_font"             => dirname(dirname(__FILE__))."/plugins/cryptx/fonts/Verdana.ttf",
			"c2i_fontSize"         => "",
			"c2i_fontRGB"          => "",
			"theContent"           => "on",
			"theExcerpt"           => "on",
			"commentText"          => "on",
			"excludedIDs"          => "",
			"java"                 => "0",
			"load_java"            => "1",
		);
		update_option('cryptX', $cryptx_opts);

		// Turn off discussion for the site.
		update_option('default_comment_status', "closed");
		update_option('default_ping_status',    "closed");
		update_option('default_pingback_flag',  "");
		
		// Activate "Wordpress.com Stats" plugin.
		if (defined('QS_STATS_API'))	// API key defined?
		{
			// The plugin always spits out errors, so don't bother
			//   checking the result
			activate_plugin(WP_PLUGIN_DIR."/stats/stats.php");
			
			// This is basically copied from the stats_admin_load()
			//   function in the stats plugin.
			stats_check_key(QS_STATS_API);
			stats_set_api_key(QS_STATS_API);
			stats_get_blog_id(QS_STATS_API);
			if (stats_get_option('blog_id'))
			{
				stats_set_option('key_check', false);
			}
		}

		restore_current_blog();
	}
	
	// Setup an account record for a new site.
	function SetupAccount($blogID, $userID, $password, $title, $meta)
	{
		global $wpdb;
		
		// Check if a record already exists for this site.
		$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM accounts WHERE blog_id = %s", $blogID));
		if ($count)
		{
			wp_mail(
				get_site_option("admin_email"),
				"Possible WibWabWeb bug?",
				"A new site was created (#$blogID), but there is already a record in the 'accounts' DB table for it. This shouldn't happen.\n\nsee SetupAccount() in " . __FILE__
			);
			return;
		}
		
		// Add a new record in the "accounts" DB table for the site.
		$one_day = 60*60*24;
		$success = $wpdb->insert(
			"accounts",
			array(
				'blog_id'            => $blogID,
				'subscription_type'  => "TRIAL",
				'subscription_start' => date("Y-m-d"),
				'subscription_end'   => date("Y-m-d", time() + $one_day*31),
				'subscription_price' => 0.00,
			),
			array(
				"%d",
				"%s",
				"%s",
				"%s",
				"%d", // note %d won't work with float values
			)
		);
		if (!$success)
		{
			wp_mail(
				get_site_option("admin_email"),
				"Problem creating new WibWabWeb site",
				"A new site was created (#$blogID), but a new record couldn't be added to the 'accounts' DB table.\n\nsee SetupAccount() in " . __FILE__
			);
		}
	}
	
	// Note the priority ordering of the actions -- it's important!
	add_action('wpmu_activate_blog', __NAMESPACE__.'\\SetupAccount',      10, 5);
	add_action('wpmu_activate_blog', __NAMESPACE__.'\\AddInitialPages',   10, 5);
	add_action('wpmu_activate_blog', __NAMESPACE__.'\\ConfigureBlog',     10, 5);
	add_action('wpmu_activate_blog', __NAMESPACE__.'\\ConfigureSettings', 10, 5);
	//add_action('wpmu_activate_blog', __NAMESPACE__.'\\AddToMailingList',  90, 5);
?>