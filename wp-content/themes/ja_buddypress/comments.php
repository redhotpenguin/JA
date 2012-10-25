<?php

	global $post;
	
	if ( post_password_required() ) :
		echo '<h3 class="comments-header">' . __('Password Protected', 'buddypress') . '</h3>';
		echo '<p class="alert password-protected">' . __('Enter the password to view comments.', 'buddypress') . '</p>';
		return;
	endif;

	if ( is_page() && !have_comments() && !comments_open() && !pings_open() )
		return;

$categories = get_the_category($post->ID);
foreach ($categories as $category) {

	if (($category->cat_name == "Blog") ||
            ($category->cat_name == "Resources") ||
            ($category->category_parent == '25' )) {

		$say_comments = true;
		if ($category->cat_name == "Resources") { $say_resource = true; }
	}
}

	if( function_exists('cflow_get_post_top_comment_ids') && function_exists('get_comments_n_replies') ){
			$default_thread_number = get_option('cflow_initial_threads_loaded');
			$top_comment_ids = cflow_get_post_top_comment_ids( $post->ID, $default_thread_number );
	}


?>

<!-- 
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$("#comments-area-tabs").organicTabs({
		"speed": 200
		});
	});
</script> 
 -->

<a name="standard-flow"></a><a name="sequential"></a><a name="participants"></a>
<div id="comments">
<div id="comments-area-tabs">
<div id="commentwrapper" class="stickypanel">
<div id="commentwrapperpad">
<?php			
// Only include comments
$numTrackBacks = 0; $numComments = 0;
foreach ( (array)$comments as $comment )

	if ( 'comment' != get_comment_type() )
		$numTrackBacks++; 
	else
		$numComments++;
?>
	
	<?php if ( !$say_comments ) { ?>
		<h1>Answers: <span class="smallgrey">Remember to <a href="<?php get_permalink(); ?>" class="refresh_the_page">refresh often</a> to see latest comments!</span></h1> 
	<?php } else { ?>
		<h1>Weigh In: <span class="smallgrey">Remember to <a href="<?php get_permalink(); ?>" class="refresh_the_page">refresh often</a> to see latest comments!</span></h1>
	<?php } ?>
	
	<div class="comment_post_info clearfix">
	<p id="comments">
	<?php
		if (!$say_comments) {
			printf( _n( '<strong class="cflow_counter" >1</strong> answer so far.', '<strong class="cflow_counter">%1$s</strong> answers so far.', $numComments, 'buddypress' ),
			$numComments, '<em>' . get_the_title() . '</em>' );
		} else {
			printf( _n( '<strong class="cflow_counter" >1</strong> comment so far.', '<strong class="cflow_counter">%1$s</strong> comments so far.', $numComments, 'buddypress' ),
			$numComments, '<em>' . get_the_title() . '</em>' );
		}
		$default_thread_n = get_option('cflow_initial_threads_loaded');
		
		echo '<div class="post_info_show_all">';
		echo 'Show All: <a href="'.get_permalink().'" class="show_all_comments">Comments</a>';
		
		if($numComments >=3)
			 echo ' | <a class="comments_grid_link" href="'.get_permalink().'./participants"> Participants</a>'; 
		echo '</div>';	
		
		echo '<div class="post_info_feed">';
		echo '<a class="post_feed_link" href='.get_post_comments_feed_link().'><img src="/feed.png" title="Comment Feed for '.get_the_title().'" alt="Comment Feed for '.get_the_title().'"/></a>Comment Feed';
		echo '</div>';
	?>
	
	</p>
</div>
	
		<?php if ($say_resource) { ?><div class="resource-disclaimer">Check out what's here, offer your comments on what you see. When you do post a comment, the JA team will invite the people behind the resource to connect back with you, responding in line to your comment. Conversation and connection made easy.</div>
	<?php }  ?>
	
	<?php do_action( 'bp_before_blog_comment_list' ) ?>

	<?php if ( comments_open() ) : ?>

<div id="respond">

	<div class="comment-content clearfix">

	<h3 id="reply" class="comments-header"> </h3>
	
	<?php if ( get_option( 'comment_registration' ) && !$user_ID ) : ?>

					<div class="clearfix"></div>

					<div class="rpx_button" id="rpx_button_1">
						<div class='comment_connect_text'>To weigh in, connect via</div>
<!-- 
						<div class="rpx_small_icons" id="rpx_small_icons_2" onclick="showRPX('rpxlogin')">
							<div class="rpx_icon  rpx_facebook" title="Facebook"></div>
							<div class="rpx_icon  rpx_google" title="GoogleApps"></div>
							<div class="rpx_icon  rpx_linkedin" title="LinkedIn"></div>
							<div class="rpx_icon  rpx_twitter" title="Twitter"></div>
						</div>
 -->

<div class="rpx_button">
<div class="rpx_small_icons rpxsharebutton" onclick="showRPX('rpxlogin')"> <img title="Google" class="rpx_icon" src="<?php $bloginfo = get_bloginfo( 'wpurl' ); ?>/wp-content/plugins/rpx/images/google_32.png"> </div>
<div class="rpx_small_icons rpxsharebutton" onclick="showRPX('rpxlogin')"> <img title="Facebook" class="rpx_icon" src="<?php $bloginfo = get_bloginfo( 'wpurl' ); ?>/wp-content/plugins/rpx/images/facebook_32.png"> </div>
<div class="rpx_small_icons rpxsharebutton" onclick="showRPX('rpxlogin')"> <img title="Twitter" class="rpx_icon" src="<?php $bloginfo = get_bloginfo( 'wpurl' ); ?>/wp-content/plugins/rpx/images/twitter_32.png"> </div>
<div class="rpx_small_icons rpxsharebutton" onclick="showRPX('rpxlogin')"> <img title="LinkedIn" class="rpx_icon" src="<?php $bloginfo = get_bloginfo( 'wpurl' ); ?>/wp-content/plugins/rpx/images/linkedin_32.png"> </div>
<div class="rpx_clear"></div>
</div>
								<div id="profile-importance"><a class="fancybox fancybox.iframe" href="<?php $bloginfo = get_bloginfo( 'wpurl' ); ?>/profile.html">How to comment & why create a JA profile!</a></div>
			
			
</div>
			

			<?php else : ?>

					<?php do_action( 'bp_before_blog_comment_form' ) ?>

					<?php if (!$user_ID) { ?>
                    <div class="comment-connect clearfix">Got an account with one of these?<br />Login here to comment.

                        <?php echo rpx_small_buttons(); ?>
                    </div>

                    <?php } else if ( $user_ID ) : ?>

                    <form action="<?php echo site_url( 'wp-comments-post.php' ) ?>" method="post" id="commentform" class="standard-form">

<div id="keepitleft">
					<p class="form-textarea">
						<textarea class="expand60-266" name="comment" id="comment" cols="60" rows="10" tabindex="4"></textarea>
					</p>
					
						
					<?php do_action( 'bp_blog_comment_form' ) ?>
					<p class="form-submit">
						<input class="submit-comment button" name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit', 'buddypress'); ?>" />
						<?php comment_id_fields(); ?>
					</p>
					
</div>
					<p id="cancel-comment-reply">
						<?php cancel_comment_reply_link( __( 'Cancel Answer', 'buddypress' ) ); ?>
					</p>
					
					<div id="comment_counter"> 
						<span id="jp_msg_span"></span>
						<div style="display:none;" id="comment_charaters">
								Characters remaining: 
								<span id="jp_limit_span">0</span>
						</div>
					</div>
					<div class="comment-action">
						<?php do_action( 'comment_form', $post->ID ); ?>
					</div>

					</form>

					<?php endif; ?>

					<?php do_action( 'bp_after_blog_comment_form' ) ?>

				<?php endif; ?>

			</div><!-- .comment-content -->

</div><!-- #respond -->
</div><!-- #commentwrapperpad -->
<!-- 
	<ul class="nav" id="stickybar">
		<li class="nav-one"><a class="current" href="#standard-flow">Comments</a></li>
		<li class="nav-two"><a href="#tweets_about_this">Tweets About This</a></li>
		<li class="nav-three"><a href="#participants">All Participants</a></li>
		<li class="nav-four"><a href="<?php get_permalink(); ?>">Escape</a></li>
	</ul>
 -->

</div> 

		<?php endif; ?>
			
<!-- Wrap for entire comment area -->
	<div class="list-wrap">
	
		<div  id="standard-flow">
			<ol class="commentlist">
				<?php 
				if( function_exists('cflow_get_post_top_comment_ids') && function_exists('get_comments_n_replies') ){
			
					$comments_n_replies = get_comments_n_replies( $top_comment_ids );
					wp_list_comments(
						array( 'callback' => 'bp_dtheme_blog_comments',
						'reverse_top_level' => false,
						'reverse_children' => true, 
						'type' => 'comment',
						'max_depth' => get_option('thread_comments_depth')
					), $comments_n_replies );
					
				} // check if cflow activated end
				else{ // cflow not activated, display comments normally
				wp_list_comments(
					array( 'callback' => 'bp_dtheme_blog_comments', 
					'type' => 'comment',
				) );
				}
		
				?>
					
			</ol><!-- .comment-list -->

		</div>
		
<!-- Commenting out alternative tabs for now		 
		<div id="tweets_about_this" class="hide" style="position: relative; top: 0px; left: 0px; display: none;">

<h3><a class="twitter-timeline" data-dnt=true href="https://twitter.com/search?q=http%3A%2F%2Fwww.journalismaccelerator.com" data-widget-id="260857404831449088">Tweets about "http://www.journalismaccelerator.com"</a></h3>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

http://ispkintrnt.com/blog/experiments-blog/new-post/

</div>


	 
		<div id="participants" class="hide" style="position: relative; top: 0px; left: 0px; display: none;">
<h3>This will be a participant grid.</h3>
		</div>
 -->
		 
</div> <!--	END List Wrap -->
	
</div>

</div><!-- #comments -->


		
<?php // respond used to be here ?>
<?php $numTrackBacks = false; //delete this line to show trackbacks again ?>
<?php if ( $numTrackBacks ) : ?>
	<div id="trackbacks">

	<span class="title"><?php the_title() ?></span>

	<?php if ( 1 == $numTrackBacks ) : ?>
		<h3><?php printf( __( '%d Trackback', 'buddypress' ), $numTrackBacks ) ?></h3>
	<?php else : ?>
		<h3><?php printf( __( '%d Trackbacks', 'buddypress' ), $numTrackBacks ) ?></h3>
	<?php endif; ?>

	<ul id="trackbacklist">
	<?php foreach ( (array)$comments as $comment ) : ?>
	
		<?php if ( get_comment_type() != 'comment' ) : ?>
			<li><h5><?php comment_author_link() ?></h5><em>on <?php comment_date() ?></em></li>
	  	<?php endif; ?>

	<?php endforeach; ?>
	</ul>
	</div>

<?php endif; ?>