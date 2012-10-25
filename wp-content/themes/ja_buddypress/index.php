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
			<div id="text-gutter">
			<ul id="hpbanners">
			<?php $args=array(
					'post_type' => 'featured_banner',
					'post_status' => 'publish',
					'orderby' => 'date'
					//'caller_get_posts'=> 1
					);
					$my_query = null;
					$my_query = new WP_Query($args);
					if( $my_query->have_posts() ) {
					 while ($my_query->have_posts()) : $my_query->the_post(); ?>
					<li class="excerpt-text"><?php the_content(); ?></li>
			<?php endwhile; }
			
		wp_reset_query();  // Restore global post data stomped by the_post().
				?>
					</ul>
					</div><!-- #post-<?php the_ID(); ?> -->
					</div>
					
			<?php ja_home(); ?>
			
			
		</div>

		<?php do_action( 'bp_after_blog_home' ) ?>
		
		

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>
