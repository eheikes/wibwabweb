<?php
function base_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?>>
		<article id="comment-<?php comment_ID(); ?>">
			<header class="comment-author vcard">
				<?php echo get_avatar($comment); ?>
				<?php printf(__('<cite class="fn">%s</cite>', 'base'), get_comment_author_link()); ?>
				|
				<time datetime="<?php echo comment_date('c'); ?>"><a href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)); ?>" title="Permalink to this comment"><?php printf(__('%1$s %2$s', 'base'), get_comment_date(),  get_comment_time()); ?></a></time>
				<?php edit_comment_link(__('(Edit)', 'base'), '', ''); ?>
			</header>

			<?php if ($comment->comment_approved == '0'): ?>
				<div class="alert alert-block fade in">
					<a class="close" data-dismiss="alert">&times;</a>
					<p><?php _e('Your comment is awaiting moderation.', 'base'); ?></p>
				</div>
			<?php endif; ?>

			<section class="comment-body">
				<?php comment_text() ?>
			</section>

			<?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>

		</article>
<?php } ?>

<?php if (post_password_required()) { ?>
	<section id="comments">
		<div class="alert alert-block fade in">
			<a class="close" data-dismiss="alert">&times;</a>
			<p><?php _e('This post is password protected. Enter the password to view comments.', 'base'); ?></p>
		</div>
	</section><!-- /#comments -->
<?php
	return;
} ?>

<section id="comments">
	<h2><?php _e("Comments"); ?></h2>
	<p>
		<?php if (have_comments()): ?>
			<?php printf(_n('One comment', '%1$s comments', get_comments_number(), 'base'), number_format_i18n(get_comments_number())); ?>.
			<?php if (!comments_open() && !is_page() && post_type_supports(get_post_type(), 'comments')): ?>
				<?php _e("Comments are closed.", 'base'); ?>
			<?php endif; ?>
		<?php else: ?>
			<?php _e("No comments yet.", 'base'); ?>
		<?php endif; ?>
		<?php _e("You can follow responses to this post through the", 'base'); ?>
		<?php post_comments_feed_link(__("RSS feed", 'base')); ?>.
	</p>

	<?php if (have_comments()): ?>
		<ol class="commentlist">
			<?php wp_list_comments(array('callback' => 'base_comment')); ?>
		</ol>

		<?php if (get_comment_pages_count() > 1 && get_option('page_comments')): // are there comments to navigate through ?>
			<nav id="comments-nav" class="pager">
				<div class="previous"><?php previous_comments_link(__('&larr; Older comments', 'base')); ?></div>
				<div class="next"><?php next_comments_link(__('Newer comments &rarr;', 'base')); ?></div>
			</nav>
		<?php endif; // check for comment navigation ?>

	<?php endif; // have comments ?>
	
</section><!-- /#comments -->

<?php if (comments_open()): ?>
	<section id="respond">
		<h2><?php comment_form_title(__('Leave a Comment', 'base'), __('Leave a Reply to %s', 'base')); ?></h2>
		<div class="cancel-comment-reply"><?php cancel_comment_reply_link(); ?></div>
		<?php if (get_option('comment_registration') && !is_user_logged_in()): ?>
			<p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.', 'base'), wp_login_url(get_permalink())); ?></p>
		<?php else: ?>
			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
				<?php if (is_user_logged_in()): ?>
					<p><?php printf(__('Logged in as <a href="%s/wp-admin/profile.php">%s</a>.', 'base'), get_option('siteurl'), $user_identity); ?> <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php __('Log out of this account', 'base'); ?>"><?php _e('Log out &raquo;', 'base'); ?></a></p>
				<?php else: ?>
					<label for="author"><?php _e('Name', 'base'); if ($req) _e(' (required)', 'base'); ?></label>
					<input type="text" class="text" name="author" id="author" value="<?php echo esc_attr($comment_author); ?>" size="22" tabindex="1" <?php if ($req) echo "aria-required='true'"; ?>>
					<label for="email"><?php _e('Email', 'base'); if ($req) _e(' (required)', 'base'); ?></label>
					<div class="more-info"><?php _e("Your email address will not be published.", 'base'); ?></div>
					<input type="email" class="text" name="email" id="email" value="<?php echo esc_attr($comment_author_email); ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?>>
					<label for="url"><?php _e('Website', 'base'); ?></label>
					<input type="url" class="text" name="url" id="url" value="<?php echo esc_attr($comment_author_url); ?>" size="22" tabindex="3" placeholder="http://www.example.com">
				<?php endif; ?>
				<label for="comment"><?php _e('Comment', 'base'); ?></label>
				<textarea name="comment" id="comment" class="input-xlarge" cols="22" rows="8" tabindex="4"></textarea>
				<input name="submit" class="btn btn-primary" type="submit" id="submit" tabindex="5" value="<?php _e('Submit Comment', 'base'); ?>">
				<?php comment_id_fields(); ?>
				<?php do_action('comment_form', $post->ID); ?>
			</form>
		<?php endif; // if registration required and not logged in ?>
	</section><!-- /#respond -->
<?php endif; // if comments open ?>