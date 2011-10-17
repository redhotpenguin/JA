<?php 
include_once('tmhUtilities.php');
include_once('tmhOauth.php');

function update_twitter_information($user_id){
	
	// Talk to Janrain
	$api_key = get_option(RPX_API_KEY_OPTION);

	$identifier = get_user_meta($user_id, 'rpx_identifier', true);
    $rpx_post_array = array('apiKey' => $api_key, 'identifier' => $identifier,'format' => 'json');
 
    $rpx_reply = rpx_post(RPX_URL_SCHEME.RPX_SERVER.'/api/v2/get_user_data', $rpx_post_array);
	$decoded_reply = json_decode($rpx_reply, true);	

	
	$twitter_username = $decoded_reply['profile']['preferredUsername'];
	$user_key = $decoded_reply['accessCredentials']['oauthToken'];
	$user_secret = $decoded_reply['accessCredentials']['oauthTokenSecret'];
	
	$consumer_key = '7TOzNFDTVypjyBAPfuzs9w'; // found in janrain.com
	$consumer_secret = 'M3tnou71b3CYRKoKrqnUu4dZufeTtphlDFYRr9oVXmA';
	
	// Talk to Twitter's API
	$tmhOAuth = new tmhOAuth(array(
		'consumer_key'    => $consumer_key,
		'consumer_secret' => $consumer_secret,
		'user_token'      => $user_key,
		'user_secret'     => $user_secret,
	));
	
	$code = $tmhOAuth->request('GET', $tmhOAuth->url('1/account/verify_credentials'));
	
	if($code === 200){
		$response = $tmhOAuth->response;
		$json_response = $response['response'];

		$decoded_response = json_decode($json_response, true);
		$user_screen_name = $decoded_response['screen_name'];
		$user_bio = $decoded_response['description'];
		$user_website = $decoded_response['url'];

	$user_twitter_url = 'https://twitter.com/'.$user_screen_name;
	
	if(!empty($user_bio)) xprofile_set_field_data('One-Line Bio' ,$user_id, $user_bio );
	if(!empty($user_twitter_url)) xprofile_set_field_data('Twitter' ,$user_id, $user_twitter_url);
	if(!empty($user_website)) xprofile_set_field_data('Website' ,$user_id, $user_website);	
	}
}
?>