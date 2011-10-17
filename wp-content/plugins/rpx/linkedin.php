<?php
include_once('OAuth.php');
require_once('linkedin_3.1.1.class.php');

function update_linkedin_information($user_id){ 
	$api_key = get_option(RPX_API_KEY_OPTION);
	$identifier = get_user_meta($user_id, 'rpx_identifier', true);

	// TALK to Janrain
    $rpx_post_array = array('apiKey' => $api_key, 'identifier' => $identifier,'format' => 'json');
    $rpx_reply = rpx_post(RPX_URL_SCHEME.RPX_SERVER.'/api/v2/get_user_data', $rpx_post_array);
	$decoded_reply = json_decode($rpx_reply, true);	
	
	$linkedin_api_key = "Dg-ceLXDQkfYXCMSq0qMepebbKtPqvsWAwjiWnAuFfxirtSdtTxKrVrCTHFoI_g5"; // found in janrain.com
	$linkedin_secret_key = "pbrp6maSABfHYFEWK_5PZZmlCQk1-Gcyt7s1Y_dUpPq8KsYVPBv29cLX3Nxfo6Ve";
		
	$user_token = $decoded_reply['accessCredentials']['oauthToken'];
	$user_secret = $decoded_reply['accessCredentials']['oauthTokenSecret'];
	
	
	// Talk to Linkedin API
	$API_CONFIG = array(
		'appKey'       => $linkedin_api_key,
		'appSecret'    => $linkedin_secret_key,
	  'callbackUrl'  => NULL 
	);
	
	//session_start();
    $OBJ_linkedin = new LinkedIn($API_CONFIG);

	//$_SESSION['oauth']['linkedin']['authorized'] = (isset($_SESSION['oauth']['linkedin']['authorized'])) ? $_SESSION['oauth']['linkedin']['authorized'] : FALSE;
			
	 // if($_SESSION['oauth']['linkedin']['authorized'] === TRUE) { // if authorization is granted
            $OBJ_linkedin->setTokenAccess(array('oauth_token' => $user_token, 'oauth_token_secret' => $user_secret));
			 $response = $OBJ_linkedin->profile('~:(first-name,last-name,headline,twitter-accounts,member-url-resources)');
	

		if($response['success'] === TRUE) { // if we receive a valid response
			$xml = simplexml_load_string($response['linkedin']);
			$json = json_encode($xml);
			$response_array = json_decode($json,TRUE); // xml -> array

		
			$user_bio = $response_array['headline'];
			$user_websites = $response_array['member-url-resources']['@attributes']['total'];
			if($user_websites > 1) $user_website = $response_array['member-url-resources']['member-url'][0]['url']; // if more than one website registered, select the first one
			else $user_website = $response_array['member-url-resources']['member-url']['url']; // only one website is registered, select it
			
			$user_twitters = $response_array['twitter-accounts']['@attributes']['total'];
			if($user_twitters > 1) $user_twitter = $response_array['twitter-accounts']['twitter-account'][0]['provider-account-name']; // if more than one twitter account is registered, select first one
			else $user_twitter = $response_array['twitter-accounts']['twitter-account']['provider-account-name']; // onle one twitter account is specified, select it
				
			$user_twitter_url = 'https://twitter.com/'.$user_twitter; // format twitter url
			// Update User meta
			if(!empty($user_bio)) xprofile_set_field_data('One-Line Bio' ,$user_id, $user_bio ); 
			if(!empty($user_twitter_url)) xprofile_set_field_data('Twitter' ,$user_id, $user_twitter_url);
			if(!empty($user_website)) xprofile_set_field_data('Website' ,$user_id, $user_website);	
	
		   } // if $response['success'] end
			 
     //  } // if $_SESSION['oauth'].. end
} // function update_linkedin_information end


?>