jQuery(document).ready(function() {

	// Callback to show/hide the image preview and button.
	function SetImageVisibility() {
		if (jQuery('#logo_type option:selected').val() == 'image') {
			jQuery('#logo_image_button').show();
			jQuery('#logo_preview img').show();
		} else {
			jQuery('#logo_image_button').hide();
			jQuery('#logo_preview img').hide();
		}
	}

	// For initial page load.
	SetImageVisibility();

	// Set change event for selection menu.
	jQuery('#logo_type').change(SetImageVisibility);

	// Activate "Choose Image" button.
	jQuery('#logo_image_button').click(function() {
		formfield = jQuery('#upload_image').attr('name');
		// Include "theme_options" parameter for referrer checking. "TB_iframe" must be last.
		tb_show('', 'media-upload.php?type=image&amp;theme_options=1&amp;TB_iframe=true');
		return false;
	});

	// Override the "Insert into Post" button.
	window.send_to_editor = function(html) {

		// WP returns HTML for the editor, so extract the IMG.
		var obj = jQuery(html);
		if (!obj.is('img')) {
			obj = obj.find('img');
		}

		var imgurl = obj.attr('src');
		jQuery('input[name="base_theme_options[logo_image]"]').val(imgurl);
		jQuery('#logo_preview img').attr('src', imgurl);

		tb_remove();
	}

});
