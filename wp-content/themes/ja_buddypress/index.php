<?php get_header() ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_blog_home' ) ?>

		<div class="page" id="blog-latest">
			
			<div id="slider-container">
			  <div id="featured-slider">
							<?php ja_slider(); ?>
							
						</div>
						<div id="slider-nav">
								<a href="#" id="prev">&lt;</a><a href="#" id="next">&gt;</a>
						</div>
			</div>
			
			<div id="home-feature-banner" class="clearfix">
			<h3 class="home-banner">Welcome to the Journalism Accelerator</h3>
							<?php
					$args=array(
					  'post_type' => 'featured_banner',
					  'post_status' => 'publish',
					  'posts_per_page' => 1,
					  'orderby' => 'rand'
					  //'caller_get_posts'=> 1
					  );
					$my_query = null;
					$my_query = new WP_Query($args);
					if( $my_query->have_posts() ) {
					 while ($my_query->have_posts()) : $my_query->the_post(); ?>
					<div id="text-gutter"><div class="excerpt-text" style="padding-left:15px;"><?php the_content( __( 'Read more...', 'twentyeleven' ) ); ?></div></div>
		
					<footer class="entry-meta">
			<?php edit_post_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
					</footer>
					</div><!-- #post-<?php the_ID(); ?> -->
			<?php
					  endwhile;
					}
				wp_reset_query();  // Restore global post data stomped by the_post().
				?>
			
			<?php ja_home(); ?>
			
			
		</div>

		<?php do_action( 'bp_after_blog_home' ) ?>
		
		

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>
