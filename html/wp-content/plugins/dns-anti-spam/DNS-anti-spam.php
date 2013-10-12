<?php
/*
Plugin Name: Block-lists anti-spam measures
Version: 1.5.1
Plugin URI: http://weblog.sinteur.com/index.php?p=8106
Description: check if a comment poster is on an open proxy list, and check if the content contains known spammer domains
Author: John Sinteur, with a big thank you to io_error!
Author URI: http://weblog.sinteur.com/
*/


function check_blackholes( $comment_text )
{

	$spammer_ip = $_SERVER['REMOTE_ADDR'];
	$rev = array_reverse(explode('.', $spammer_ip));

	$lookup = implode('.', $rev) . '.' . 'l1.spews.dnsbl.sorbs.net.';

	if ($lookup != gethostbyname($lookup)) {
		header("Location: http://www.spews.org/ask.cgi?x=" . $spammer_ip);
		exit();
	}
	$lookup = implode('.', $rev) . '.' . 'sbl-xbl.spamhaus.org.';
	if ($lookup != gethostbyname($lookup)) {
		header("Location: http://www.spamhaus.org/query/bl?ip=" . $spammer_ip);
		exit();
	}
	$lookup = implode('.', $rev) . '.' . 'list.dsbl.org.';
	if ($lookup != gethostbyname($lookup)) {
		header("Location: http://dsbl.org/listing?" . $spammer_ip);
		exit();
	}


	return  $comment_text ;
}


function check_for_surbl ( $comment_text )
{
/*  for a full explanation, see http://www.surbl.org 
	summary: blocks comment if it contains an url that's on a known spammers list.
*/

	//get site names found in body of comment.
	$regex_url   = "/(www.)([^\/\"<\s]*)/im";
	$mk_regex_array = array();
	preg_match_all($regex_url, $comment_text, $mk_regex_array);
 		
	for( $cnt=0; $cnt < count($mk_regex_array[2]); $cnt++ ) {
		$domain_to_test = rtrim($mk_regex_array[2][$cnt],"\\");
$test .= $domain_to_test;
		if (strlen($domain_to_test) > 3)
		{
			$domain_to_test = $domain_to_test . ".multi.surbl.org.";
			if( gethostbyname($domain_to_test) != $domain_to_test ) {
				
				header("Location: $comment_text");
				exit();
			}
 		}
	}
	return $test . $comment_text ;

}

function check_for_surbl2 ( $comment_url )
{
/*  for a full explanation, see http://www.surbl.org 
	summary: blocks comment if it contains an url that's on a known spammers list.
*/

	$pieces = explode('/',$comment_url);
	for( $cnt=0; $cnt < count($pieces); $cnt++ ) {
		$short_url = $pieces[$cnt];

		if ($short_url != 'http')
		{
			$short_url = str_replace("www.", "", "$short_url");
			if (strlen($short_url) > 3)
			{
				$domain_to_test = $short_url . ".multi.surbl.org.";
				if( gethostbyname($domain_to_test) != $domain_to_test ) {
					
					header("Location: $comment_url");
					exit();
				}
			}
		}
	}
	return  $comment_url ;

}


add_action('pre_comment_content', 'check_for_surbl', 1);
add_action('pre_comment_author_url', 'check_for_surbl2', 1);
add_action('pre_comment_content', 'check_blackholes', 1);

?>
