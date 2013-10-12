<?php
	//
	// Email screen.
	//
	// Copyright (c) 2009 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//

	// WordPress Administration Bootstrap
	require_once('admin.php');
	$title = __('Email');
	$parent_file = 'email.php';
	require_once('admin-header.php');
	// Determine the domain to use for email.
	$domain = "mailanyone.net";
	foreach (ArrowQuick\Account\GetDomains() as $domain_info)
	{
		if ($domain_info->active)
		{
			$domain = $domain_info->name;
		}
	}
?>
<div class="wrap">

<div style="float: left; width: 49%;">

<?php //screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>
<p>To check your email, please login using your email address and password. To manage all email accounts and settings, login as
&#8220;<?php $user = wp_get_current_user(); echo $user->user_login;?>&#8221;.</p>

<form method="post" action="https://webmail.mailanyone.net/webmail.php" target="_blank">
<input type="hidden" name="DoLogin" value="Y" />
<input type="hidden" name="FailURL" value="https://webmail.mailanyone.net/webmail.php" />
<input type="hidden" name="LogoutURL" value="https://webmail.mailanyone.net/webmail.php" />
<table>
	<tr>
		<td>Username:</td>
		<td><input name="user" type="text" id="user" size="13" value="" /></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input name="password" type="password" id="password" size="13" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="Login" /></td>
	</tr>
</table>
</form>
<p>You can also access the webmail interface from any browser or smartphone at <kbd><a href="http://webmail.<?php echo $domain; ?>">http://webmail.<?php echo $domain; ?></a></kbd>.

</div>
<div style="float: right; width: 49%;">

<h2>Email Settings</h2>
<p>You can also configure your email program (Outlook, Thunderbird, Apple Mail, and others) to send and receive your <?php echo QS_NAME; ?> email.</p>

<h3>Incoming Server (POP or IMAP)</h3>
<p><strong>POP3</strong> allows you to download your messages directly to your computer.
You can choose to leave a copy of the message on the server or remove them to free up space.</p>
<p>Your POP server is: <kbd>pop.<?php echo $domain; ?></kbd></p>
<p><strong>IMAP</strong> is a mirror image of the server.
This is a good option to use if you are checking your email on many computers or devices.
If you move mail into your email client then it will be copied to the server as well.</p>
<p>Your IMAP server is: <kbd>imap.<?php echo $domain; ?></kbd></p>

<h3>Outgoing Server (SMTP)</h3>
<p>The <strong>SMTP</strong> server is used for sending messages.
Usually an outgoing server is provided by your internet service provider (ISP).
Please check with the company that provides your internet service for the server to use.</p>
<p>You can alternatively use the <?php echo QS_NAME; ?> outgoing server. For this server, you must authenticate using your username and password.</p>
<p>Your SMTP server is: <kbd>smtp.<?php echo $domain; ?></kbd></p>

	</div>
</div>


       
<?php
include('admin-footer.php');
?>
