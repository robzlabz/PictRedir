<?php 
/*
Plugin Name: Wordpress Picture Redirect
Plugin URI: 
Description: Fast Wordpress Redirect Picture
Author: Robbyn Rahmandaru
Version: 1.0
Author URI: http://robbynr.com/

*/



function wpr_show($view, $data = array()) {
	extract($data);
	include __DIR__ . "/views/{$view}.php";
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
	
	$status = isset($_POST['status']) ? 1 : 0;

	// hanya mengubah tujuan redirect 
	if(get_option('wpr_status') == $status) {
		update_option('wpr_redir', $_POST['redir']);
		die('<div class="updated"><p><strong>Success</strong> Htaccess berhasil di rubah menuju '.$_POST['redir'].'</p></div>');	
	} 


	$htaccess_redir = file_get_contents(__DIR__."/core/redir.txt");
	if(file_exists(ABSPATH . ".htaccess") && is_writable(ABSPATH . ".htaccess")) {
		if($status == 1) {

			// jika belum ada maka dibuat

			update_option('wpr_status', $status);
		} else if ($status == 0) {
			// jika sudah ada maka di hapus
		}
	} else {
		echo '<div class="error"><p><strong>ERROR</strong> .htaccess not found / not writeable</p></div>';
	}

}

?>