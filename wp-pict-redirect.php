<?php 
/*
Plugin Name: Wordpress Picture Redirect
Plugin URI: 
Description: Fast Wordpress Redirect Picture with Cache
Author: Robbyn Rahmandaru
Version: 1.0
Author URI: http://robbynr.com/

*/



function wpr_show($view, $data = array()) {
	extract($data);
	include __DIR__ . "/views/{$view}.php";
}

function rrmdir($dir) {
   	if (is_dir($dir)) {
     	$objects = scandir($dir);
     	foreach ($objects as $object) {
       		if ($object != "." && $object != "..") {
         		if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
       		}
 		}
	   	reset($objects);
   		rmdir($dir);
   	}
}

add_action('admin_menu', 'wpr_menu');
function wpr_menu() {
    add_menu_page('Picture Redirect', 'Pict Redirect', 'manage_options', 'wpr_setting', 'wpr_setting');
}

function wpr_setting() {

	$siteurl  = get_option('siteurl');
	$domain   = str_replace(array('https://','http://','www.'),'',$siteurl);

	$htaccess = ABSPATH . ".htaccess";	

	$data['fail_htaccess'] 	= file_exists($htaccess) && is_writable($htaccess);
	$data['wpr_redir'] 		= get_option('wpr_redir','single');
	$data['wpr_status']	    = get_option('wpr_status', 0);
	
	wpr_show('setting', $data);
}

add_action('wp_ajax_setting', 'wpr_save');
function wpr_save(){

	$siteurl  = get_option('siteurl');
	$domain   = str_replace(array('https://','http://','www.'),'',$siteurl);

	$htaccess = ABSPATH . ".htaccess";
	$htaccess_redir = str_replace("{{domain}}", $domain, file_get_contents(__DIR__."/core/redir.txt"));
	
	// buat status 0 atau 1
	$status = isset($_POST['status']) ? 1 : 0;
	
	update_option('wpr_redir', $_POST['redir']);		
	echo('<div class="updated"><p><strong>Success</strong> Redirect berhasil di rubah menuju '.$_POST['redir'].'</p></div>');	

	if(file_exists($htaccess) && is_writable($htaccess)) {
		if($status == 1) { // enable			

			// jika belum ada maka dibuat
			$data = file_get_contents($htaccess);
			if(strpos($data, $htaccess_redir) === FALSE) {
				$rewrite = "$data $htaccess_redir";
				file_put_contents($htaccess, $rewrite);
			}
			// tambah file wpr_redir.php
			if( ! file_exists(ABSPATH . "wpr_redir.php")) {
				$script = file_get_contents(__DIR__."/core/wpr_redir.php");
				file_put_contents(ABSPATH . "wpr_redir.php", $script);
			}
			// tambah file cache standar	
			if( ! is_dir(ABSPATH . "wpr_cache")) mkdir(ABSPATH . "wpr_cache");
			$serialize = array('info' => array(
				'domain' => $domain,
				'to'	 => $_POST['redir']
			));
			file_put_contents(ABSPATH . "wpr_cache/main.cache", serialize($serialize));
			

			die('<div class="updated"><p><strong>Success</strong> Redirect berhasil di aktifkan</p></div>');
		} else if ($status == 0) {			

			// jika sudah ada maka di hapus
			$data = file_get_contents($htaccess);
			if(strpos($data, $htaccess_redir) !== FALSE) {
				$rewrite = str_replace($htaccess_redir, '', $data);
				file_put_contents($htaccess, $rewrite);
			}
			// hapus wpr_redir
			if(file_exists(ABSPATH . "wpr_redir.php")) {
				unlink(ABSPATH . "wpr_redir.php");
			}
			// clear cache
			rrmdir(ABSPATH . "wpr_cache");

			die('<div class="updated"><p><strong>Success</strong> Redirect berhasil di non aktifkan</p></div>');
		}
		update_option('wpr_status', $status);
	} else {
		echo '<div class="error"><p><strong>ERROR</strong> .htaccess not found / not writeable</p></div>';
	}
}

add_action('wp_ajax_clear_cache', 'wpr_clear_cache');
function wpr_clear_cache(){
	$tmp = file_get_contents(ABSPATH . "wpr_cache/main.cache");

	rrmdir(ABSPATH . "wpr_cache");	
	
	if( ! is_dir(ABSPATH . "wpr_cache")) mkdir(ABSPATH . "wpr_cache");	
	file_put_contents(ABSPATH . "wpr_cache/main.cache", $tmp);

	die('<div class="updated"><p><strong>Success</strong> Cache clear</p></div>');
}

?>