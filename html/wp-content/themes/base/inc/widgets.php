<?php
//
// Built-in widgets.
//

class CopyrightMessageWidget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'copyright-message',
			'Copyright Message',
			array('description' => __("Simple copyright message with your business name", 'base'))
		);
	}

	public function widget($args, $instance) {
		extract($args);

		echo $before_widget;
		echo "Copyright &copy; " . date('Y');
		echo " " . get_bloginfo('name') . ".";
		echo " All rights reserved.";
		echo $after_widget;
	}

}
register_widget('CopyrightMessageWidget');
