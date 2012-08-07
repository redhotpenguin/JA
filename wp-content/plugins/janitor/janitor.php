<?php
/*
Plugin Name: JAnitor
Plugin URI: 
Description: JA Maintenance
Version: 0.1
Author: Jonas Palmero
Author URI: http://www.twitter.com/welldonejonas
*/


add_action('init', 'init_janitor'); 

function init_janitor(){
	if(is_admin()){ 
		include_once('janitor_admin.php');
		$admin_menu = new Janitor_Admin();
		
	}
}




?>