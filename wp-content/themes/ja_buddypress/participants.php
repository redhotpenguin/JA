<?php
/*
Template Name: Participants Grid
*/

get_header(); ?>

	<div id="content">
		<div class="padder">
		<?php do_action( 'bp_before_blog_single_post' ) ?>
		<div class="page" id="blog-single">
			<div class="post-content">
				<?php 
					global $wp_query; 
					global $post;
					if( !isset( $wp_query->query_vars['participants'] )) {
						// need redirection to homepage or something
						} 
					$post_id =  url_to_postid( $wp_query->query_vars['participants'] );
					$permalink  = get_permalink($post_id);
					$title = get_the_title($post_id);
					global $participant_ids;
	
					$participant_number = 0;
					$participants = array();
					if( !empty($participant_ids) )foreach($participant_ids as $user_id){
						$participant = get_userdata($user_id);
						if( empty($participant) )
							continue;

						array_push($participants, $participant);
						$participant_number++;	
					}
				?>
	<div id="participants">
				<h2><?php echo $participant_number; ?> people talking about:</h2>
				<div class="entry">
				<h3><?php echo $title; ?>
	<span  class="participant_top_link_back" ><a href="<?php echo $permalink; ?>">Back to the Post</a></span>				
			</h3>			
					<div class="context">
					
							<?php
								$i = 0;
								
								if( !empty($participants) ) foreach($participants as $participant){
									$user_id = $participant->ID;
									if( empty($participant) )
										continue;
										
									$participant_profile_url = get_link_to_public_profile($user_id);
									$comment = purple_p_get_last_comment_by_user($user_id, $post_id);
									$comment_link = get_comment_link( $comment[0]->comment_ID );
									$comment_date = get_comment_date('M. j \a\t g:i a', $comment[0]->comment_ID ) ;
									$twitter_handle = "";
						
									$twitter_handle = get_twitter_handle ( xprofile_get_field_data('Twitter' ,$user_id)) ;
									$provider = $participant->rpx_provider;
									
									if($i == 2){
										echo '<hr class="participant_rule"/>';
										$i = 0;
									}
									$i++;
								?>
									<div class="participant">
										<div class="participant_avatar"> <?php echo "<a title='".$participant->display_name."' href='$participant_profile_url'>".get_avatar($user_id, 75)."</a>"; ?>  </div>
										<div class="participant_text">
										<div class="participant_name"> <?php echo "<a href='$participant_profile_url' title='View Profile'>".$participant->display_name."</a>"; ?> </div>
										<div class="participant_last_comment"><?php echo "Last commented on: <br/><a href='$comment_link'>$comment_date</a>";?></div>
										<div class="participant_meta">
										<?php
										if(!empty($twitter_handle))
											echo "<a href='https://twitter.com/$twitter_handle' class='twitter-follow-button' data-show-count='false'>Follow @ $twitter_handle</a><script src='//platform.twitter.com/widgets.js' type='text/javascript'></script>									";

										?>
										</div>
										
										</div>
									</div>
											
									<?php
								}//foreach end	
							?>
							<div class="clearfix"> </div>
	
							<span ><a  href="<?php echo $permalink; ?>">Back to the Post</a> |
							
							 <a  href="<?php echo $permalink;?>#comments">Write a Comment</a></span>
							
							</div>	  <!-- #participants --> 
							</div> <!-- .context --> 
							</div> <!-- .entry --> 
							 
					</div> <!-- .post-content --> 
			</div> <!-- .page -->

		<?php do_action( 'bp_after_blog_single_post' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>