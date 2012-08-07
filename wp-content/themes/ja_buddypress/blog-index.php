<?php
/*
Template Name: Blog Index Template
*/
get_header(); ?>

    <div id="content">
        <div class="padder">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div class="page" id="blog-archives" style="margin-bottom:20px;">
                <h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</div>
			
		<div class="two-columns">	
			<?php get_template_part('blog-index', 'tab-area'); ?>
		</div>
		
		<div class="secondary-sidebar">	
			<?php get_sidebar( 'blog-index' ); ?>
		</div>
		
		<?php endwhile; else: ?>
			<p><?php _e( 'Sorry, no posts matched your criteria.', 'buddypress' ) ?></p>
		<?php endif; ?>

        </div><!-- .padder -->
    </div><!-- #content -->

	<!--Primary Sidebar-->
	<div id="sidebar">
		<div class="padder">
			<?php dynamic_sidebar( 'sidebar' ); ?>
		</div><!-- .padder -->
	</div><!-- #sidebar -->
	
<?php get_footer(); ?>
