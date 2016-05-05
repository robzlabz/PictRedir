<?php

/*
|
| Redirect to Post
| By RobzLabz
|
*/

include('wp-config.php');

if(isset($_GET['to'])){

    if( ! is_dir(ABSPATH . "wpr_cache")) mkdir(ABSPATH . "wpr_cache");

    $cache_file = ABSPATH . "wpr_cache/main.cache";
    if( ! file_exists($cache_file)) {
        die("Please re-enable plugins");
    }

    $main_cache     = unserialize(file_get_contents($cache_file));

    $image          = $_GET['to'];
    $imageMD5       = md5($image);
    $wpr_to         = $main_cache['info']['to'];

    // cari gambar yang benar
    if(file_exists(ABSPATH . "wpr_cache/" . $imageMD5)) {
    	$cache 			= unserialize(file_get_contents(ABSPATH . "wpr_cache/" . $imageMD5));
    	$to_single 		= $cache['single'];
    	$to_attach		= $cache['attachment'];
    } else {
    	$attach = $wpdb->get_row("SELECT post_name, post_parent FROM $wpdb->posts WHERE post_type='attachment' AND guid LIKE '%{$image}%' LIMIT 1");
		$ID = $attach->post_parent;
		$single = $wpdb->get_var("SELECT post_name FROM $wpdb->posts WHERE ID = $ID LIMIT 1");

		$base = $_SERVER['HTTP_HOST'];

		$to_single = 'http://' . $base . '/' . $single . '/';
		$to_attach = 'http://' . $base . '/' . $single . '/' . $attach->post_name . '/';

		$cache['single'] = $to_single;
		$cache['attachment'] = $to_attach;

		file_put_contents(ABSPATH . "wpr_cache/" . $imageMD5, serialize($cache));
    }

	if( $wpr_to == 'attachment' ) { // attachment
		$url = $to_attach;
	} else if($wpr_to == 'single') { // single
		$url = $to_single;
	} else { // home
		$url = "http://" . $_SERVER['HTTP_HOST'];
	}

	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	echo '<meta name="referrer" content="unsafe-url">';
	echo '<META HTTP-EQUIV="Refresh" CONTENT="'.mt_rand(2,3).'; URL='.$url.'">';

	die();
}

?>
