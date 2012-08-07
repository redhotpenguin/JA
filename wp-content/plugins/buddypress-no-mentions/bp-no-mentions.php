<?php
class BP_No_Mentions {

	function init() {
		// remove @mention email notifications
		add_action( 'plugins_loaded', array(&$this, 'remove_email_notifications'), 99 );

		// removes @mention links in updates, forum posts, etc.
		remove_filter( 'bp_activity_new_update_content', 'bp_activity_at_name_filter' );
		remove_filter( 'groups_activity_new_update_content', 'bp_activity_at_name_filter' );
		remove_filter( 'pre_comment_content', 'bp_activity_at_name_filter' );
		remove_filter( 'group_forum_topic_text_before_save', 'bp_activity_at_name_filter' );
		remove_filter( 'group_forum_post_text_before_save', 'bp_activity_at_name_filter' );
		remove_filter( 'bp_activity_comment_content', 'bp_activity_at_name_filter' );

		// readjust notifications screen to remove @mention option
		remove_action( 'bp_notification_settings', 'bp_activity_screen_notification_settings', 1 );
		add_action( 'bp_notification_settings', array(&$this, 'adjust_notification_settings'), 1 );

		// miscellaneous removals - subnav tab and public message button
		add_action( 'init', array(&$this, 'remove_elements') );

		// remove inline CSS if defined
		if ( !defined( 'BP_NO_MENTIONS_DISABLE_INLINE_CSS' ) )
			add_action( 'wp_head', array(&$this, 'inline_css'), 99 );
	}

	function remove_email_notifications() {
		// have to call the notifications file, otherwise we can't remove email notifications!
		require_once( BP_PLUGIN_DIR . '/bp-activity/bp-activity-notifications.php' );
		remove_action( 'bp_activity_posted_update', 'bp_activity_at_message_notification', 10, 3 );

		// remove group @mention email notifications
		if ( bp_is_active( 'groups' ) ) {
			require_once( BP_PLUGIN_DIR . '/bp-groups/bp-groups-notifications.php' );
			remove_action( 'bp_groups_posted_update', 'groups_at_message_notification', 10, 4 );
		}
	}

	function remove_elements() {
		global $bp;

		// remove @mention subnav tab on profile
		bp_core_remove_subnav_item( $bp->activity->slug, 'mentions' );

		// remove public message button from profile
		remove_action( 'bp_member_header_actions', 'bp_send_public_message_button' );
	}

	function adjust_notification_settings() {
		global $bp; ?>
		<table class="notification-settings zebra" id="activity-notification-settings">
			<thead>
				<tr>
					<th class="icon"></th>
					<th class="title"><?php _e( 'Activity', 'buddypress' ) ?></th>
					<th class="yes"><?php _e( 'Yes', 'buddypress' ) ?></th>
					<th class="no"><?php _e( 'No', 'buddypress' )?></th>
				</tr>
			</thead>

			<tbody>
				<tr>
					<td></td>
					<td><?php printf( __( "A member replies to an update or comment you've posted", 'buddypress' ), $current_user->user_login ) ?></td>
					<td class="yes"><input type="radio" name="notifications[notification_activity_new_reply]" value="yes" <?php if ( !get_user_meta( $bp->loggedin_user->id, 'notification_activity_new_reply', true ) || 'yes' == get_user_meta( $bp->loggedin_user->id, 'notification_activity_new_reply', true ) ) { ?>checked="checked" <?php } ?>/></td>
					<td class="no"><input type="radio" name="notifications[notification_activity_new_reply]" value="no" <?php if ( 'no' == get_user_meta( $bp->loggedin_user->id, 'notification_activity_new_reply', true ) ) { ?>checked="checked" <?php } ?>/></td>
				</tr>
	
				<?php do_action( 'bp_activity_screen_notification_settings' ) ?>
			</tbody>
		</table>
	<?php
	}
	
	function inline_css() {
		global $bp;
		
		// let's see if the user is logged in, since the @mentions tab only shows up then anyway
		if ( $bp->loggedin_user->id ) :
			// remove @mentions tab from activity directory
			if ( bp_is_activity_front_page() || ( bp_is_activity_component() && bp_is_directory() ) ) :
	?>
		<style type="text/css">li#activity-mentions {display:none;}</style>
	<?php
			endif;
		endif;
		
		// remove public message button from member profile
		if ( bp_is_member() ) :
	?>
		<style type="text/css">#item-header-content .highlight {display:none;}</style>		
	<?php
		endif;
	}
}

//initialize only if the activity component is enabled!
if ( bp_is_active( 'activity' ) ) :
	$no_mentions = new BP_No_Mentions();
	$no_mentions->init();
endif;

?>