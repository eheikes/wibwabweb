<?php
	//
	// Support page.
	//
	// Copyright (c) 2009 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//

	// WordPress Administration Bootstrap
	require_once('admin.php');
	$title = __('Support');
	require_once('admin-header.php');
	
	// If a ticket was submitted, email it to the administrator.
	if (isset($_POST['blog_id']))
	{
		$site = QS_SITE;
		$to = get_site_option("admin_email");
		$subject = QS_NAME . " Support Request";
		$has_cookies = (@$_COOKIE[AUTH_COOKIE] == '' ? "Disabled" : "Enabled");
		$message = <<<MSG
The following request was sent through the Support page ({$_SERVER["HTTP_HOST"]}{$_SERVER["REQUEST_URI"]}).

Blog ID: {$_POST['blog_id']}

Type: {$_POST['type']}

Priority: {$_POST['priority']}

Message: {$_POST['message']}

IP Address: {$_SERVER['REMOTE_ADDR']}

User Agent: {$_SERVER['HTTP_USER_AGENT']}

Cookies: {$has_cookies}

Javascript: {$_POST['javascript_on']}

Flash Version: {$_POST['flash_vers']}

Screen Size: {$_POST['screen_size']}

Browser Size: {$_POST['browser_size']}

Color Depth: {$_POST['color_depth']}

MSG;
		$sent_success = wp_mail($to, $subject, $message);
	}
?>
<div class="wrap">
<?php if (@$sent_success): ?>
	<div id="message" class="updated fade"><p><strong>
	<?php _e('Your support request has been sent.'); ?>
	</strong></p></div>
<?php endif; ?>
<h2><?php echo esc_html( $title ); ?></h2>

	
	<h3 class="title"><?php _e('General Help'); ?></h3>
	<p>For help with using <?php _e(QS_NAME); ?>, please click on the
	&#8220;Help&#8221; button in the top right corner of every page.</p>
	
	<h3 class="title"><?php _e('Support Tickets'); ?></h3>
	<p>If you have a question or problem that is not addressed by the
	help system, or would like to suggest a new feature for <?php _e(QS_NAME); ?>,
	you can submit a ticket below.</p>
	
	<?php // __TODO__ tickets list and integration with Eventum ?>
	<form action="" method="post">
		<input type="hidden" name="blog_id" value="<?php _e($blog_id) ?>" />

		<table>
			<tr>
				<td>
					<label for="support_type">Type:</label>
				</td>
				<td>
					<select id="support_type" name="type">
						<option selected="selected">Support Request</option>
						<option>Feature Suggestion</option>
						<option>Question / Help</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="support_priority">Priority:</label>
				</td>
				<td>
					<select id="support_priority" name="priority">
						<option>High</option>
						<option selected="selected">Normal</option>
						<option>Low</option>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<label for="support_message">Message:</label>
				</td>
				<td>
					<textarea id="support_message" name="message" rows="10" cols="40"></textarea>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" value="Submit" /></td>
		</table>
		<input type="hidden" id="javascript_on" name="javascript_on" value="Disabled" />
		<input type="hidden" id="flash_vers" name="flash_vers" value="(Javascript not working)" />
		<input type="hidden" id="screen_size" name="screen_size" value="" />
		<input type="hidden" id="browser_size" name="browser_size" value="" />
		<input type="hidden" id="color_depth" name="color_depth" value="" />
		<script type="text/javascript">
			// Javascript check
			jQuery('#javascript_on').val("Enabled");
			
			// Flash Version
			if (FlashDetect.major >= 1)
			{
				jQuery('#flash_vers').val(FlashDetect.major + "." + FlashDetect.minor + "." + FlashDetect.revision);
			}
			else
			{
				jQuery('#flash_vers').val("Not installed");
			}
			
			// Screen Resolution & Color Depth
			if (self.screen)
			{
				jQuery('#screen_size').val(screen.width + ' x ' + screen.height);
				jQuery('#color_depth').val(screen.colorDepth + ' bit');
			}
			else if (self.java)
			{
				var javaobj   = java.awt.Toolkit.getDefaultToolkit();
				var screenobj = javaobj.getScreenSize();
		
				jQuery('#screen_size').val(screenobj.width + ' x ' + screenobj.height);
				if (self.screen)
				{
					jQuery('#color_depth').val(screen.colorDepth + ' bit');
				}
			}
		
			// Browser Size
			var bsw = '';
			var bsh = '';
			if (window.innerWidth)
			{
				bsw = window.innerWidth;
				bsh = window.innerHeight;
			}
			else if (document.documentElement)
			{
				bsw = document.documentElement.clientWidth;
				bsh = document.documentElement.clientHeight;
			}
			else if (document.body)
			{
				bsw = document.body.clientWidth;
				bsh = document.body.clientHeight;
			}
			if (bsw != '' && bsh != '')
			{
				jQuery('#browser_size').val(bsw + ' x ' + bsh);
			}
		</script>
	</form>

</div>
<?php
include('admin-footer.php');
?>
