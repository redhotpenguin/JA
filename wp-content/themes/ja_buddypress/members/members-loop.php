<?php /* Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter() */ ?>

<?php do_action( 'bp_before_members_loop' ) ?>

<?php 
/*
function my_query_filter_new ( $query_string ) {
	 $query_string .= '&per_page=15&type=alphabetical';
	return $query_string;
}
//add_filter( 'bp_ajax_querystring', 'my_query_filter_new' );
*/
?>

<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) ) ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="member-dir-count-top">
			<?php bp_members_pagination_count() ?>
		</div>

		<div class="pagination-links" id="member-dir-pag-top">
			<?php bp_members_pagination_links() ?>
		</div>

	</div>

	<?php do_action( 'bp_before_directory_members_list' ) ?>

	<ul id="members-list" class="item-list clearfix">
	<?php while ( bp_members() ) : bp_the_member(); ?>

		<li>
			<div class="item-avatar">
				<a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar() ?></a>
			</div>

			<div class="item">
				<div class="item-title">
					<a href="<?php bp_member_permalink() ?>"><?php bp_member_name() ?></a>

					<?php if ( bp_get_member_latest_update() ) : ?>

						<span class="update"> - <?php bp_member_latest_update( 'length=10' ) ?></span>

					<?php endif; ?>
					

				</div>

				

				<?php do_action( 'bp_directory_members_item' ) ?>
				<p class="member-bio"><?php bp_member_profile_data('field=Bio'); ?></p>
				
				<div class="item-meta"><span class="activity"><?php bp_member_last_active() ?></span></div>
			</div>

			<div class="action">

				<?php do_action( 'bp_directory_members_actions' ); ?>

			</div>

			<div class="clear"></div>
		</li>

	<?php endwhile; ?>
	</ul>

	<?php do_action( 'bp_after_directory_members_list' ) ?>

	<?php bp_member_hidden_fields() ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="member-dir-count-bottom">
			<?php bp_members_pagination_count() ?>
		</div>

		<div class="pagination-links" id="member-dir-pag-bottom">
			<?php bp_members_pagination_links() ?>
		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( "Sorry, no matching people found.", 'buddypress' ) ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_members_loop' ) ?>
