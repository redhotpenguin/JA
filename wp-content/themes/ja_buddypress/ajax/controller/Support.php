<?php
/* Support */

class Support{
	const PROVIDER_NOT_FOUND = '<span class="support_warning">Sorry, there are no providers linked to this account. Please <a target="_blank"  href="mailto:support@journalismaccelerator.com?subject=Authentification Problem, No Janrain Account linked to this username">contact us</a> for further assistance.</span>';
	const USER_NOT_FOUND = '<span class="support_fail">Sorry, no member found with this email address. Please check the spelling of the email or please <a target="_blank" href="mailto:support@journalismaccelerator.com?subject=Authentification Problem, No Janrain Account linked to this email">contact us</a> for further assistance.</span>';
	const USER_FOUND = '<span class="support_success">Success! We have sent you an email with your account information.</span>';
	const USER_VALID = '<span class="support_success">Success! We have sent you an email with your account information to the email address associated with this username.</span>';
	const USERNAME_NOT_FOUND = '<span class="support_fail">Sorry, no member found with this username. Please check the spelling of the username or please <a target="_blank" href="mailto:support@journalismaccelerator.com?subject=Authentification Problem, No Janrain Account for this username">contact us</a> for further assistance.</span>';
	const EMAIL_SUBJECT = 'Account Information for Journalism Accelerator';
	const EMAIL_CONTENT = 'Hi %s, <br/>
		<p>Please find below your Journalism Accelerator account information:</p>
		<p>You are connected to the JA with <b>%s</b> and the email address <strong>%s</strong>.</p> 
		<p>Here is the link to your JA profile: %s </p>
		<p>Please don\'t hesitate to contact us at support@journalismaccelerator.com if you need further assistance.</p>
		';
	
	public function __construct(){

	}
	
	public function recover_provider($params){
		extract(  $params ) ;
		/*
			avalaible variables: 
			$type: should be 'email' or 'username'
			$value: should be an email address or a username
		*/
				
		if($type== 'email'){ // retrieve account info by email
			$email = sanitize_email($value); // sanitize email
			$user_id = get_user_id_from_string($email);
				
			/* make sure we have actually received a valid email address*/
				if( !$this->_validate_email($email) ){
					error_log("Provider Recovery invalid email:". $email );
					return false;
				}
					
					
				if( $user_id ){
					$provider = $this->_get_provider( $user_id );
					if( $provider){
						$this->_send_recovery_email($email, $provider, $user_id);
						echo Support::USER_FOUND;
					}
					else
						echo Support::PROVIDER_NOT_FOUND;	
				}
				else{
					echo Support::USER_NOT_FOUND;
				}

		}elseif($type == 'username'){ // retrieve account info by username
			$value = sanitize_user($value); // sanitize username
			
			$user = get_user_by('login', $value);
			if( !$user ){ // username not found in the database
				echo Support::USERNAME_NOT_FOUND;
			}
			else{
				$provider = $this->_get_provider( $user->ID );
				if($provider){
					$this->_send_recovery_email($user->user_email, $provider, $user->ID);
					echo Support::USER_VALID;
				}else{
					echo Support::PROVIDER_NOT_FOUND;
				}
			}
			
		}else{
			return false;
		}


	}
	
	private function _get_provider( $user_id ){
		if( $provider = get_user_meta($user_id, 'rpx_provider', true) ){
			return $provider;
		}
		else
			return false;
	}
	
	private function _send_recovery_email( $email_address, $provider, $user_id){
		$profile_link = get_link_to_public_profile($user_id);
		$profile_link = "<a href='$profile_link'>$profile_link</a>";
		$name = get_display_name($user_id);
		$email_content =  sprintf(Support::EMAIL_CONTENT, $name,$provider,  $email_address, $profile_link);
		
		$support_email = 'support@journalismaccelerator.com';
		$headers = array("From: Journalism Accelerator <noreply@www.journalismaccelerator.com>",  "Content-Type: text/html", "Bcc: ". $support_email );
		$headers = implode("\r\n",$headers) . "\r\n";
		return wp_mail( $email_address, Support::EMAIL_SUBJECT,  $email_content, $headers );
	}
	
	private function _validate_email($email) { 
		$pattern = '/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
		return preg_match($pattern, $email);
	} 

}