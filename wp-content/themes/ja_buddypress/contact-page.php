<?php
/*
Template Name: Contact Page
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
						

							<h1>Contact Us</h1>


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