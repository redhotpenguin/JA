<?php
/*
Plugin Name: JA Comment Shortcode
Plugin URI: 
Description: Embed comments with a shortcode
Version: 0.1
Author: Jonas Palmero
Author URI: http://www.twitter.com/welldonejonas
*/

add_action('wp_enqueue_scripts', 'ja_comment_load_scripts');
function ja_comment_load_scripts(){
	 $css_url = plugins_url('css/ja_comment.css' , __FILE__ );
	 wp_register_style('ja-comment', $css_url);
	 wp_enqueue_style('ja-comment');
}

function ja_comment_add_button() {  
   if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )  
   {  
    add_filter('mce_external_plugins', 'ja_comment_add_plugin');  
    add_filter('mce_buttons', 'ja_comment_register_button');  
   }  
}
add_action('init', 'ja_comment_add_button');


function ja_comment_register_button($buttons) {  
   array_push($buttons, "ja_comment");  
   return $buttons;  
}

function ja_comment_add_plugin($plugin_array) {  
   $script_url = plugins_url('js/ja_comment.js' , __FILE__ ); 
   $plugin_array['ja_comment'] = $script_url;  
   return $plugin_array;  
}  

// SHORTCODE: [ja_comment comment_id = '123']  abcdef [ja_comment]

function do_ja_comment( $atts, $content = '' ){
	extract( shortcode_atts( array(
		'comment_id' => '',
		'extra_info' => '',
		'author_name' => '',
		'avatar_url' => '',
	), $atts ) );
	
	$plugin_url = plugins_url().'/comment_shortcode';
	

	$comment = get_comment($comment_id);
	
	if( !is_object($comment) )
		return false;
		
	$user_id = $comment->user_id;
	
	if( !empty($avatar_url) ){
		$avatar = "<img src='$avatar_url' class='ja_avatar' style='width:50px; height:50px;' />";
	}
	else
		$avatar = custom_get_avatar($user_id, array("class" =>"ja_avatar", "width" => "50", "height" => "50") );
	
	if( empty($author_name) )
		$author_name = $comment->comment_author;
		
	if( !empty( $extra_info) )
		$extra_info = "<div class='ja_comment_extra'>$extra_info</div>";
		
	$user_bio = xprofile_get_field_data( 'One-Line Bio' , $user_id );
	
	$comment_link = get_comment_link( $comment_id );
	
	$author_profile_link = get_link_to_public_profile( $user_id );
	
	$comment_date = get_comment_date('d M y', $comment_id ) ;
	$post_id = $comment->comment_post_ID;
	$post_title = get_the_title($post_id);
		
	$html_comment = "<div class = 'ja_comment_wrap'>
				
				<div class='ja_comment_meta clearfix'>
					<div class='ja_comment_meta_avatar'><a title='$author_name' href='$author_profile_link'>$avatar</a></div>
					<div class='ja_comment_meta_text'>
						<div class='ja_comment_name'><a href='$author_profile_link' title='View Profile'>$author_name</a></div>
						
						<div class='ja_comment_view_comment_btn'>
							<a href='$comment_link' title=\"View $author_name's comment on $post_title\">
								<span class='ja_comment_btn_icon'>	
									<img src='$plugin_url/img/ja_14.png'/>
								</span>
								<span class='ja_comment_btn_label'>	
									View Comment
								</span>
							</a>
						</div>
						<div class='clearfix'></div>
						<div class='ja_comment_bio'>$user_bio</div>
						
					
						
						$extra_info
					</div>
				</div>
				
				<div class='ja_comment_content'>$content</div>
				
				<div class='ja_comment_date'> <a href='$comment_link'> $comment_date </a> </div>
				
			</div>";
	
	return $html_comment;
}
add_shortcode( 'ja_comment', 'do_ja_comment' );


?>