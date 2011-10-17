<?php get_header(); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_archive' ) ?>

		<div class="page" id="blog-archives">
		<h1>Resources Related to Journalism and <?php wp_title(null); ?> <span class="feed"><a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2"><img src="/feed.png" alt="Feed Icon" /></a> <a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2">Feed for this topic</a></span></h1>
			<?php echo category_description(); ?>
			<div class="navigation">

					<?php if(function_exists('wp_page_numbers')) { wp_page_numbers(); } else { ?>
					<div class="alignleft"><?php next_posts_link( __( '&laquo; Previous Posts', 'buddypress' ) ) ?></div>
					<div class="alignright"><?php previous_posts_link( __( 'Next Posts &raquo;', 'buddypress' ) ) ?></div>
				<?php } ?>

				</div>
			<?php if ( have_posts() ) : ?>

				<?php while (have_posts()) : the_post(); ?>

					<?php do_action( 'bp_before_blog_post' ) ?>

					<div class="post" id="post-<?php the_ID(); ?>">
					
						<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
						<?php $excerpt = strip_tags(get_the_excerpt()); echo $excerpt; ?>
						<p class="post-info">Posted by <?php the_author_link(); ?> on <?php echo get_the_date(); ?><br />
						<?php $category = get_the_category(); $cat_number = count($category); if ($cat_number > 1) { ?>Topics:<?php } else { ?>Topic:<?php } ?> <?php the_category(', ') ?></p>
						

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

				<h2 class="center"><?php _e( 'Not Found', 'buddypress' ) ?></h2>
				<?php locate_template( array( 'searchform.php' ), true ) ?>

			<?php endif; ?>

		</div>

		<?php do_action( 'bp_after_archive' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer(); ?>
