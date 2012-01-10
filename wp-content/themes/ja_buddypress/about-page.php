<?php
/*
Template Name: About Page
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
					<?php $bitly = get_post_bitly( $post->ID ); ?>
						<span class="addthis_toolbox addthis_default_style sharing"><!-- AddThis Button BEGIN -->
<a class="addthis_button_facebook"></a>
<a class="addthis_button_tweet" tw:url="<?php echo $bitly; ?>" tw:via="journaccel" tw:count="none"></a>
<a class="addthis_button_email"></a>
<a class="addthis_button_print"></a>
<a class="addthis_button_compact"></a>
<a class="addthis_counter addthis_bubble_style"></a>
<script type="text/javascript">
	var addthis_config = {
		data_track_clickback: false
	};
</script>

<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=journalismaccelerator"></script>
</span>

							<h1>About:</h1>


						<div class="entry">
						
						
							<div class="context"><?php the_content(); ?></div>
							
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