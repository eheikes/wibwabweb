<?php
	//
	// Stats screen.
	//
	// Copyright (c) 2009 ArrowQuick Solutions LLC.
	// Licensed under the GNU General Public License version 2
	//   (http://www.gnu.org/licenses/).
	//

	// WordPress Administration Bootstrap
	require_once('admin.php');
	$title = __('Statistics');
	$parent_file = 'stats.php';
	require_once('admin-header.php');
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo wp_specialchars( $title ); ?></h2>

	<p>This feature is still being developed.</p>

</div>
<?php
include('admin-footer.php');
?>
