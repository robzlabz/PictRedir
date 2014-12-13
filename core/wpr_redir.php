<?php

/*
|
| Redirect to Post
| By RobzLabz
|
*/

include('wp-config.php');

if(isset($_GET['to'])){

	if( ! is_dir(ABSPATH . "wpr_cache") === false) mkdir(ABSPATH . "wpr_cache");
	
	$cache_file = ABSPATH . "wpr_cache/main.cache";	
	if(! file_exists($cache_file) || ! is_writable($cache_file)) {
		die("Please re-enable plugins");
	}
	
	$main_cache	= unserialize(file_get_contents($cache_file));

	$image 		= $_GET['to'];
	$imageMD5 	= md5($image);
	$wpr_to 	= $main_cache['info']['to'];

    // cari gambar yang benar
    if(file_exists(ABSPATH . "wpr_cache/" . $imageMD5)) {
    	$cache = unserialize(file_get_contents(ABSPATH . "wpr_cache/" . $imageMD5));
    	$to_single 		= $cache['single'];
    	$to_attach		= $cache['attachment'];
    } else {
    	$attach = $wpdb->get_results("SELECT ID, post_parent FROM $wpdb->posts WHERE post_type='attachment' AND post_status='inherit' AND guid LIKE '%{$image}'");
		$ID = $attach[0]->post_parent;
		$single = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type='post' AND post_status='publish' AND ID = $ID");	

		$to_single = get_permalink($single[0]->ID);
		$to_attach = get_permalink($attach[0]->ID);

		$cache['single'] = $to_single;
		$cache['attachment'] = $to_attach;

		file_put_contents(ABSPATH . "wpr_cache/" . $imageMD5, serialize($cache));
    }
	
	if( $wpr_to == 'attachment' ) { // attachment
		$url = $to_attach;
	} else if($wpr_to == 'single') { // single
		$url = $to_single;
	} else { // home
		$url = "http://" . $main_cache['info']['domain'];
	}
	
	// redirect now!
	header("HTTP/1.1 302 Found");
	header("Location: ". $url);	
	die();
}

?>