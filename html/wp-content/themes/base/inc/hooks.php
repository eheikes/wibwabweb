<?php
//
// Framework-specific hooks.
//

// header.php
function base_stylesheets() { do_action('base_stylesheets'); }
function base_wrap_before() { do_action('base_wrap_before'); }
function base_header_before() { do_action('base_header_before'); }
function base_header_inside() { do_action('base_header_inside'); }
function base_header_inside_after() { do_action('base_header_inside_after'); }
function base_header_after() { do_action('base_header_after'); }

// 404.php, archive.php, front-page.php, index.php, loop-page.php, loop-single.php,
// loop.php, page-custom.php, page-full.php, page.php, search.php, single.php
function base_content_before() { do_action('base_content_before'); }
function base_content_after() { do_action('base_content_after'); }
function base_main_before() { do_action('base_main_before'); }
function base_main_after() { do_action('base_main_after'); }
function base_post_before() { do_action('base_post_before'); }
function base_post_after() { do_action('base_post_after'); }
function base_post_inside_before() { do_action('base_post_inside_before'); }
function base_post_inside_after() { do_action('base_post_inside_after'); }
function base_loop_before() { do_action('base_loop_before'); }
function base_loop_after() { do_action('base_loop_after'); }
function base_sidebar_before() { do_action('base_sidebar_before'); }
function base_sidebar_inside_before() { do_action('base_sidebar_inside_before'); }
function base_sidebar_inside_after() { do_action('base_sidebar_inside_after'); }
function base_sidebar_after() { do_action('base_sidebar_after'); }

// footer.php
function base_footer_before() { do_action('base_footer_before'); }
function base_footer_inside() { do_action('base_footer_inside'); }
function base_footer_inside_after() { do_action('base_footer_inside_after'); }
function base_footer_after() { do_action('base_footer_after'); }
