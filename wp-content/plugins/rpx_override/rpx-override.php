<?php
/*
Plugin Name: RPX OVERRIDE
Plugin URI: 
Description: Custom functions for a full integration of Janrain.
Version: 1.0
Author: JA DEV TEAM
Author URI: 
*/
require_once(RPX_PATH_ROOT . '/rpx_c.php');

function custom_get_avatar($user_id, $params){
	$avatar_url = get_usermeta($user_id, 'rpx_photo');
	if(empty($avatar_url))
		$avatar_url = 'http://1.gravatar.com/avatar/f272a071bda1c80e031432e4caf517ac?s=100';

	$width = $params['width'];
	$height = $params['height'];
	$class = $params['class'];
	$alt = $params['alt'];
	$title = $params['title'];

	if(is_numeric($width))
		$width = 'width:'.$width.'px;';
	else 
		$width = 'width:auto;';
		
	if(is_numeric($height))
		$height = 'height:'.$height.'px;';
	else 
		$height = 'height:auto;';
		
	$style = $width.$height;
	
	if( !empty($class) )
		$class = 'class= "'.$class.'"';
		
	if( !empty($title) )
		$title = 'title= "'.$title.'"';
		if( !empty($alt) )
		$alt = 'alt= "'.$alt.'"';
	
		
	$img_meta = "{$class} {$title} {$alt} ";
	$avatar = "<img src={$avatar_url} style={$style} $img_meta/>";
	
	
	return $avatar;
}

if ( !function_exists( 'get_avatar' ) ) :
/**
 * Retrieve the avatar for a user who provided a user ID or email address.
 *
 * @since 2.5  - upgraded by JA TEAM on 08/25/2011
 * @param int|string|object $id_or_email A user ID,  email address, or comment object
 * @param int $size Size of the avatar image
 * @param string $default URL to a default image to use if no avatar is available
 * @param string $alt Alternate text to use in image tag. Defaults to blank
 * @return string <img> tag for the user's avatar
*/

function get_avatar( $id_or_email, $size = '96', $default = '', $alt = false ) {
	if(is_object($id_or_email)){ 
		$id_or_email = $id_or_email->user_id;
	}

	if ( false === $alt)
		$safe_alt = '';
	else
		$safe_alt = esc_attr( $alt );

	if ( !is_numeric($size) )
		$size = '96';

	$email = '';

    $id = (int) $id_or_email;

    $photo_url = rpx_get_buddy_photo($id);
	$upload_dir = wp_upload_dir();
	$upload_dir = $upload_dir['basedir'].'/avatars/';
	
    if (!$photo_url) { // we don't have a janrain photo entry, try building a gravatar
		
        $user = get_userdata($id);
        if ( $user ) {
            // build the gravatar link from the email
             $email = $user->user_email;

            $email_hash = md5( strtolower( $email ) );
           
			$photo_url = sprintf( "http://%d.gravatar.com", ( hexdec( $email_hash[0] ) % 2 ) );
            $photo_url = "$photo_url/avatar/$email_hash?s=$size";

        } else {
            // no user, use the default
	
            $photo_url = 'http://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=50';
        }
		
		//$photo_url = 'http://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=50';
    }


	$avatar = "<img alt='{$safe_alt}' src='{$photo_url}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";

	
   return $avatar;
}
endif;

/*
	Function: get_user_id_from_string
	Description: Return an user id based on an email(copied from includes/ms-functions.php)
	Param: $string (email@)
	Note: Used in functions.php
*/
function get_user_id_from_string($string) {
	$user_id = 0;
	if ( is_email( $string ) ) {
		$user = get_user_by('email', $string);
		if ( $user )
			$user_id = $user->ID;
	} elseif ( is_numeric( $string ) ) {
		$user_id = $string;
	} else {
		$user = get_user_by('login', $string);
		if ( $user )
			$user_id = $user->ID;
	}

	return $user_id;
}

/*
	Function: rpx_get_comment_reply_link
	Description: Display the link 'Log in to Reply' and display the Janrain login form
	Note: Used in functions.php
*/
function rpx_get_comment_reply_link($args = array(), $comment = null, $post = null) {
	global $user_ID;
	$defaults = array('add_below' => 'comment', 'respond_id' => 'respond', 'reply_text' => __('Reply'),
		'login_text' => __('Log in to Reply'), 'depth' => 0, 'before' => '', 'after' => '');

	$args = wp_parse_args($args, $defaults);

	if ( 0 == $args['depth'] || $args['max_depth'] <= $args['depth'] )
		return;
	extract($args, EXTR_SKIP);
	$comment = get_comment($comment);
	if ( empty($post) )
		$post = $comment->comment_post_ID;
	$post = get_post($post);

	if ( !comments_open($post->ID) )
		return false;

	$link = '';

	if ( get_option('comment_registration') && !$user_ID ){
		$link = '<a class="login-reply-link" href="#" onclick="showRPX(\'rpxlogin\', '.$comment->comment_ID.' )">'.$login_text.'</a>';
		}
	else
		$link = "<a class='comment-reply-link' href='" . esc_url( add_query_arg( 'replytocom', $comment->comment_ID ) ) . "#" . $respond_id . "' onclick='return addComment.moveForm(\"$add_below-$comment->comment_ID\", \"$comment->comment_ID\", \"$respond_id\", \"$post->ID\")'>$reply_text</a>";
	return apply_filters('comment_reply_link', $before . $link . $after, $args, $comment, $post);
}


/*
	Function: rpx_upload_avatar
	Description: Upload the rpx_photo meta key after an image is uploaded.
*/
function rpx_upload_avatar(){ 
	global $bp;
	if(!empty($_POST['avatar-crop-submit']) && $bp->template_message_type === "success"){
		$current_user = wp_get_current_user();
		$id = $current_user->ID;

		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['basedir'].'/avatars/'.$id.'/';

		$search_result = glob($upload_dir.'*-bpfull.jpg'); // search in the directory a file that ends with bpfull.jpg
		$file_name = $search_result[0]; // result is in an array and is the first index
		$file_name = pathinfo($file_name); // extract the filename
		$file_name = $file_name['basename'];
		$img_path = "/wp-content/uploads/avatars/$id/$file_name";
	
		if(!empty($img_path)) update_user_meta($id, 'rpx_photo', "$img_path"); // update table
		$username = $current_user->user_login;
		$site = site_url();
		wp_redirect("$site/members/$username/profile/change-avatar/");
	}
}
add_action('xprofile_screen_change_avatar','rpx_upload_avatar');





/* HELPER FUNCTIONS */

/*
	Function: get_link_to_public_profile
	Description: Return the link to the public profile
	Param: Email Or User ID
	Example:  http://jadev.redhotpenguin.com/members/welldonejonas/
*/
function get_link_to_public_profile($idOrEmail){
			if(is_string($idOrEmail)) $id =  get_user_id_from_string($idOrEmail);
			else $id = $idOrEmail;
			$site_url = get_site_url();
			
			$user_login = bp_core_get_username($id);
			if(!$user_login) return false;
			else{
				$url = $site_url."/members/$user_login/";
				return $url;
			}
}


/*
	Function: comment_author_profile
	Description: echo the member profile's url
	Param: comment object
	Example: display <a href="http://jadev.redhotpenguin.com/members/welldonejonas/">Jonas P. </a>
*/
function comment_author_profile($comment){
	if(is_object($comment)){
		$email = $comment->comment_author_email;
		$name = $comment->comment_author;
		$link = get_link_to_public_profile($email);
		if($link){
			echo "<a href='$link'>$name</a>";
		}
		elseif($comment->comment_author_url){
			echo "<a href='$comment->comment_author_url'>$name</a>";
		}
		else{
			echo $comment->comment_author;
		}
		return true;
	 }
	 else return false;
}

function get_comment_author_profile($comment){
	if(is_object($comment)){
		$email = $comment->comment_author_email;
		$name = $comment->comment_author;
		$link = get_link_to_public_profile($email);
		if($link){
			return $link;
		}
		elseif($comment->comment_author_url){
			return $comment->comment_author_url;
		}
		else{
			return $comment->comment_author;
		}
	
	 }
	 else return false;
}

/*
	Function: get_display_name
	Description: Return the display name 
	Param: Email Or User ID
	Example:  Jonas Palmero (instead of jonas.palmero)
*/
function get_display_name($idOrEmail){
			if(is_string($idOrEmail)) $id =  get_user_id_from_string($idOrEmail);
			else $id = $idOrEmail;
			
			if($user = get_userdata($id)){
				return $user->display_name;
			}
			else return false;
}

/*
	Function: get_edit_link
	Description: Return a link to the edit profile page
	Param: User ID
	Example:  /members/jonas-palmero/profile/edit/group/1/
*/
function get_edit_link($id){
	$user_login = bp_core_get_username($id);
	if($user_login) return $url  = "/members/$user_login/profile/edit/group/1/";
	else return false;
}
/* HELPER FUNCTIONS END */


// AUTOMATICALLY UPDATE USER'S FEED AFTER HIS COMMENT IS EDITED
function update_activity_stream_from_comment_edit($comment_id){
	$comment = get_comment($comment_id);

	$comment_author = $comment->comment_author;
	$comment_post_id = $comment->comment_post_ID;
	$comment_content = $comment->comment_content;
	$comment_author_email = $comment->comment_author_email;
	$comment_author_url = $comment->comment_author_url;
	
	$user_id = get_user_id_from_string($comment_author_email);
	$permalink = get_permalink($comment_post_id);

	$comment_date = get_comment_date('Y-m-d G:m:s', $comment_id);
	$comment_date = get_gmt_from_date($comment_date);
	
	$action = "<a href='$comment_author_url'>$comment_author</a> commented on $permalink";
		
	bp_activity_add( array(
		'user_id' => $user_id,
		'component' => 'blogs',
		'type' => 'new_blog_comment',
		'action' => $action,
		'primary_link'=> $permalink."#comment-$comment_id",
		'content' => $comment_content,
		'recorded_time' => $comment_date 
	) );
	
	return $comment_id;
}
add_filter('edit_comment', 'update_activity_stream_from_comment_edit');

/*
	Function: get_twitter_handle
	Description: Return the twitter handle
	Param: Any likely twitter links http://www.twitter.com/welldonejonas
	Example:  http://www.twitter.com/welldonejonas -> welldonejonas 
*/
function get_twitter_handle($has_twitter){

	$pieces = explode('/', $has_twitter);

	if (sizeof($pieces) == 5) {
	
		# https://twitter.com/#!/redhotpenguin
		$has_twitter = $pieces[4];

	} else if (sizeof($pieces) == 4) { 

		// http://twitter.com/redhotpenguin
		$has_twitter=$pieces[3];

	} else if (sizeof($pieces) == 2) {

		// twitter.com/redhotpenguin
		// no http:// on twitter link.
		$has_twitter=$pieces[1];

	} else {
		$pieces = explode('@', $has_twitter);
		if (sizeof($pieces) == 2) {
			$has_twitter = $pieces[1];

		} else {
			// use what they entered
		}
	}
	if ($has_twitter) {
		$pieces = explode('"', $has_twitter);
		$has_twitter=$pieces[0];
	}

	return $has_twitter;
}


/* 
	* Try to steal someone's picture when they provide a twitter link 
	* Get executed everytime someone modify its profile 
*/
function get_avatar_from_twitter($user_id){
		$user_avatar =  get_user_meta($user_id, 'rpx_photo', true);
		
		if($user_avatar) return false; // user already has an avatar, let's not override it
	
		$twitter_link = bp_get_profile_field_data('field=Twitter');
		if(!$twitter_link) return false; // no twitter found
		
		$twitter_handle = get_twitter_handle($twitter_link);
		$new_avatar_url = 'http://api.twitter.com/1/users/profile_image?screen_name='.$twitter_handle.'&size=normal';
		
		
		// test if $new_avatar_url is a valid link.
		$request_headers = get_headers($new_avatar_url, 1); 
		$request_status = $request_headers[0];
		if($request_status == 'HTTP/1.0 404 Not Found') return false;
		
	    if(update_user_meta($user_id, 'rpx_photo' , $new_avatar_url)) return true;
		else return false;
	
}
add_action('xprofile_updated_profile','get_avatar_from_twitter');

?>