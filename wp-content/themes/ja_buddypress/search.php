<?php get_header() ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_blog_search' ) ?>

		<div class="page" id="blog-search">
			<?php if (function_exists('relevanssi_didyoumean')) { relevanssi_didyoumean(get_search_query(), "<p>Did you mean: ", "?</p>", 5); } ?>
			<?php if (have_posts()) : ?>

				<h2 class="pagetitle"><?php printf( __('Search results for "%s"', 'buddypress' ), get_search_query()); ?></h2>

				<div class="navigation">
					<?php if(function_exists('wp_page_numbers')) { wp_page_numbers(); } else { ?>
					<div class="alignleft"><?php next_posts_link( __( '&laquo; Previous Posts', 'buddypress' ) ) ?></div>
					<div class="alignright"><?php previous_posts_link( __( 'Next Posts &raquo;', 'buddypress' ) ) ?></div>
				<?php } ?>
				</div>
				
				

				<?php while (have_posts()) : the_post(); ?>

					<?php do_action( 'bp_before_blog_post' ) ?>

					<div class="post" id="post-<?php the_ID(); ?>">

						<div class="post-content">
							<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

							<div class="entry">
								<?php the_excerpt(); ?>
							</div>
							
						</div>

					</div>

					<?php do_action( 'bp_after_blog_post' ) ?>

				<?php endwhile; ?>

				<div class="navigation">
					<?php if(function_exists('wp_page_numbers')) { wp_page_numbers(); } else { ?>
					<div class="alignleft"><?php next_posts_link( __( '&laquo; Previous Posts', 'buddypress' ) ) ?></div>
					<div class="alignright"><?php previous_posts_link( __( 'Next Posts &raquo;', 'buddypress' ) ) ?></div>
				<?php } ?>
				</div>

			<?php else : ?>

				<h2 class="center"><?php _e( 'No results found. Try a different search?', 'buddypress' ) ?></h2>
				<?php locate_template( array( '/searchform.php'), true ) ?>

			<?php endif; ?>

		</div>

		<?php do_action( 'bp_after_blog_search' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>
