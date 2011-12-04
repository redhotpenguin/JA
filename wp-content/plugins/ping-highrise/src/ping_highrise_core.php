<?php
class Ping_Highrise_Core{

	
	public function __construct(){
		
	}
	
	public function make_request( $url, $body=array() ){ // make asynchronous HTTP POST REQUEST
		$res = @wp_remote_post($url,
			array(
					'method' => 'POST',
					'timeout' => '0.1',
					'blocking' => false,
					'body' => $body
				)
			);
					
		if($res){ return true; }
		else{ 
			ph_log("Ping Highrise Core: Posting to $url Failed");
			return false; 
		}
	}
	
}

?>