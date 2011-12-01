<?php
class Ping_Highrise_Core{
	private $hr_url;
	
	public function __construct($highrise_url){
		$this->hr_url = $highrise_url;
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
		else{ return false; }
	}
	
}

?>