<?php

/*
Plugin Name: Better Recent Comments
Plugin URI: http://www.journalismaccelerator.com/
Description: Widetized Recent Comments for  Journalism Accelerator
Author: Jeremy and Fred
Version: 1
*/

function widget_recent_comments($no_comments = 10, $comment_len = 100) {
	global $wpdb;
	$request = "SELECT * FROM $wpdb->comments";
	$request .= " JOIN $wpdb->posts ON ID = comment_post_ID";
	$request .= " WHERE comment_approved = '1' AND post_status = 'publish' AND post_password =''";
	$request .= " ORDER BY comment_date DESC LIMIT 3";
	$comments = $wpdb->get_results($request);
	if ($comments) {

		foreach ($comments as $mycomment) {

			ob_start();

			$user_email = $mycomment->comment_author_email;

		
			$myid = get_user_id_from_string($user_email);
			
			$comment_tweet_avatar   = get_comment_meta($mycomment->comment_ID, 'tmac_image',true);
			if($comment_tweet_avatar) echo "<img src='$comment_tweet_avatar'  width='50px' class='avatar' />";
			else echo get_avatar($myid, 50)
			
			//	echo bp_core_fetch_avatar( array( 'item_id' => $id, 'width' => 32, 'height' => 32, 'email' =>  $myid ) ); 



			?>

		<p class="answer">
<?php
	if ($myid) {
		$email = $mycomment->comment_author_email;
?>
<a href="<?php echo get_link_to_public_profile($email); ?>">
<?php
}
?>
	<?php 
	comment_author_profile($mycomment);
	?>
<?php
	if ($myid) {
?>
</a>
<?php
}
?>

<?php
				echo ' on ' ;
				$category = get_the_category($mycomment->comment_post_ID);
				?>
				<a href="<?php echo get_permalink($mycomment->comment_post_ID); ?>#comment-<?php echo $mycomment->comment_ID; ?>"><?php echo $category[0]->cat_name; ?></a>: <?php
				if (strlen($mycomment->comment_content) > 100) {
					echo strip_tags(substr(apply_filters('get_comment_text', $mycomment->comment_content), 0, 99)) . "...";
				}
				else {
					echo strip_tags($mycomment->comment_content);
				}
				?>
				</p>
				<div class="clear"></div>
			<?php
			ob_end_flush();
		}
	} else {
		echo '<p>Comments not found.</p>';
	}
}

function widget_dp_recent_comments($args) {

  extract($args);
  echo $before_widget;
  echo $before_title;?>Recent Comments<?php echo $after_title;
  widget_recent_comments();
  echo $after_widget;

}

function dp_recent_comments_init()
{
  register_sidebar_widget(__('Better Recent Comments'), 'widget_dp_recent_comments');
}

add_action("plugins_loaded", "dp_recent_comments_init");

?>
