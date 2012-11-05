<?php get_header() ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_blog_single_post' ) ?>

		<div class="page" id="blog-single">
			
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				
				<?php setPostViews(get_the_ID()); ?>

				<div class="item-options">

					<div class="alignleft"><?php next_posts_link( __( '&larr; Previous Entries', 'buddypress' ) ) ?></div>
					<div class="alignright"><?php previous_posts_link( __( 'Next Entries &rarr;', 'buddypress' ) ) ?></div>

				</div>

				<div class="post cat-<?php $category = get_the_category(); echo $category[0]->parent; ?>" id="post-<?php the_ID(); ?>">

					<div class="post-content">
					
						<?php if (in_category(28) || parent_category_is(28)) { ?>
							<h1>Question:</h1>
						<?php } elseif (in_category(25) || parent_category_is(25)) { ?>
							<h1>Resource:</h1><h3 class="index_title"><a href="/resource-index/" class="resource"><span class="expandlink">Resources Index</span></a></h3>
						<?php } elseif (in_category(39) || parent_category_is(39)) { ?>
							<h1>Blog:</h1><h3 class="index_title blog-index"><a href="/blog-index/" class="resource" ><span class="expandlink">Blog Index</span></a></h3>

<?php } ?>

						<div class="entry">
						
						<?php if (in_category(28) || parent_category_is(28)) {
							the_title('<h2>', '</h2>');
							$bitly = get_post_bitly( $post->ID );
							?>
							<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style ">
<a class="addthis_button_printfriendly"><img style="border:none;" src="http://cdn.printfriendly.com/button-print-gry20.png" alt="Print Friendly and PDF"/></a>
<a class="addthis_button_tweet" tw:url="<?php echo $bitly; ?>" tw:counturl="<?php echo get_permalink($post->ID); ?>" tw:via="journaccel"></a>
<a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
<a class="addthis_button_linkedin_counter"></a>
</div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=journalismaccelerator"></script>
<script type="text/javascript">
	var addthis_config = {
		data_track_clickback: false
	};
</script>

<!-- AddThis Button END -->
							<div class="context">
							
							<?php if (get_post_meta($post->ID, 'underwrite')) { ?>
							<div class="underwrite clearfix"><?php echo get_post_meta($post->ID, 'underwrite', true); ?></div>
							<?php } ?>
							<?php the_content(); ?>
						
							
							</div>
							<?php if (in_category(25) || parent_category_is(25)) : ?>
							<p style="font-size: 1em; line-height: 1.25em;"><em>The Journalism Accelerator is not responsible for the content we post here, as excerpts from the source, or links on those sites. The JA does not endorse these sites or their products outright but we sure are intrigued with what they’re up to.</em></p>
							<?php endif; ?>
							<hr />
							<div class="post-info">Posted by <?php the_author_link(); ?> on <?php the_date(); ?><?php edit_post_link('Edit This Post', ' | ') ?><br />
							<span class="categories"><?php $cat_number = count($category); if ($cat_number > 1) { ?>Topics:<?php } else { ?>Topic:<?php } ?> <?php the_category(' ') ?></span></div>
						<?php 
						} elseif (in_category(40) || parent_category_is(40)) {
							the_title('<h2>','</h2>');
							the_content();
						} else {
							custom_resource_tout();
								if (in_category(39) || parent_category_is(39)) { the_title('<h2 class="blogtitle">','</h2>'); } else { the_title('<h2>','</h2>');	}
							?>
								<?php if (in_category(39) || parent_category_is(39)) : ?><div class="post-info">Posted <?php if (!in_category(25) && !parent_category_is(25)) : ?>by <?php the_author_link(); ?> <?php endif; ?>on <?php the_date(); ?><?php edit_post_link('Edit Post', ' — '); ?></div><?php endif; ?>
								<!-- AddThis Button BEGIN -->
								<?php $bitly = get_post_bitly( $post->ID ); ?>
<div class="addthis_toolbox addthis_default_style ">
<a class="addthis_button_printfriendly"><img style="border:none;" src="http://cdn.printfriendly.com/button-print-gry20.png" alt="Print Friendly and PDF"/></a>
<a class="addthis_button_tweet" tw:url="<?php echo $bitly; ?>" tw:counturl="<?php echo get_permalink($post->ID); ?>" tw:via="journaccel"></a>
<a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
</div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=journalismaccelerator"></script>
<script type="text/javascript">
	var addthis_config = {
		data_track_clickback: false
	};
</script>
<!-- AddThis Button END -->
							<?php
							custom_resource_fields();
							?><div class="resource-content"><?php
							the_content(); 
							?></div>
							<?php if (in_category(25) || parent_category_is(25)) : ?>
							<p style="font-size: 1em; line-height: 1.25em;"><em>The Journalism Accelerator is not responsible for the content we post here, as excerpts from the source, or links on those sites. The JA does not endorse these sites or their products outright but we sure are intrigued with what they’re up to.</em></p>
							<?php endif; ?>
							<hr />
							<div class="post-info"><?php if (!in_category(39) && !parent_category_is(39)) : ?>Posted <?php if (!in_category(25) && !parent_category_is(25)) : ?>by <?php the_author_link(); ?> <?php endif; ?>on <?php the_date(); ?><?php edit_post_link('Edit Post', ' — '); ?><br /><?php endif; ?>
						<span class="categories"><?php $cat_number = count($category); if ($cat_number > 1) { ?>Topics:<?php } else { ?>Topic:<?php } ?> <?php the_category(' ') ?></span></div><?php
						} ?>
						<?php if (in_category(28) || parent_category_is(28)) {  } ?>
						
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