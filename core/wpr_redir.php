<?php

/*
|
| Redirect to Post
| By RobzLabz
|
*/


if(isset($_GET['to'])){

	include('wp-config.php');

	$image = $_GET['to'];    
    $siteurl  = get_option('siteurl');
	$domain   = str_replace(array('https://','http://','www.'),'',$siteurl);

    // cari gambar yang beanr    
	$attach = $wpdb->get_results("SELECT ID, post_name, guid, post_parent FROM $wpdb->posts WHERE post_type='attachment' AND post_status='inherit' AND guid LIKE '%{$image}'");
	$ID = $attach[0]->post_parent;
	$single = $wpdb->get_results("SELECT ID, post_name FROM $wpdb->posts WHERE post_type='post' AND post_status='publish' AND ID = $ID");

	$to_single = $single[0]->post_name . "/";
	$to_attach = $to_single . $attach[0]->post_name . "/";

	if( get_option('wpr_to') == 'attachment' ) {
		$url = $to_attach;
	} else { // single
		$url = $to_single;
	}	

	// redirect now!
	header("HTTP/1.1 302 Found");
	header("Location: http://{$domain}/". $url);	
	die();
}

?>