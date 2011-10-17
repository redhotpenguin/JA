<?php do_action( 'bp_before_profile_loop_content' ) ?>
<?php global $has_twitter; $has_twitter=0; ?>
<?php if ( function_exists('xprofile_get_profile') ) : ?>

    <?php if ( bp_has_profile() ) : ?>
<div class="profile-container clearfix">

        <?php while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

            <?php if ( bp_profile_group_has_fields() ) : ?>

                <?php do_action( 'bp_before_profile_field_content' ) ?>

                <div class="bp-widget <?php bp_the_profile_group_slug() ?>">
		
                    <?php if ( 1 != bp_get_the_profile_group_id() ) : ?>
                        <h4><?php bp_the_profile_group_name() ?></h4>
                    <?php endif; ?>

                        <?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

                            <?php if ( bp_field_has_data() ) : ?>
                                
				<?php if ( 'Twitter' == bp_get_the_profile_field_name() ) : ?>

<?php
	$has_twitter = bp_get_profile_field_data('field=Twitter');

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
?>
				<?php endif; ?>

                            <?php endif; ?>

                            <?php do_action( 'bp_profile_field_item' ) ?>

                        <?php endwhile; ?>
                         
		                    
                
                </div>

                <?php do_action( 'bp_after_profile_field_content' ) ?>

            <?php endif; ?>

        <?php endwhile; ?>

        <?php do_action( 'bp_profile_field_buttons' ) ?>

<?php if ( $has_twitter ) : ?>

<script type='text/javascript'>
    jQuery(document).ready(function(){
        jQuery(".tweet").tweet({
            username: "<?php echo $has_twitter; ?>",
            avatar_size: 32,
            count: 5,
            template: function(i){return i["time"]+' '+i["text"]},
            loading_text: "loading tweets..."
        });
    });
</script>

<div class="twitter_container">
  <h3>Recent Tweets from <br /><?php echo $has_twitter;?></h3>
   <div id="twitter-follow"></div>
  <script type="text/javascript">

  twttr.anywhere(function (T) {
    T('#twitter-follow').followButton("<?php echo $has_twitter; ?>");
  });

</script>
  <div class="tweet"></div>
  
</div>

<?php else: // no twitter found:?>
<div  class="no_tweet">
<h3>Recent Tweets</h3>
<p><?php 

if(bp_is_my_profile()) {
$user = wp_get_current_user();
$id = $user->id;
$edit_profile_link = get_edit_link($id);
echo '<span class=""> Do you tweet?  <a href="'.$edit_profile_link.'">Update your profile</a> with your Twitter url and share your thoughts with the Journalism Accelerator community. </span>';}

else echo '<span class="">This member has no recent tweets. </span>'; ?></p></div>
<?php
endif; // if($has_twitter) end


?> 

<?php /* Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_activity_loop() */ ?>

<?php do_action( 'bp_before_activity_loop' ) ?>

<?php if ( bp_has_activities( bp_ajax_querystring( 'activity' ) ) ) : ?>

	<?php /* Show pagination if JS is not enabled, since the "Load More" link will do nothing */ ?>
	<noscript>
		<div class="pagination">
			<div class="pag-count"><?php bp_activity_pagination_count() ?></div>
			<div class="pagination-links"><?php bp_activity_pagination_links() ?></div>
		</div>
	</noscript>

	<?php if ( empty( $_POST['page'] ) ) : ?>
		<ul id="activity-stream" class="activity-list item-list">
		<h3>Recent Activity</h3>
	

	<?php while ( bp_activities() ) : bp_the_activity(); ?>
		
		<?php include( locate_template( array( 'activity/entry.php' ), false ) ) ?>

	<?php endwhile; ?>


	<?php if ( empty( $_POST['page'] ) ) : ?>
		</ul>
	<?php endif; ?>

<?php else : ?>
	<div id="message" class="info">
		<p><?php _e( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress' ) ?></p>
	</div>
<?php endif; ?>

<?php do_action( 'bp_after_activity_loop' ) ?>

<form action="" name="activity-loop-form" id="activity-loop-form" method="post">
	<?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ) ?>
</form>


<? else: // no activity found: ?>
<div class="no_activity">
<h3>Recent Activity</h3>
<p><?php 
if(bp_is_my_profile()) {	
echo '<span class=""> You have no recent activity.    To weigh in, <a href="http://www.journalismaccelerator.com/topics/questions/">Browse through questions</a> posted by the Journalism Accelerator community or check out our archive of <a href="http://www.journalismaccelerator.com/topics/resources/">resource listings</a>. </span>';}
else  {echo '<span class="">This member has no recent activity. </span>';} ?></p>
</div>

<?php endif;?>
</div>    
<?php endif; ?>

<?php else : ?>

    <?php /* Just load the standard WP profile information, if BP extended profiles are not loaded. */ ?>
    <?php bp_core_get_wp_profile() ?>

<?php endif; ?>

<?php do_action( 'bp_after_profile_loop_content' ) ?>

