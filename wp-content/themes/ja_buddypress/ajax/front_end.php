<?php
/* 
	Ajax Controller
	This file is executed by wordpress when ajax action is set to 'frontend'
	The ajax Hook is defined in functions.php
*/

$controller_name = $_REQUEST['controller_name'];
$file_requested = dirname ( __FILE__ ).'/controller/'.$controller_name.'.php';

if ( file_exists( $file_requested) ){
	include $file_requested;
	$controller = new $controller_name();
	if(isset ($_REQUEST['controller_action'])){
		$action = $_REQUEST['controller_action'];
		
		$controller->$action( $_REQUEST['action_params']  );
	}
	
}
else{
	error_log('Controller not implemented: '.$file_requested);
}