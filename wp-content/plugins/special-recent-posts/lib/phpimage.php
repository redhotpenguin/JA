<?php
/*
| ----------------------------------------------------
| File        : phpimage.php
| Project     : Special Recent Posts plugin for Wordpress
| Version     : 1.9
| Description : This file contains the widget main class.
| Author      : Luca Grandicelli
| Author URL  : http://www.lucagrandicelli.com
| Plugin URL  : http://www.lucagrandicelli.com/special-recent-posts-plugin-for-wordpress/
| ----------------------------------------------------
*/

/*
| ---------------------------------------------
| INCLUDES
| ---------------------------------------------
*/
require_once './phpthumb/ThumbLib.inc.php';

// Check for image file.
$fileName = (isset($_GET['file'])) ? base64_decode($_GET['file']) : null;

// Try & Catch block to handle PHP Thumb class initialization.
try {
	// Initialize PHP Thumb Class.
	$thumb = PhpThumbFactory::create($fileName);
}
catch (Exception $e) {

	// Handling errors.
	echo $e->getMessage();
}

/*
| ---------------------------------------------
| IMAGE PROCESS
| ---------------------------------------------
*/

// Resize thumbnail with adaptive mode.
$thumb->adaptiveResize($_GET["width"], $_GET["height"]);

// Check for rotation value.
if (isset($_GET["rotation"])) {

	// Check for display mode.
	switch($_GET["rotation"]) {
		case "no":
		break;
		
		case "rotate-cw":
			$thumb->rotateImage('CW');
		break;
		
		case "rotate-ccw":
			$thumb->rotateImage('CCW');
		break;
	}
}

// Output generated thumbnail.
$thumb->show();
