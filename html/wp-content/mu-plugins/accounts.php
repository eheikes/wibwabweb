<?php
	//
	// This file defines common routines for Accounts & Domains programming.
	// It may eventually need to be moved into the includes folder.
	//
	// Copyright (c) 2009-2011 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//
	
	namespace ArrowQuick\Account;

	require_once dirname(__FILE__).'/plans.php';
	require_once dirname(__FILE__).'/trustcommerce.php';
	
	// Retrieve information about the account's subscription.
	// Returns an object with the following properties:
	//   type            => subscription type (TRIAL, STARTER, etc)
	//   name            => human-readable name of the subscription type
	//   price           => cost per subscription period
	//   start_time      => timestamp of when account began
	//   renew_time      => timestamp of next billing cycle
	//   end_time        => timestamp of when subscription ends, or trial expires
	//   billing_id      => ID in the payment system
	//   credit_card     => last 4 digits of credit card used for payments
	//   credit_card_exp => when credit card expires, in YYYY-MM-DD format
	function GetSubscriptionInfo($blogId = null)
	{
		global $blog_id;
		global $wpdb;
		
		// If no blog ID was passed, used the currently active blog.
		if (is_null($blogId)) $blogId = $blog_id;

		$sql = $wpdb->prepare(
			"SELECT "
			. "`subscription_type` AS `type`"
			. ",`subscription_type`"
			. ",`subscription_price` AS `price`"
			. ",UNIX_TIMESTAMP(subscription_start) AS `start_time`"
			. ",UNIX_TIMESTAMP(subscription_end) AS `end_time`"
			. ",`billing_id`"
			. ",`credit_card`"
			. ",`credit_card_exp`"
			. " FROM `accounts`"
			. " WHERE `blog_id` = %d",
			$blogId
		);
		$data = $wpdb->get_row($sql);

		// Check for errors.
		if (!$data)
		{
			return null;
		}
		
		// Get the plan name.
		$all_plans = \ArrowQuick\GetSubscriptionPlans();
		$data->name = @$all_plans[$data->subscription_type]['name'];
		
		// Calculate renewal time.
		$day_renew   = date("j", $data->start_time);
		$month_renew = (date("j") > $day_renew ? date("n") + 1 : date("n"));
		$year_renew  = (date("n") == 12 ? date("Y") + 1 : date("Y"));
		$renew_timestamp = mktime(0, 0, 0, $month_renew, $day_renew, $year_renew);
		if ($renew_timestamp > $data->end_time)
		{
			$data->renew_time = null;
		}
		else
		{
			$data->renew_time = $renew_timestamp;
		}
		
		return $data;
	}
	
	// Saves subscription info for an account.
	// Takes an associative array that can include:
	//   type            => subscription type (TRIAL, STARTER, etc)
	//   price           => cost per subscription period
	//   start_time      => timestamp of when account begins
	//   end_time        => timestamp of when subscription ends, or trial expires
	//   billing_id      => ID in the payment system
	//   credit_card     => last 4 digits of credit card used for payments
	//   credit_card_exp => when credit card expires, in MMYY format
	// Returns TRUE if successful, FALSE otherwise.
	function SaveSubscriptionInfo($newInfo)
	{
		global $blog_id;
		global $wpdb;

		// Format the data for SQL.
		foreach ($newInfo as $key => $val)
		{
			switch ($key)
			{
				case 'type':
					$newInfo[$key] = "`subscription_type`='" . esc_sql($val) . "'";
					break;
				case 'start_time':
					$newInfo[$key] = "`subscription_start`='" . date('Y-m-d', $val) . "'";
					break;
				case 'end_time':
					if (is_null($val))
					{
						$newInfo[$key] = "`subscription_end`=NULL";
					}
					else
					{
						$newInfo[$key] = "`subscription_end`='" . date('Y-m-d', $val) . "'";
					}
					break;
				case 'price':
					$newInfo[$key] = "`subscription_price`=" . $val;
					break;
				case 'billing_id':
					$newInfo[$key] = "`billing_id`='" . esc_sql($val) . "'";
					break;
				case 'credit_card':
					$newInfo[$key] = "`credit_card`='" . esc_sql($val) . "'";
					break;
				case 'credit_card_exp':
					$month = substr($val, 0, 2);
					$year  = "20" . substr($val, 2, 2);
					$tmp = mktime(0, 0, 0, $month, 1, $year);
					$newInfo[$key] = "`credit_card_exp`='" . sprintf("%04d-%02d-%02d", $year, $month, date("t", $tmp)) . "'";
					break;
				default:
					break;
			}
		}
		
		$sql = $wpdb->prepare(
			"UPDATE `accounts` SET "
			. implode(",", $newInfo)
			. " WHERE `blog_id` = %d",
			$blog_id
		);
		$result = $wpdb->query($sql);
		if ($result === false)
		{
			return false;
		}
		
		return true;
	}
	
    // Returns FALSE if the current blog is in trial or expired.
	function IsAccountActive()
	{
		global $blog_id;
		global $wpdb;

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
		$is_not_trial = ($data[0]->subscription_type != 'TRIAL');
		$is_still_subscribed = is_null($data[0]->end_time) or ($data[0]->end_time >= $data[0]->now_time);

		return $is_not_trial && $is_still_subscribed;
	}
    
	// Retrieve information about the account's domains.
	// Returns an array of objects with the following properties:
	//   name       => domain name (all lowercase)
	//   active     => if the domain is active
	// The array is sorted alphabetically.
	function GetDomains()
	{
		global $blog_id;
		global $wpdb;

		// Retrieve the domains into an array of objects.
		$sql = $wpdb->prepare(
			"SELECT "
			. "LOWER(`domain`) AS `name`"
			. ",`active`"
			. " FROM `wp_domain_mapping`"
			. " WHERE `blog_id` = %d"
			. " ORDER BY `name`",
			$blog_id
		);
		$data = $wpdb->get_results($sql);
				
		return $data;
	}
	
    // Returns TRUE if either the site name or the tagline is WP's default.
	function IsOrgNameOrTaglineDefault()
	{
		global $blog_id;
		global $wpdb;

		// Retrieve the domains into an array of objects.
		$sql = $wpdb->prepare(
			"SELECT `option_name`, `option_value` "
			."FROM `wp_".$blog_id."_options` "
			."where (`option_name` = 'blogname') or (`option_name` = 'blogdescription')"
		);
		$data = $wpdb->get_results($sql);
		$is_default = false;
		foreach ($data as $d)
		{
			if (($d->option_name == 'blogname')
			and ($d->option_value == 'My Blog'))
			{
				$is_default = true;
			}
			elseif (($d->option_name == 'blogdescription')
			and     ($d->option_value == 'The best quality widgets for the price.'))
			{
				$is_default = true;
			}
		}
		return $is_default;
	}

	// Like WP's get_space_allowed(), but can specify which site to check.
	function GetSiteSpaceAllowed($id)
	{
		global $wpdb;

		$spaceAllowed = "wp_{$id}_options";
		$sql = $wpdb->prepare("SELECT option_value FROM wp_{$id}_options WHERE option_name = 'blog_upload_space' LIMIT 1");
		$row = $wpdb->get_row($sql);
		$spaceAllowed = (!empty($row)) ? $row->option_value : 0;

		if ($spaceAllowed == false)
		{
			$spaceAllowed = get_site_option("blog_upload_space"); // global config checked only if no local setting
		}
		if (empty($spaceAllowed)
		or  !is_numeric($spaceAllowed))
		{
			$spaceAllowed = 50;
		}

		return $spaceAllowed;
	}

	// Action hook to email the dev team when a user's profile changes.
	function UpdateProfile($user_id, $oldData)
	{
		global $wpdb;

		$sql = $wpdb->prepare(
			"SELECT *  "
			. "FROM `wp_users` "
			. "WHERE `id` = %d ", $user_id
		);
		$data = $wpdb->get_row($sql);

		// If the user info hasn't changed, do nothing.
		if ($data->user_email == $oldData->user_email)
		{
			return;
		}
		
		$txt = <<<MSG
Hey there, Webbie!

The user profile for USER_LOGIN has been changed.  Please update records such as Xero
and Constant Contact with the new info.

Nicename: USER_NICENAME
Email: USER_EMAIL
URL: USER_URL
Display name: USER_DISPLAY_NAME

Thanks!

--The WibWabWeb Team
MSG;

		$txt = str_replace("USER_LOGIN", $data->user_login, $txt);
		$txt = str_replace("USER_NICENAME", $data->user_nicename, $txt);
		$txt = str_replace("USER_EMAIL", $data->user_email, $txt);
		$txt = str_replace("USER_URL", $data->user_url, $txt);
		$txt = str_replace("USER_DISPLAY_NAME", $data->display_name, $txt);

		wp_mail('web@arrowquick.com', 'User profile changed', $txt);
	}
	add_action('profile_update', __NAMESPACE__.'\\UpdateProfile', 10, 2);
    
	// Inject CSS and JS into the <head> for the My Account page.
	function AccountPageExtra()
	{
		echo <<<END
<style type="text/css" media="screen">
	/* for My Account page */
	table.plans { border-spacing: .5em .1em; }
	table.plans th,
	table.plans td { padding: .5em 1em; text-align: center; }
	table.plans td { width: 15%; }
	table.plans th { border-top: 1px solid #333; }
	table.plans tr.action td { border-bottom: 1px solid #333; }
	table.plans th.current { border-top: 3px solid #333; }
	table.plans tr.action td.current { border-bottom: 3px solid #333; }
	.form-table .note { font-size: x-small; }
	#credit-card, #billing-name, #billing-address, #billing-city { width: 16em; }
	#cvv { width: 4em; }
	#billing-zip { width: 5em; }
</style>
<script type="text/javascript">
//<![CDATA[
	jQuery(document).ready(function($) {
		// Hide the "changed plan" message.
		$("#changed-plan").hide();
		
		// Hide the popup message.
		$("#popup-message").hide();
		
		// Activate the "switch to this plan" buttons.
		$("table.plans input[type='button']").click(function() {
			// Show message about needing to resubmit billing info.
			var msg = $("#popup-message").text();
			if (msg != "")
			{
				alert(msg);
			}
			
			// Switch default plan in the dropdown.
			var plan = $(this).siblings("input[name='plan']").val();
			$("#subscription-plan option:selected").removeAttr("selected");
			$("#subscription-plan option[value='" + plan + "']").attr("selected", "selected");
			$("#subscription-plan").change();
			
			// Move focus to the form.
			$("html, body").animate({ scrollTop: $("#billing-info").offset().top }, 500);
		});
		
		// Show the "changed plan" message as necessary.
		$("#subscription-plan").change(function() {
			var text = $("#subscription-plan option:selected").text();
			if (text.match(/\(current\)/))
			{
				$("#changed-plan").hide();
			}
			else
			{
				$("#changed-plan").show();
			}
		});
		
		// Convert links to popup windows.
		$("a.popup").click(function() {
			var url = $(this).attr("href");
			window.open (url, "popup", "status=0,toolbar=0,location=0,menubar=0,width=600,height=500");
			return false;
		});
	});
//]]>
</script>
END;
	}
	add_action('admin_head', __NAMESPACE__.'\\AccountPageExtra');
?>