<?php
    //
    // Modifications to existing theme widgets, or new widgets.
    //

	namespace ArrowQuick\Widgets;

	//
	// Remove some of the default widgets.
	//
	function RemoveWidgets()
	{
		if (!is_super_admin())
		{
			// WP default widgets, tied to posts/blogging.
			unregister_widget('WP_Widget_Archives');
			unregister_widget('WP_Widget_Calendar');
			unregister_widget('WP_Widget_Categories');
			unregister_widget('WP_Widget_Links');
			unregister_widget('WP_Widget_Recent_Comments');
			unregister_widget('WP_Widget_Recent_Posts');
			unregister_widget('WP_Widget_Tag_Cloud');

			// WP default widget that displays login/admin/feed links.
			unregister_widget('WP_Widget_Meta');

			// My Page Order plugin's widget (advanced version of default Pages widget).
			unregister_widget('mypageorder_Widget');
		}
	}
	add_action('widgets_init', __NAMESPACE__.'\\RemoveWidgets', 99);

?>