<?php
/*
Template Name: Provider Recovery

/support/provider-recovery/
*/

get_header() ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_blog_single_post' ) ?>

		<div class="page" id="blog-single">
			
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<div class="item-options">

					<div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'buddypress' ) ) ?></div>
					<div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'buddypress' ) ) ?></div>

				</div>


					<div class="post-content">
						
							<?php the_title('<h1>','</h1>'); ?>


						<div class="entry">
						
							<script type='text/javascript'>
								jQuery(document).ready(function($){
									

									function validateEmail( email ) {  
										var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
										return re.test(email);
									} 
									
									function recover( action_type, action_value, message_target ){
									message_target.html('<img src="/wp-content/themes/ja_buddypress/images/ajax_wheel.gif"/>');

										var data = {
											action: 'frontend',
											controller_name: 'Support',
											controller_action: 'recover_provider',
											action_params : { type:action_type, value: action_value }
										};
											
										jQuery.post(ajaxurl, data, function(response) {
											message_target.html(response);
										});
									}
									
									
									$('#provider_recovery_email_form').submit(function(){
										$('#provider_username_recovery_message').html('');
										var message_target = $('#provider_email_recovery_message');
										var email = $('#provider_recovery_email').val();
										
										if(email == ""){
												message_target.html('<span class="support_warning">Please enter your email address.</span>');
												return false;
										}
										
										if( validateEmail(email) != "" ){
											message_target.html('<img src="/wp-content/themes/ja_buddypress/images/ajax_wheel.gif"/>');
											recover('email',email, message_target);
										}
										else{
											message_target.html('<span class="support_warning">Please enter a valid email address.</span>');
										}
										
											return false;
											
										});
									
									
									$('#provider_recovery_username_form').submit(function(){
										$('#provider_email_recovery_message').html('');
										var message_target = $('#provider_username_recovery_message');
										var username = $('#provider_recovery_username').val()
										
										if(username == ""){
												message_target.html('<span class="support_warning">Please enter your username.</span>');
												return false;
										}
										recover('username',username, message_target);
										
										return false;
									});
									
								});
							</script>
							
							<div class="context">
							
							<?php
								$form = '<div id="provider_recovery"><form method="POST" action="#" id="provider_recovery_email_form">
									<p>Having problems connecting to JA?  Please enter either your email address or your username to receive an email with your JA account information.</p>
									
									<table>
									<tr>
										<td class="recovery_row"><label>Your email address:</label></td>
										<td><input type="text" id="provider_recovery_email" name="email" size="30" /></td>
									</tr>
									
									<tr>
										<td colspan="2"><input type="submit" value="Submit"/> <div id="provider_email_recovery_message"></div> </td>
									</tr>
									</table>
									</form>
			
									<hr/>
									
									<form method="POST" action="#" id="provider_recovery_username_form">
									<table>
									<tr>
										<td class="recovery_row"><label>Your username:</label></td>
										<td><input type="text" id="provider_recovery_username" name="username" size="30" /></td>
									</tr>
									
									<tr>
										<td colspan="2"><input type="submit" value="Submit"/> <div id="provider_username_recovery_message"></div> </td>
									</tr>
									
									</table>	
									</form>
								</div>';
								global $post;
								
								 $content = get_the_content(); 
								 $content = apply_filters('the_content', $content);
								
								 echo str_replace('{provider_recovery_form}', $form, $content);
									
							?>
							</div> <!-- context end-->
					
							
							</div>
							
						</div>




			<?php endwhile; else: ?>

				<p><?php _e( 'Sorry, no posts matched your criteria.', 'buddypress' ) ?></p>

			<?php endif; ?>

		</div>

		<?php do_action( 'bp_after_blog_single_post' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>