<?php

function update_facebook_information($user_id) {

	// Talk to Janrain (borrowed from twitter.php)
	$api_key = get_option(RPX_API_KEY_OPTION);

	$identifier = get_user_meta($user_id, 'rpx_identifier', true);

	$rpx_post_array = array('apiKey' => $api_key,
				'identifier' => $identifier,
				'format' => 'json');
 
	$rpx_reply = rpx_post(RPX_URL_SCHEME.RPX_SERVER.'/api/v2/get_user_data', $rpx_post_array);
	$decoded_reply = json_decode($rpx_reply, true);	

//	log_error(var_dump($decoded_reply));
//	echo(var_dump($decoded_reply));
    

    $at = $decoded_reply['accessCredentials']['accessToken'];
    $uid = $decoded_reply['accessCredentials']['uid'];

    $url = "https://graph.facebook.com/$uid?access_token=$at";

    $stuff = @file_get_contents($url);

  //  print_r($stuff);

    $decoded_json = json_decode($stuff);
  
    $username = $decoded_json->username;
    $bio = $decoded_json->bio;
	$website = $decoded_json->link;


    xprofile_set_field_data('Website' ,$user_id, $website);

}

?>
