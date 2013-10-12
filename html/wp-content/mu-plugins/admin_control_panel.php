<?php
	//
	// This file defines various changes to WP's
	//   admin control panel.
	//
	// Copyright (c) 2009-2011 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//
	//
	
	namespace
	{
		// Turn off the Contact Form 7 donation message.
		define('WPCF7_SHOW_DONATION_LINK', false);
	}
	
	namespace ArrowQuick\AdminControlPanel
	{
		//
		// All functions are in alphabetical order except for the init
		//   functions (here at the top).
		//
		
		
		//
		// General initialization for all pages.
		//
		function Init()
		{
			// Register the jQuery cookie script.
			wp_register_script('jquery-cookie', content_url().'/js/jquery.cookie.js', array('jquery'));
			
			// Register the Flash detection script.
			wp_register_script('flash-detect', content_url().'/js/flash_detect_min.js');			
		}
		add_action('init', __NAMESPACE__.'\\Init', 999);


		//
		// General initialization for admin pages.
		//
		function InitAdmin()
		{
			// Remove profile link on admin profile page.
			// This needs to be in "admin_init" hook or later because WPMu doesn't
			//   add the actions until "init"!
			remove_action('show_user_profile', 'myblogs_profile_link'); // see wp-admin/includes/mu.php
		}
		add_action('admin_init', __NAMESPACE__.'\\InitAdmin', 999);
		

		//
		// Add announcements to the top of the screen.
		//
		function Announcements()
		{
			$announcements = \ArrowQuick\Announcements::Get();
			if (!empty($announcements))
			{
				foreach($announcements as $announcement)
				{
					_e("<div class='important-message'>");
					_e($announcement->html);
					_e("</div>");
				}
			}
		}
		add_action('admin_notices', __NAMESPACE__.'\\Announcements');


		//
		// Update the contextual help system (help text).
		//
		function FilterContextualHelp($html)
		{
			// Change some wording.
			$html = str_replace('<h5>Get help with', '<h5>Help with', $html);
			$html = str_replace('<h5>Other Help', '<h5>More Help', $html);
			
			return $html;
		}
		add_filter('contextual_help', __NAMESPACE__.'\\FilterContextualHelp', 999);
		
		
		//
		// Update the contextual help system (context list).
		// __TODO__ This can probably be fleshed out more.
		//
		function FilterContextualHelpList($list)
		{
			// Dashboard
			$list['dashboard'] = '
				<p>' .	__('Most of the modules on this screen can be moved. If you hover your mouse over the title bar of a module you&rsquo;ll notice the 4 arrow cursor appears to let you know it is movable. Click on it, hold down the mouse button and start dragging the module to a new location. As you drag the module, notice the dotted gray box that also moves. This box indicates where the module will be placed when you release the mouse button.') . '</p>
				<p>' . __('The same modules can be expanded and collapsed by clicking once on their title bar and also completely hidden from the Screen Options tab.') . '</p>
			';
			
			// My Sites
			$list['sites'] = "<p>This screen lists all the sites you have with " . QS_NAME . ". You can switch to a different site by clicking on the &#8220;Dashboard&#8221; link.</p>";
			
			// Edit Pages
			$list['edit-pages'] = "<p>This screen lists all the pages on your site. To edit a page, click on its name below. To add a new page, click on the <a href='page-new.php'>Add New</a> link in the lefthand menu.</p>";
			
			// Edit/Add Page
			$list['page'] = "<p>This is the main editing screen for page editing. Page content, publish status, and other attributes can all be modified on this page.</p>";
			
			// General Options
			unset($list['options-general']);
			
			return $list;
		}
		add_filter('contextual_help_list', __NAMESPACE__.'\\FilterContextualHelpList', 999);

		//
        // Made changes to default help links.
        //
		function FilterDefaultHelp($html)
		{
			global $title;
			$html = "";
			
			if (!empty($title))
			{
				$html .= "<a href='http://" . QS_SITE . "/?s=" . urlencode($title) . "' target='_blank'>";
				$html .= "Find help on &#8220;" . esc_html($title) . "&#8221;</a><br />\n";
			}
			$html .= '<a href="http://' . QS_SITE . '/help" target="_blank">' . __('Tutorials') . '</a><br />';
			$html .= '<a href="http://' . QS_SITE . '/blog" target="_blank">' . QS_NAME . ' Blog</a><br />';
			$html .= '<form method="get" action="http://' . QS_SITE . '">'
				   . '<input type="text" value="' . the_search_query() .'" name="s" id="s" size="40" placeholder="Search Knowledgebase" />'
				   . '<input type="submit" value="Search" />'
				   . '</form>'
				   . '<br />';

			return $html;
		}
		add_filter('default_contextual_help', __NAMESPACE__.'\\FilterDefaultHelp', 999);
		

		//
		// Ignore default footer text and replace it with ours.
		//
		function FilterFooter($htmlSoFar)
		{
			// Include copyright message.
			$html = "Copyright &copy; " . QS_YEAR_STARTED;
			if (date('Y') > QS_YEAR_STARTED)
			{
				$html .= "&#8211;" . date('Y');
			}
			$html .= " <a href='" . QS_COMPANY_URL . "'>";
			$html .= htmlentities(QS_LEGAL_COMPANY_NAME);
			$html .= "</a>.";
			
			// Include link to EULA page.
			$html .= " <a href='http://" . QS_SITE . "/tos'>Terms of Service</a>.";
			
			return $html;
		}
		add_filter('admin_footer_text', __NAMESPACE__.'\\FilterFooter', 999);
		

		//
		// Ignore alternate footer text and replace it with ours.
		//
		function FilterUpdateFooter($htmlSoFar)
		{
			$html = 'Version ' . QS_VERSION;
			return $html;
		}
		add_filter('update_footer', __NAMESPACE__.'\\FilterUpdateFooter', 999);
		

		//
		// Exclude comments from the table columns.
		// This can be used for any control panel (posts, pages, media).
		//
		function HideCommentColumn($cols)
		{
			if (!is_super_admin())
			{
				unset($cols['comments']);
			}
			return $cols;
		}
		add_action('manage_pages_columns', __NAMESPACE__.'\\HideCommentColumn', 999);
		add_action('manage_posts_columns', __NAMESPACE__.'\\HideCommentColumn', 999);
		add_action('manage_media_columns', __NAMESPACE__.'\\HideCommentColumn', 999);
				

		//
		// Include custom CSS.
		//
		function IncludeCSS()
		{
			// There is no nice way to add CSS to an admin page.
			// wp_enqueue_style() doesn't apply! Just barf something into the page!
			// http://codex.wordpress.org/Creating_Admin_Themes
			echo "<link rel='stylesheet' type='text/css' href='" . content_url("/css/admin.css") . "'>\n";
		}
		add_action('admin_head', __NAMESPACE__.'\\IncludeCSS');


		//
		// Include needed JS libraries on the admin pages.
		//
		function LoadAdminJavascript()
		{
			wp_print_scripts('flash-detect'); // for Support page
		}
		add_action('admin_head', __NAMESPACE__.'\\LoadAdminJavascript');
		

		//
		// Include needed jQuery libraries on the login page.
		//
		function LoadLoginJavascript()
		{
			wp_print_scripts('jquery-cookie'); // for bookmark reminder box
		}
		add_action('login_head', __NAMESPACE__.'\\LoadLoginJavascript', 999);


		//
		// Makes changes to the toolbar (née admin bar).
		//
		function ModifyAdminBar($toolbar)
		{
			$all_nodes = $toolbar->get_nodes();
			
			// Change the greeting from "Howdy" to "Hello".
			$my_account = $toolbar->get_node('my-account');
			$my_account->title = str_replace("Howdy", "Hello", $my_account->title);			
			$toolbar->add_node($my_account);
			
			// Modify submenu items in the "My Sites" list.
			foreach ($all_nodes as $node)
			{
				if ($node->parent == 'my-sites-list')
				{
					$id = $node->id;
					
					// Remove the image in the site title
					//   (can add back when WP replaces their logo with favicons).
					$node->title = preg_replace("#<img.*?\>#", "", $node->title);
					$toolbar->add_node($node);

					// Remove "Manage Comments" node.
					$toolbar->remove_node("{$id}-c");
					
					// Change "New Post" to "New Page".
					$new = $toolbar->get_node("{$id}-n");
					$new->title = "New Page";
					$new->href = $new->href . "?post_type=page";
					$toolbar->add_node($new);
				}
			}

			// Modify the WordPress node into a WibWabWeb node.
			$wp_node = $toolbar->get_node('wp-logo');
			$wp_node->href = "http://wibwabweb.com";
			$wp_node->meta['title'] = "Go to WibWabWeb.com";
			$toolbar->add_node($wp_node);

			// Remove the default submenu in the WordPress node.
			$toolbar->remove_node('about');
			$toolbar->remove_node('wporg');
			$toolbar->remove_node('documentation');
			$toolbar->remove_node('support-forums');
			$toolbar->remove_node('feedback');
			
			// Add subnodes to the WordPress/WibWabWeb node.
			$news = array(
				'id'     => 'news',
				'title'  => esc_html("WibWabWeb Blog"),
				'parent' => 'wp-logo',
				'href'   => "http://wibwabweb.com/blog/",
			);
			$toolbar->add_node($news);
			$tuts = array(
				'id'     => 'tutorials',
				'title'  => esc_html("Tutorials & Guides"),
				'parent' => 'wp-logo',
				'href'   => "http://wibwabweb.com/help/",
			);
			$toolbar->add_node($tuts);
			$support = array(
				'id'     => 'support',
				'title'  => esc_html("Support & Feedback"),
				'parent' => 'wp-logo',
				'href'   => admin_url() . "support.php",
			);
			$toolbar->add_node($support);
			
			// Network admins get to see everything.
			if (!is_super_admin())
			{
				// Remove the "Comments" item.
				$toolbar->remove_node('comments');
				
				// Remove the "Add New->Post" item.
				$toolbar->remove_node('new-post');
				
				// Remove the "Add New->Link" item.
				$toolbar->remove_node('new-link');
			}
		}
		add_action('admin_bar_menu', __NAMESPACE__.'\\ModifyAdminBar', 999);


		//
		// Make changes to the page editing screen.
		//
		function ModifyPageEditor()
		{
			global $wp_meta_boxes;
			
			// Don't make any changes for site admins.
			if (!is_super_admin())
			{
				// Remove the custom metadata box for now.
				unset($wp_meta_boxes['page']['normal']['core']['pagecustomdiv']);
			
				// Leave out the Discussion options.
				unset($wp_meta_boxes['page']['normal']['core']['pagecommentstatusdiv']);
			}
		}
		add_action('do_meta_boxes', __NAMESPACE__.'\\ModifyPageEditor', 999);


		//
		// Block access to certain portions of the backend
		//   when the account is expired.
		//
		function RestrictExpiredAccounts()
		{
			global $blog_id;
			global $wpdb;
			
			// Site administrators retain access.
			if (is_super_admin()) return;

			$sql = $wpdb->prepare(
				"SELECT "
				. "`subscription_type`"
				. ",UNIX_TIMESTAMP(`subscription_end`) as `end_time`"
				. ",UNIX_TIMESTAMP(curdate()) as `now_time`"
				. " FROM `accounts`"
				. " WHERE `blog_id` = %d",
				$blog_id
			);
			$data = $wpdb->get_results($sql);

			$is_trial_expired = ($data[0]->end_time < $data[0]->now_time) && ($data[0]->subscription_type == 'TRIAL');

			// Can access Dashboard, My Sites, My Account, and Support
			$dashboard_page = (( substr( $_SERVER[ 'SCRIPT_NAME' ], -10 ) == '/index.php' )
				|| ( substr( $_SERVER[ 'SCRIPT_NAME' ], -13 ) == '/my-sites.php' )
				|| ( substr( $_SERVER[ 'SCRIPT_NAME' ], -12 ) == '/account.php' )
				|| ( substr( $_SERVER[ 'SCRIPT_NAME' ], -12 ) == '/support.php' ));

			// Redirect happens if 1) trial account is expired, and  2) trying to access off the dashboard area
			if ($is_trial_expired
			and !$dashboard_page)
			{
				$message = "Your trial has expired. You must <a href='account.php'>activate your account</a> to access this page.";
				wp_die($message);
			}
		}
		add_action('admin_init', __NAMESPACE__.'\\RestrictExpiredAccounts', 999);


		//
		// Add message to top of the screen for trial accounts.
		//
		function TrialMessage()
		{
			$plan = \ArrowQuick\Account\GetSubscriptionInfo();
			if ($plan->type == 'TRIAL')
			{
				_e("<div class='important-message'>");
				$ttl = ($plan->end_time - time()) / (60*60*24);
				if ($ttl > 0)
				{
					printf("You have %d days left in your trial.", $ttl);
				}
				else
				{
					_e("Your trial has expired.");
				}
				_e(" <a href='account.php'>Activate your account</a>.");
				_e("</div>");
			}
		}
		add_action('admin_notices', __NAMESPACE__.'\\TrialMessage');


		//
		// Re-order the left-hand WP navigation menu.
		//
		function ReorderMenu($activateReorder)
		{
			if (!$activateReorder) return true;
			return array(
				'index.php',				// Dashboard
				'separator1',				// --
				'edit.php?post_type=page',	// Pages
				'upload.php',				// Media
				'domainmapping',			// Domains
				'email.php',				// Email
				'separator2',				// --
				'themes.php',				// Appearance
				'tools.php',				// Tools
				'options-general.php',		// Settings
				'separator-last',			// ~~
				'wpcf7',					// Contact Form 7
			);
		}
		add_filter('custom_menu_order', __NAMESPACE__.'\\ReorderMenu');
		add_filter('menu_order',        __NAMESPACE__.'\\ReorderMenu');


		//
		// Changes the left-hand WP navigation menu.
		// Uses wp-admin-menu-classes.php abstraction routines as needed.
		//
		function UpdateMenu()
		{
			global $menu, $submenu;
			
			// Add the "My Account" page.
			add_submenu_page('index.php', __('My Account'), __('My Account'), 'read', 'account.php');
			
			// Add the "Support" page.
			add_submenu_page('index.php', __('Support'), __('Support'), 'read', 'support.php');
			
			// Remove the "Posts", "Links", "Comments", and "Users" menus.
			if (!is_super_admin())
			{
				remove_menu_page('edit.php');			// Posts
				remove_menu_page('link-manager.php');	// Links
				remove_menu_page('edit-comments.php');	// Comments
				remove_menu_page('users.php');			// Users
			}
			
			// Rename "My Page Order" to just "Page Order".
			rename_admin_menu_item('edit.php?post_type=page', 'mypageorder', "Page Order");
			
			// Add the "Domains" menu for site admins.
            if (get_site_option('dm_user_settings'))
            {
				add_menu_page(
					__('Domains'), __('Domains'),
					'manage_options',
					'domainmapping',
					'dm_manage_page',
					'images/domain.png'
				);
            }
            
            // Add the "Email" menu.
			add_menu_page(
				__('Email'), __('Email'),
				'read',
				'email.php',
				'',
				'images/mail_48.png'
			);

			// Move "Appearance" options under Settings.
            // BUG moving appearance under settings breaks the customize links.  lukef@AQ 1-29-10
            //   It also makes a visual break because widgets and the customize link are still at the bottom.
            /*
			$submenu['options-general.php'] = array_merge(
				(array)$submenu['options-general.php'],
				(array)$submenu['themes.php']
			);
			unset($menu[40]);
			*/
			
			// TODO Add the "Marketing" menu.
			/*
			add_menu_page(
				__('Marketing'), __('Marketing'),
				'read',
				'marketing.php',
				'',
				'images/menu/comments.png'
			);
			$menu[42] = array_pop($menu);
			add_submenu_page('marketing.php', __('Search Engines'), __('Search Engines'), 'read', 'marketing.php');
			add_submenu_page('marketing.php', __('Advertising'), __('Advertising'), 'read', 'advertising.php');
			add_submenu_page('marketing.php', __('Email Newsletters'), __('Email Newsletters'), 'read', 'newsletters.php');
			*/
			
			// TODO Add the "Statistics" menu.
			// __TODO__ use people icon
			/*
			add_menu_page(
				__('Statistics'), __('Statistics'),
				'read',
				'stats.php',
				'',
				'images/menu/users.png'
			);
			$menu[43] = array_pop($menu);
			*/
			// __TODO__ there can be a lot of submenus here

			// Make changes to the "Tools" menu.
			if (!is_super_admin())
			{
				// Remove the default "Tools" and "Import" and "Delete Site" pages.
				remove_submenu_page('tools.php', 'tools.php');			// Available Tools
				remove_submenu_page('tools.php', 'import.php');			// Import
				remove_submenu_page('tools.php', 'ms-delete-site.php');	// Delete Site

				// Add the contact form plugin.
                /* BUG This is acting weird now.  Leaving the contact form button active and
                 * commenting this link out until more direction.
				add_submenu_page(
					'tools.php',
					__('Form Builder'),
					__('Form Builder'),
					'read',
					'contact-form-7/admin/admin.php'
//                    '?page=wpcf7'
				); */
            }
			
			// BUG Remove the "Contact" plugin menu.  This should only remove the full menu item, not the submenu.
			/*
			if (!is_super_admin())
			{
				foreach ($menu as $index => $item)
				{
					if ($item[2] == 'contact-form-7/admin/admin.php')
					{
						unset($menu[$index]);
					}
				}
			}
			*/

			// Remove the "Site Stats" menu item (for now).
			remove_submenu_page('index.php', 'stats');

			// Update the "Settings" submenus.
			// Hide some items from all users who are not site admins.
			if (!is_super_admin())
			{
				$to_hide = array(
					'options-discussion.php',	// Discussion
					'options-privacy.php',		// Privacy
					'options-permalink.php',	// Permalinks
				);
				foreach ($to_hide as $slug)
				{
					remove_submenu_page('options-general.php', $slug);
				}
			}

			// Rename the CryptX plugin menu.
			rename_admin_menu_item('options-general.php', 'cryptx/cryptx.php', "CryptX Anti-Spam");
			
			// Move "Themes" item up to be the second in the list.
            // BUG themes is back under appearance.  lukef@AQ 1-29-10
            /*
			foreach($submenu['options-general.php'] as $key => $subm) {
				if ($subm[0]=='Themes') {
					$theme_loc = $key;
				}
			}
			$submenu['options-general.php']['0.5'] = $submenu['options-general.php'][$theme_loc];
			unset($submenu['options-general.php'][$theme_loc]);
			*/

			// Remove the domain mapping item under "Tools". (It has its own menu.)
			remove_submenu_page('tools.php', 'domainmapping');
			
			// Remove the Google Maps item under Settings.
			remove_submenu_page('options-general.php', 'cets_embedGmaps_options');

//echo "\n<!-- Menu";print_r(($menu));echo " -->\n";
//echo "\n<!-- Submenu";print_r($submenu);echo " -->\n";
		}
		add_action('admin_menu', __NAMESPACE__.'\\UpdateMenu', 999);

	} // end namespace
?>