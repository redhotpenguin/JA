<?php

class Ping_Highrise_Business{


	
	public function __construct(){
	
	}
	
	public function new_comment_hook($comment_id, $approved = true){ // executed when a new comment is posted
	
		if( empty( $comment_id ) || !is_numeric( $comment_id ) ){
			ph_log('New Comment Hook - no ID');
			return false;
		}
		
		global $wpdb;

		
	 if( $wpdb->insert('wp_phighrise', 
			array(
				'uc_id' => $comment_id,
				'type'=> 'comment',
				'status' => 'not_processed',
				'code' => 0,
				'time' => current_time('mysql')	
			)
		) )
			return true;
			
		else
			return false;
		
	}

	public function new_user_hook($user_id){ // executed when a user registers
		$post_highrise_url = get_option('post_highrise_url');
		$hr_url = get_option('highrise_url');
		$hr_token= get_option('highrise_token');
		
		$post_body = array(
			'action' => 'new_user',
			'hr_url' =>  $hr_url,
			'hr_token' => $hr_token,
			'user_id' => $user_id
		);
		$this->make_request($post_highrise_url, $post_body);
	}
	
	
	public function make_request( $url, $body=array() ){ // make asynchronous HTTP POST REQUEST
		$res = @wp_remote_post($url,
			array(
					'method' => 'POST',
					'timeout' => '0.5',
					'blocking' => false,
					'body' => $body
				)
			);
			
		if( is_array($res) ){ return true; } // callback required to actually test the response
		else{ 
			ph_log("Ping Highrise Business: Posting to $url Failed");
			return false; 
		}
	}

}

?>