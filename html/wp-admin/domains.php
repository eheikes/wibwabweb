<?php
	//
	// Domains screen.
	//
	// Copyright (c) 2009 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//

	// WordPress Administration Bootstrap
	require_once('admin.php');
	$title = __('Domains');
	$parent_file = 'domains.php';
	require_once('admin-header.php');
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo wp_specialchars( $title ); ?></h2>

	<?php
		$domains = ArrowQuick\Account\GetDomains();
		if (empty($domains)):
	?>
	<p>You have not added any domains to your site yet.
	You will need at least one domain for visitors to find you.</p>
	<?php
		else:
	?>
		<table class="ruled">
		<thead>
			<th>Domain Name</th><th>Active?</th>
		</thead>
		<tbody>
		<?php foreach ($domains as $domain): ?>
			<tr>
				<td><?php echo htmlentities($domain->name); ?></td>
				<td class="checkmarks"><?php echo ($domain->active ? "<strong>X</strong>" : "&nbsp;"); ?></td>
			</tr>
		<?php endforeach;?>
		</tbody>
		</table>
	<?php
		endif;
	?>
	
	<h3 class="title"><?php _e('Add/Remove Domains') ?></h3>
        <?php $plan = ArrowQuick\Account\GetSubscriptionInfo();

        if ($plan->type == 'TRIAL') { ?>
            <p>You currently have a Trial account. Please <a href="account.php">sign up for a paid subscription plan</a> to assign a domain to your website.</p>
        <?php } ?>
	<?php // __TODO__ automate it ?><p><em>While <?php _e(QS_NAME) ?> is
	in beta, you must <a href="support.php">contact us</a> to add or remove
	domains. In the future, this process will be automated.</em></p>

</div>
<?php
include('admin-footer.php');
?>
