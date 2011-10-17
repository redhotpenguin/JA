<?php
/*
Plugin Name: BuddyPress Edit User Profiles.
Plugin URI: http://sndevelopment.com
Description: This plugin is designed to add a link to users.php to allow admins to edit user's BuddyPress Profiles.
Version: 1.0
Author: Sean Newby
Author URI: http://sndevelopment.com
License: GPL2

Copyright 2011 Sean Newby (email : seannewby@sndevelopment.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if( !class_exists( 'bpEditProfile' ) ){
	class bpEditProfile{

		function bpEditProfile() {
			__construct();
		}
		
		function __construct() {
			add_action( 'init' , array( &$this, 'init' ) );
		}
		
		function init(){
			// localization
			load_plugin_textdomain( 'bp-edit-profile' , false , dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			
			// check for buddypress and in the dashboard before we add the filter
			if( function_exists( 'bp_core_get_user_domain' ) && is_admin() ) {
				add_filter ( 'user_row_actions' , array( &$this , 'add_edit_profile' ) , 20 , 2 );
			}
		}
		
		function add_edit_profile( $actions , $user_object ){
			// Add "Edit BuddyPress Profile" to $actions array if current user is an admin
			if( current_user_can( 'delete_users' ) ) {
				$actions['edit_profiles'] = '<a href="' . bp_core_get_user_domain( $user_object->ID ) . 'profile/edit/" title="' . __( 'Edit BuddyPress Profile' , 'bp-edit-profile' ) . '">' . __( 'Edit BuddyPress Profile' , 'bp-edit-profile' ) . '</a>';
			}
			return $actions;
		}
	}
}

// Instantiate the class
if ( class_exists( 'bpEditProfile' ) ) {
	$bp_edit_profile = new bpEditProfile();
}