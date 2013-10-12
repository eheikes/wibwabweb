<?php
	//
	// This file adds to the signup forms.
	//
	// Copyright (c) 2011 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//
	//
	
	namespace ArrowQuick\Signup
	{
		// For some reason, WP loads the homepage on all the signup
		//   and activation pages.
		// This makes is_front_page() TRUE, which we don't want.
		function DontLoadHomepage()
		{
			global $wp_query;

			// Load a regular page (i.e., not the front page).
			// The content isn't actually shown, so this should work.
			$wp_query = new \WP_Query("page_id=54"); // Contact Us page

			// Fix the title.
			$wp_query->posts[0]->post_title = "Sign Up";
			
			// Define variables so we can customize the page.
			// TODO make the theme flexible enough (or refactor it) so we don't need this
			if (!isset($wp_query->is_signup))
			{
				$wp_query->is_signup = true;
			}
		}
		add_action('signup_header',   __NAMESPACE__.'\\DontLoadHomepage');
		add_action('activate_header', __NAMESPACE__.'\\DontLoadHomepage');
		
		// Modify <body> CSS classes.
		function ModifyBodyCSS($classes)
		{
			if (is_signup())
			{
				// Remove unwanted classes.
				$removals = array('page-id-54', 'contact');
				foreach ($classes as $key => $val)
				{
					if (in_array($val, $removals))
					{
						unset($classes[$key]);
					}
				}

				// Add additional classes.
				$classes[] = 'signup';
			}

			return $classes;
		}
		add_filter('body_class',  __NAMESPACE__.'\\ModifyBodyCSS', 90);

		// Add some JS to the blog form.
		function BlogFormJS($errors)
		{
			// Modify the site URL in realtime with the form data.
			$suffix = QS_SITE; // == "wibwabweb.com"
			echo <<<SCRIPT
<script type="text/javascript">
	jQuery(document).ready(function($) {
		
		function UpdateSiteUrl()
		{
			var val = $("input[name='blogname']").val();
			if (val == '') val = 'domain';
			$("#new_site_url").text(val + ".$suffix");
		}
		$("input[name='blogname']").keyup(UpdateSiteUrl);
		UpdateSiteUrl();
	});
</script>
SCRIPT;
		}
		add_action('signup_blogform', __NAMESPACE__.'\\BlogFormJS');

		// Replace the subject line of the activation email.
		function FilterActivationSubject($txtSoFar, $domain, $path, $title, $user, $user_email, $key, $meta)
		{
			$txt = "Activate your " . QS_NAME . " site";
			return $txt;
		}
		add_filter('wpmu_signup_blog_notification_subject',
				   __NAMESPACE__.'\\FilterActivationSubject',
				   999,
				   8 // num arguments
		);

	} // end namespace
	
	namespace // global
	{
		// Create an is_signup() helper function for themes.
		if (!function_exists('is_signup'))
		{
			function is_signup()
			{
				global $wp_query;
				
				if (!isset($wp_query->is_signup)) return false;
				
				return (bool)$wp_query->is_signup;
			}
		}
	} // end namespace

?>