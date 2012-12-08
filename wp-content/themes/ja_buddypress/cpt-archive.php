<?php 
/*
Template Name: CPT Archive Page
*/
 ?>

<?php get_header(); ?>

<?php if (is_page(array(10506,'questions-archive'))) :  ?>

	<div id="content">
		<div class="padder">
		<div class="page" id="blog-archives">

                <h1>Questions <span class="feed"><a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2"><img src="/feed.png" alt="Feed Icon" /></a> 
                        <a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2">Feed of all questions</a></span></h1>
                
<p><?php echo get_post_field('post_content', $post_id); ?></p>

                <?php ja_question_home(); ?>

                <h2>More Questions</h2>

                <?php
                $current_page = get_query_var('paged');
                $offset = 4;
                $args = array(
                    'post_status' => 'publish',
                    'post_type' => 'questions_cpt',
                );


                if ($current_page == 0) {
                    $args['offset'] = $offset;
                    $args['paged'] = 0;
                } else {
                    $args['offset'] = $offset + ( 10 * --$current_page );
                    //$args['paged'] = $current_page;
                }

                $question_post_query = new WP_Query($args);

                global $max_page;
                // update global $max_page to take into parameter the offset


                if ($question_post_query->found_posts < 10) {
                    $max_page = 1; // 1 => no pagination
                } else {
                    $max_page = ceil(( $question_post_query->found_posts - $offset ) / 10);
                }


                if ($question_post_query->have_posts()) :
                    ?>

                    <?php
                    while ($question_post_query->have_posts()) :

                        $question_post_query->the_post();
                        ?>

            <?php do_action('bp_before_blog_post') ?>

                        <div class="post" id="post-<?php the_ID(); ?>">

                            <h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to', 'buddypress') ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            <?php $excerpt = strip_tags(get_the_excerpt());
            echo $excerpt; ?>
                            <p class="post-info">Posted by <?php the_author_link(); ?> on <?php echo get_the_date(); ?><br />
                                <span class="categories"><?php $category = get_the_category();
            $cat_number = count($category);
            if ($cat_number > 1) { ?>Topics:<?php } else { ?>Topic:<?php } ?> <?php the_category(' ') ?></span></p>

                        </div>

            <?php do_action('bp_after_blog_post') ?>

                        <?php endwhile; ?>

                    <div class="navigation">
                    <?php if (function_exists('wp_page_numbers')) {
                        wp_page_numbers();
                    } else { ?>
                            <div class="alignleft"><?php next_posts_link(__('&laquo; Previous Posts', 'buddypress')) ?></div>
                            <div class="alignright"><?php previous_posts_link(__('Next Posts &raquo;', 'buddypress')) ?></div>
                    <?php } ?>

                    </div>

            <?php else : ?>
				<h2 class="center"><?php _e('Not Found', 'buddypress') ?></h2>
				<?php locate_template(array('searchform.php'), true) ?>
	<?php endif; ?>




		</div><!-- .page -->
		</div><!-- .padder -->
	</div><!-- #content -->

<?php endif; ?>

<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer(); ?>