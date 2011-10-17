<?php get_header(); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_archive' ) ?>

		<div class="page faq" id="blog-archives">

			<h1><?php wp_title(null); ?></h1>
			
			<?php if ( have_posts() ) : ?>
			
			<ul id="toc">
			
			<?php while (have_posts()) : the_post(); ?>
			
			<li><a href="#post-<?php the_ID(); ?>"><?php the_title(); ?></a></li>
			
			<?php endwhile; ?>
			
			</ul>
			
			<?php endif; ?>
			
			<?php rewind_posts(); ?>

			<?php if ( have_posts() ) : ?>
				
				<?php while (have_posts()) : the_post(); ?>

					<?php do_action( 'bp_before_blog_post' ) ?>

					<div class="post" id="post-<?php the_ID(); ?>">
					
						<h2 class="posttitle"><?php the_title(); ?></h2>
						<?php the_content(); ?>
						<p><a href="#toc">Back to Top</a></p>
						

					</div>

					<?php do_action( 'bp_after_blog_post' ) ?>

				<?php endwhile; ?>

				<div class="navigation">

					<div class="alignleft"><?php next_posts_link( __( '&laquo; Previous Posts', 'buddypress' ) ) ?></div>
					<div class="alignright"><?php previous_posts_link( __( 'Next Posts &raquo;', 'buddypress' ) ) ?></div>

				</div>

			<?php else : ?>

				<h2 class="center"><?php _e( 'Not Found', 'buddypress' ) ?></h2>
				<?php locate_template( array( 'searchform.php' ), true ) ?>

			<?php endif; ?>

		</div>

		<?php do_action( 'bp_after_archive' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer(); ?>
