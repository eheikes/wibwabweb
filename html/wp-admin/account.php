<?php
	//
	// Billing & Account screen.
	//
	// Copyright (c) 2009-2011 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//

	// WordPress Administration Bootstrap
	require_once('admin.php');
	$title = __('My Account');
	require_once('admin-header.php');

	$plans     = ArrowQuick\GetSubscriptionPlans();
	$account   = ArrowQuick\Account\GetSubscriptionInfo();
	$is_active = ArrowQuick\Account\IsAccountActive();

	function SaveAccount()
	{
		global $plans, $account, $is_active;
		global $blog_id;
		
		// Lookup the chosen plan.
		$new_plan = @$_POST['plan'];
		if (!array_key_exists($new_plan, $plans))
		{
			wp_die(__('Your account info could not be saved. Please let us know if the problem persists.'));
		}
		
		// Get values of billing info.
		$billing_id = ($account->billing_id == "" ? null : $account->billing_id);
		$amount  = $plans[$new_plan]['cost'];
		$cc      = preg_replace('#[^\d]#', '', @$_POST['cc']);
		$exp     = preg_replace('#[^\d]#', '', @$_POST['exp-month'] . substr(@$_POST['exp-year'], -2));
		$cvv     = preg_replace('#[^\d]#', '', @$_POST['cvv']);
		$name    = @$_POST['name'];
		$address = @$_POST['address1'];
		$city    = @$_POST['city'];
		$state   = @$_POST['state'];
		$zip     = preg_replace('#[^\d]#', '', @$_POST['zip']);

		// Send billing info to payment processor.
		$tc = new TrustCommerce;
		$billing_id = $tc->EditRecurring($billing_id, array(
			"amount"   => $amount,
			"cc"       => $cc,
			"exp"      => $exp,
			"cvv"      => $cvv,
			"name"     => $name,	// optional
			"address1" => $address,
			"city"     => $city,	// optional
			"state"    => $state,	// optional
			"zip"      => $zip,
		));
		/*
		$data = $tc->GetResponse(PAYMENT_DATA);
		echo "data <pre>"; print_r($data); echo "</pre><br>\n";
		echo "errors <pre>"; print_r($tc->LastErrors()); echo "</pre><br>\n";
		*/
		if ($billing_id === false)
		{
			return false;
		}
		
		// Email administrator if the plan has changed.
		if ($account->type != $new_plan)
		{
			$blog_name = get_bloginfo('name');
			$blog_url  = get_site_url();
			$to      = get_blog_option(1, "admin_email", "web@arrowquick.com");
			$subject = "WibWabWeb site has switched plans";
			if (WP_DEBUG)
			{
				$subject = "TEST " . $subject;
			}
			$headers = "From: do-not-reply@wibwabweb.com\r\n";
			$msg     = <<<MSG
This is an automated message.

The {$blog_name} site (#{$blog_id}, {$blog_url}) has switched plans, from {$account->type} to {$new_plan}.

Please pro-rate the remainder of the month and credit their account (if downgrading) or charge them (if upgrading).
MSG;
			wp_mail($to, $subject, $msg, $headers);
		}
		
		// Update the account record.
		$data = array(
			'type'            => $new_plan,
			'price'           => $amount,
			'end_time'        => null,
			'billing_id'      => $billing_id,
			'credit_card'     => substr($cc, -4),
			'credit_card_exp' => $exp,
		);
		if (!$is_active)
		{
			$data['start_time'] = time();
		}
		$res = ArrowQuick\Account\SaveSubscriptionInfo($data);
		if (!$res)
		{
			return false;
		}
		
		return true;
	}
		
	// "Update Billing Info" form submission
	if (!empty($_POST) && check_admin_referer("update_account", "_wpnonce"))
	{
		$save_success = SaveAccount();
		if (!$save_success)
		{
			?>
			<div id="message" class="error"><p><strong>Your account could not be updated. Please double-check your billing information.</strong></p></div>
			<?php
		}

		// Account may have changed, so reload info.
		$account   = ArrowQuick\Account\GetSubscriptionInfo();
		$is_active = ArrowQuick\Account\IsAccountActive();
	}
?>
<div class="wrap">

<?php if (@$save_success): ?>
	<div id="message" class="updated"><p><strong>
	<?php _e('Your account has been updated.'); ?>
	</strong></p></div>
<?php endif; ?>

<h2><?php echo esc_html($title); ?></h2>
	
	<table class="plans">
		<tr class="name">
			<?php foreach ($plans as $key => $plan): ?>
			<th <?php echo ($account->type == $key ? "class='current'" : ""); ?> valign="top"><?php esc_html_e($plan['name']); ?></th>
			<?php endforeach; ?>
		</tr>
		<tr class="price">
			<?php foreach ($plans as $key => $plan): ?>
			<td <?php echo ($account->type == $key ? "class='current'" : ""); ?>><?php
				if ($account->type == $key) // this is the current plan
				{
					if ($account->price > 0)
					{
						echo "$" . number_format($account->price, 2) . "/month";
					}
					else
					{
						echo "Free";
					}
				}
				else
				{
					if ($plan['cost'] > 0)
					{
						echo "$" . number_format($plan['cost'], 2) . "/month";
					}
					else
					{
						echo "Free";
					}
				}
			?></td>
			<?php endforeach; ?>
		</tr>
		<tr class="action">
			<?php foreach ($plans as $key => $plan): ?>
			<td <?php echo ($account->type == $key ? "class='current'" : ""); ?>><?php
				if ($account->type == $key	// this is the current plan
				and $is_active)
				{
					echo "<div><em>Current Plan</em></div>\n";
					if ($account->renew_time) // will renew
					{
						echo "Renews on " . date("F j", $account->renew_time);
					}
					elseif ($account->end_time) // has expiration
					{
						echo "Expires on " . date("F j, Y", $account->end_time);
					}
				}
				elseif ($key != "TRIAL")
				{
					?>
					<input type="hidden" name="plan" value="<?php echo $key; ?>" />
					<input type="button" value="<?php
						if ($is_active)
						{
							echo "Switch to";
						}
						else
						{
							echo "Subscribe to";
						}
					?> this plan" class="button-primary" />
					<?php
				}
			?></td>
			<?php endforeach; ?>
		</tr>
	</table>
	
	<?php if ($is_active): ?>
		<h3 id="billing-info" class="title"><?php _e("Update Billing Information"); ?></h3>
		<?php if ($account->credit_card != ""): ?>
			<p>Current card on file: <strong>************<?php echo $account->credit_card; ?></strong></p>
		<?php endif; ?>
	<?php else: ?>
		<h3 id="billing-info" class="title"><?php _e("Activate My Account") ?></h3>
	<?php endif; ?>
	<form action="" method="post">
		<p id="popup-message"><?php
			if ($is_active)
			{
				echo "For security purposes, we require you to re-enter your billing information when you change your subscription plan.";
			}
			else
			{
				echo "Thank you for subscribing! Please enter your billing information to activate your account.";
			}
		?></p>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="subscription-plan">Subscription Plan</label>
				</th>
				<td>
					<select id="subscription-plan" name="plan">
						<?php foreach ($plans as $key => $plan): ?>
							<?php if ($key != "TRIAL"): ?>
								<option value="<?php echo $key; ?>" <?php if ($account->type == $key) { echo 'selected="selected"'; } ?>><?php
									esc_html_e($plan['name']);
									if ($account->type == $key
									and $is_active)
									{
										echo " (current)";
									}
								?></option>
							<?php endif; ?>
						<?php endforeach; ?>
					</select>
					<?php if ($is_active): ?>
						<span id="changed-plan">Your account will be charged or credited for the difference in price (pro-rated).</span>
					<?php else: ?>
						<span>Details on all plans may be found <a href="<?php echo get_site_url(1, "pricing/"); ?>">here</a>.</span>
					<?php endif; ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="credit-card">Card Number</label>
				</th>
				<td>
					<input type="text" id="credit-card" name="cc" />
					<img src="<?php echo includes_url('images/credit-card-logos.png'); ?>" alt="All major credit cards accepted." title="All major credit cards accepted." height="24" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="expiration-month">Expiration Date</label>
				</th>
				<td>
					<select id="expiration-month" name="exp-month">
						<?php for ($i = 1; $i <= 12; $i++): ?>
							<option><?php echo sprintf("%02d", $i); ?></option>
						<?php endfor; ?>
					</select>
					<select id="expiration-year" name="exp-year">
						<?php for ($i = date("Y"); $i <= date("Y") + 25; $i++): ?>
							<option><?php echo $i; ?></option>
						<?php endfor; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="cvv">Card Security Code</label>
					<div class="note">(3- or 4-digit number on your card)</div>
				</th>
				<td>
					<input type="text" id="cvv" name="cvv" />
					<a href="http://en.wikipedia.org/w/index.php?title=Card_security_code&printable=yes" class="popup">more info</a>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="billing-name">Cardholder&#8217;s Name</label>
					<div class="note">(as it appears on the credit card)</div>
				</th>
				<td>
					<input type="text" id="billing-name" name="name" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="billing-address">Billing Address</label>
				</th>
				<td>
					<input type="text" id="billing-address" name="address1" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="billing-city">City</label> &amp;
					<label for="billing-state">State</label>
				</th>
				<td>
					<input type="text" id="billing-city" name="city" />
					<?php $states = ArrowQuick\Geo\GetUsStates(); ?>
					<select id="billing-state" name="state">
					<?php foreach ($states as $abbr => $name): ?>
						<option value="<?php echo $abbr; ?>"><?php esc_html_e($name); ?></option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="billing-zip">Zip Code</label>
				</th>
				<td>
					<input type="text" id="billing-zip" name="zip" />
				</td>
			</tr>
		</table>
		<?php wp_nonce_field("update_account"); ?>
		<p class="submit">
			<input type="submit" value="Save Changes" class="button-primary" />
			<img src="<?php echo includes_url('images/padlock.gif'); ?>" alt="Padlock icon." title="All billing information is encrypted." width="16" height="16" style="vertical-align: middle" />
			<?php if (WP_DEBUG): ?>
			<strong>Debugging mode is ON -- card transaction will <em>not</em> be processed.</strong>
			<?php endif; ?>
		</p>
	</form>
	<?php if (!$is_active): ?>
		<p><em>While <?php _e(QS_NAME) ?> is
		in beta, we will have to manually activate your account.
		Please allow a day or two while your account is activated.</em></p>
	<?php endif; ?>

	<?php if ($is_active): ?>
		<h3 class="title"><?php _e('Cancel My Account') ?></h3>
		<p>We&#8216;re sorry to see you go. You may cancel your account at
		any time.</p>
		<?php // __TODO__ automate it ?><p><em>While <?php _e(QS_NAME) ?> is
		in beta, you must <a href="support.php">contact us</a> to cancel your account.    In the future, this process will be automated and immediate.</em></p>
		<p>Note that cancellation will delete all your pages and files.
		You should <a href="export.php">export your data</a> before
		you cancel your account if you would like to use it in the future.</p>
	<?php endif; ?>

</div>
<?php
include('admin-footer.php');
?>
