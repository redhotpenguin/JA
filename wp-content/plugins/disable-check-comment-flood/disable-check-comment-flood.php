<?php
/*
Plugin Name: Disable Check Comment Flood
Description: Disables the Check Comment Flood feature so comments can be post-dated.
Version: 1.0
Author: Bangbay Siboliban
Author URI: http://www.siboliban.org/
*/

remove_filter('check_comment_flood', 'check_comment_flood_db');

?>