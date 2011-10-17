
<?php
	if ( post_password_required() ) :
		echo '<h3 class="comments-header">' . __('Password Protected', 'buddypress') . '</h3>';
		echo '<p class="alert password-protected">' . __('Enter the password to view comments.', 'buddypress') . '</p>';
		return;
	endif;

	if ( is_page() && !have_comments() && !comments_open() && !pings_open() )
		return;
?>

<?php
global $post;

?>

<?php
$categories = get_the_category($post->ID);
foreach ($categories as $category) {

	if (($category->cat_name == "Blog") ||
            ($category->cat_name == "Resources") ||
            ($category->category_parent == '25' )) {

		$say_comments = true;
		if ($category->cat_name == "Resources") { $say_resource = true; }
	}
}
?>
<div id="comments">

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
		<h1>Answers:</h1>
	<?php } else { ?>
		<h1>Weigh In:</h1>
		<?php if ($say_resource) { ?><div class="resource-disclaimer">Check out what’s here, offer your comments on what you see. When you do post a comment, the JA team will invite the people behind the resource to connect back with you, responding in line to your comment. Conversation and connection made easy.</div>
	<?php } } ?>
	
	<h3 id="comments">
	<?php
		if (!$say_comments) {
			printf( _n( 'This question has <strong>one</strong> answer so far.', 'This question has <strong>%1$s</strong> answers so far.', $numComments, 'buddypress' ),
			num2words((int)$numComments), '<em>' . get_the_title() . '</em>' );
		} else {
			printf( _n( 'This post has <strong>one</strong> comment so far.', 'This post has <strong>%1$s</strong> comments so far.', $numComments, 'buddypress' ),
			num2words((int)$numComments), '<em>' . get_the_title() . '</em>' );
		}
	?>
	</h3>

	<?php do_action( 'bp_before_blog_comment_list' ) ?>

	<ol class="commentlist">
		<?php wp_list_comments( array( 'callback' => 'bp_dtheme_blog_comments' ) ); ?>
	</ol><!-- .comment-list -->

	<?php do_action( 'bp_after_blog_comment_list' ) ?>

	<?php if ( get_option( 'page_comments' ) ) : ?>

		<div class="comment-navigation paged-navigation">

		<?php paginate_comments_links(); ?>

		</div>

	<?php endif; ?>

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
	
<?php if ( comments_open() ) : ?>

<div id="respond">
	<div class="comment-avatar-box">
		<div class="avb">
			<a href="<?php echo bp_loggedin_user_domain() ?>">
			<?php 
				echo get_avatar( bp_loggedin_user_id(), 50 ); 
				
				?>
			</a>
		</div>
	</div>

	<div class="comment-content">

	<h3 id="reply" class="comments-header">
		<?php 
			if (!$say_comments) {
				comment_form_title( __( 'Answer this question: ', 'buddypress' ), __( 'Reply to %s: ', 'buddypress' ), true ); echo '<br /><span style="color: #555;">' . get_the_title() . '</span>';
			} else {
				comment_form_title( __( 'Comment on this post', 'buddypress' ), __( 'Reply to %s', 'buddypress' ), true );
			}
		?>
	</h3>


	<p id="cancel-comment-reply">
		<?php cancel_comment_reply_link( __( 'Click here to cancel answer.', 'buddypress' ) ); ?>
	</p>
			
	<?php if ( get_option( 'comment_registration' ) && !$user_ID ) : ?>

			
					
					<?php 
					//printf( __('You must be <a href="#" onclick="showRPX(\'rpxlogin\')" title="Log in">logged in</a> to answer.', 'buddypress'), wp_login_url( get_permalink() ) ); 
					?>
					
					<div class="rpx_button" id="rpx_button_1">
						<div class='comment_connect_text'>To weigh in, connect via</div>
						<div class="rpx_small_icons" id="rpx_small_icons_2" onclick="showRPX('rpxlogin')">
							<div class="rpx_icon rpx_size16 rpx_facebook" title="Facebook"></div>
							<div class="rpx_icon rpx_size16 rpx_google" title="GoogleApps"></div>
							<div class="rpx_icon rpx_size16 rpx_linkedin" title="LinkedIn"></div>
							<div class="rpx_icon rpx_size16 rpx_twitter" title="Twitter"></div>
						</div>
					</div>
				
			

			<?php else : ?>

					<?php do_action( 'bp_before_blog_comment_form' ) ?>



					<?php if (!$user_ID) { ?>
                    <div class="comment-connect clearfix">Got an account with one of these?<br />Login here to comment.

                        <?php echo rpx_small_buttons(); ?>
                    </div>

                    <?php } else if ( $user_ID ) : ?>

                    <form action="<?php echo site_url( 'wp-comments-post.php' ) ?>" method="post" id="commentform" class="standard-form">

					<p class="log-in-out">
					<?php printf( __('Logged in as <a href="%1$s" title="%2$s">%2$s</a>.', 'buddypress'), bp_loggedin_user_domain(), $user_identity ); ?> <a href="<?php echo wp_logout_url( get_permalink() ); ?>" title="<?php _e('Log out of this account', 'buddypress'); ?>"><?php _e('Log out &rarr;', 'buddypress'); ?></a>
					</p>


						<p class="form-textarea">
							<textarea name="comment" id="comment" cols="60" rows="10" tabindex="4"></textarea>
						</p>
						
						
						<div id="comment_counter"> 
						<span id="jp_msg_span"></span>
							<div id="comment_charaters">
								Characters remaining: 
								<span id="jp_limit_span"></span>
							</div>
						</div>
						
						
	
						<?php do_action( 'bp_blog_comment_form' ) ?>

						<p class="form-submit">
							<input class="submit-comment button" name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit', 'buddypress'); ?>" />
							<?php comment_id_fields(); ?>
						</p>

						<div class="comment-action">
							<?php do_action( 'comment_form', $post->ID ); ?>
						</div>

					</form>

					<?php endif; ?>

					<?php do_action( 'bp_after_blog_comment_form' ) ?>

				<?php endif; ?>

			</div><!-- .comment-content -->
		</div><!-- #respond -->

		<?php endif; ?>
