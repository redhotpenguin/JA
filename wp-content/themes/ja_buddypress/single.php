<?php get_header() ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_blog_single_post' ) ?>

		<div class="page" id="blog-single">
			
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<div class="item-options">

					<div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'buddypress' ) ) ?></div>
					<div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'buddypress' ) ) ?></div>

				</div>

				<div class="post cat-<?php $category = get_the_category(); echo $category[0]->parent; ?>" id="post-<?php the_ID(); ?>">

					<div class="post-content">
						<span class="addthis_toolbox addthis_default_style sharing"><!-- AddThis Button BEGIN -->
<a class="addthis_button_facebook"></a>
<a class="addthis_button_tweet" tw:via="journaccel" tw:count="none"></a>
<a class="addthis_button_email"></a>
<a class="addthis_button_print"></a>
<a class="addthis_button_compact"></a>
<a class="addthis_counter addthis_bubble_style"></a>
<script type="text/javascript">var addthis_config = {"data_track_clickback":true};</script>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=journalismaccelerator"></script>
</span>
						<?php if (!in_category(array(25, 35, 34, 33, 26, 36, 32, 31, 30, 39, 40))) { ?>
							<h1>Question:</h1>
						<?php } elseif (in_category(array(25, 35, 34, 33, 26, 36, 32, 31, 30, 52))) { ?>
							<h1>Resource:</h1>
						<?php } elseif (in_category(array(39))) { ?>
							<h1>Blog:</h1>
						<?php } elseif (in_category(array(40))) { ?>
							<h1>About:</h1>
						<?php } ?>

						<div class="entry">
						
						<?php if (!in_category(array(25, 35, 34, 33, 26, 36, 32, 31, 30, 39, 40))) {
							the_title('<p>', '</p>');
							?>
							
							<div class="context">
							
							<?php if (get_post_meta($post->ID, 'underwrite')) { ?>
							<div class="underwrite clearfix"><?php echo get_post_meta($post->ID, 'underwrite', true); ?></div>
							<?php } ?>
							<?php the_excerpt(); ?>
							<?php the_content(); ?>
							<h3 class="respond"><a href="#respond">Answer This</a> <span class="numcomments">(<a href="#comments"><?php 
							comments_number('No answers','One answer','% answers'); ?></a>)</span></h3>
							<?php // the_content(); ?>
							
							</div>
							<?php if (in_category('25')) : ?>
							<p style="font-size: 1em; line-height: 1.25em;"><em>The Journalism Accelerator is not responsible for the content we post here, as excerpts from the source, or links on those sites. The JA does not endorse these sites or their products outright but we sure are intrigued with what they’re up to.</em></p>
							<?php endif; ?>
							<hr />
							<?php wp_gdsr_render_article(); ?>
							<div class="post-info">Posted by <?php the_author_link(); ?> on <?php the_date(); ?><?php edit_post_link('Edit This Post', ' | ') ?><br />
							<?php $cat_number = count($category); if ($cat_number > 1) { ?>Topics:<?php } else { ?>Topic:<?php } ?> <?php the_category(', ') ?></div>
						<?php 
						} elseif (in_category(array(40))) {
							the_title('<h2>','</h2>');
							the_content();
						} else {
							custom_resource_tout();
							the_title('<h2>','</h2>');
							?><?php
							custom_resource_fields();
							?><div class="resource-content"><?php
							the_content(); 
							?></div>
							<?php if (in_category('25')) : ?>
							<p style="font-size: 1em; line-height: 1.25em;"><em>The Journalism Accelerator is not responsible for the content we post here, as excerpts from the source, or links on those sites. The JA does not endorse these sites or their products outright but we sure are intrigued with what they’re up to.</em></p>
							<?php endif; ?>
							<hr />
							<?php wp_gdsr_render_article(); ?>
							<div class="post-info">Posted by <?php the_author_link(); ?> on <?php the_date(); ?><?php edit_post_link('Edit Post', ' — '); ?><br />
						<?php $cat_number = count($category); if ($cat_number > 1) { ?>Topics:<?php } else { ?>Topic:<?php } ?> <?php the_category(', ') ?></div><?php
						} ?>
						<?php if (!in_category(array(25, 35, 34, 33, 26, 36, 32, 31, 30))) {  } ?>
						
							<?php wp_link_pages(array('before' => __( '<p><strong>Pages:</strong> ', 'buddypress' ), 'after' => '</p>', 'next_or_number' => 'number')); ?>
						</div>

					</div>

				</div>

			<?php comments_template(); ?>

			<?php endwhile; else: ?>

				<p><?php _e( 'Sorry, no posts matched your criteria.', 'buddypress' ) ?></p>

			<?php endif; ?>

		</div>

		<?php do_action( 'bp_after_blog_single_post' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>