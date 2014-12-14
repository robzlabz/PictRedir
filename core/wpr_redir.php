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
    	unset($cache);
    } else {
    	$attach = $wpdb->get_row("SELECT ID, post_parent FROM $wpdb->posts WHERE post_type='attachment' AND guid LIKE '%{$image}%' LIMIT 1");
		$ID = $attach->post_parent;
		$single = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE ID = $ID LIMIT 1");	

		$to_single = get_permalink($single);
		$to_attach = get_permalink($attach->ID);

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
	
	unset($main_cache);
	// redirect now!
	header("HTTP/1.1 302 Found");
	header("Location: ". $url);	
	die();
}

?>