<?php get_header(); ?>

<?php if (is_category(25)) : // RESOURCE HOME PAGE  ?>


    <div id="content">
        <div class="padder">

            <?php do_action('bp_before_archive') ?>

            <div class="page" id="blog-archives">

                <h1>Resources <span class="feed"><a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2"><img src="/feed.png" alt="Feed Icon" /></a> <a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2">Feed of all resources</a></span></h1> <h3 class="widgettitle"><a href="#" class="expand resource" ><span class="expandlink">Resource Index</span></a></h3>
                <?php echo category_description(); 
                
                ja_resource_home(); ?>

                <h2>More Resources</h2>

                <?php
                $current_page = get_query_var('paged');
                $offset = 4;
                $args = array(
//                    'cat' => 25, -445,
                    'category__in' => 25,
                    'post_status' => 'publish',
                );

                if ($current_page == 0) {
                    $args['offset'] = $offset;
                    $args['paged'] = 0;
                } else {
                    $args['offset'] = $offset + ( 10 * --$current_page );
                    //$args['paged'] = $current_page;
                }

                $resource_post_query = new WP_Query($args);

                global $max_page;
                // update global $max_page to take into parameter the offset

                if ($resource_post_query->found_posts < 10) {
                    $max_page = 1; // 1 => no pagination
                } else {
                    $max_page = ceil(( $resource_post_query->found_posts - $offset ) / 10);
                }


                if ($resource_post_query->have_posts()) :
                    ?>

                    <?php
                    while ($resource_post_query->have_posts()) :
                        $resource_post_query->the_post();

                        do_action('bp_before_blog_post')
                        ?>

                        <div class="post" id="post-<?php the_ID(); ?>">

                            <h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to', 'buddypress') ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            <?php $excerpt = strip_tags(get_the_excerpt());
            echo $excerpt; ?>
                            <p class="post-info">Posted on <?php echo get_the_date(); ?><br />
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
            </div>

    <?php do_action('bp_after_archive') ?>

        </div><!-- .padder -->
    </div><!-- #content -->

    <?php // Resource Home  END ?>

    <?php
// QUESTION ARCHIVE

elseif (is_category(28)) :
    ?>

    <div id="content">
        <div class="padder">

    <?php do_action('bp_before_archive') ?>

            <div class="page" id="blog-archives">

                <h1>Questions <span class="feed"><a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2"><img src="/feed.png" alt="Feed Icon" /></a> 
                        <a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2">Feed of all questions</a></span></h1>

    <?php echo category_description(); ?>

                <?php ja_question_home(); ?>

                <h2>More Questions</h2>

                <?php
                $current_page = get_query_var('paged');
                $offset = 4;
                $args = array(
                    'cat' => 28,
                    'post_status' => 'publish',
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
            </div>

    <?php do_action('bp_after_archive') ?>

        </div><!-- .padder -->
    </div><!-- #content -->

            <?php // Question Home END  ?>

<?php
elseif (is_category(39)) :
    //STARTING BLOG HOMEPAGE
    ?>

    <div id="content">
        <div class="padder">

                <?php do_action('bp_before_archive') ?>

            <div class="page" id="blog-archives">
                <h1><?php wp_title(null); ?> <span class="feed"><a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2"><img src="/feed.png" alt="Feed Icon" /></a> <a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2">Feed for this topic</a></span></h1>
                <h3 class="widgettitle"><a href="#" class="expand resource" ><span class="expandlink">Blog Index</span></a></h3>
    <?php echo category_description(); ?>

    <?php if (have_posts()) : ?>

                                <?php while (have_posts()) : the_post(); ?>

                                    <?php do_action('bp_before_blog_post') ?>

                        <div class="post" id="post-<?php the_ID(); ?>">

                            <h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to', 'buddypress') ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                            <p class="post-info">Posted by <?php the_author_link(); ?> on <?php echo get_the_date(); ?><br />
                                <span class="categories">
                                    <?php
                                    $category = get_the_category();
                                    $cat_number = count($category);
                                    if ($cat_number > 1) {
                                        ?>
                                        Topics:

                                <?php
                            } else {
                                ?>

                                        Topic:

                        <?php } ?>

            <?php the_category(' ') ?></span></p>
                            <?php the_content(); ?>

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

            </div>

            <?php do_action('bp_after_archive') ?>

        </div><!-- .padder -->
    </div><!-- #content -->

                <?php // Blog Home ?>

            <?php elseif (is_category('tweetsforkeeps')) : ?>

    <div id="content">
        <div class="padder">

    <?php do_action('bp_before_archive') ?>

            <div class="page" id="blog-archives">
                <h1>Tweets for Keeps <span class="feed"><a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2"><img src="/feed.png" alt="Feed Icon" /></a> <a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2">Feed for this topic</a></span></h1>
    <?php echo category_description(); ?>

    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>

                        <?php do_action('bp_before_blog_post') ?>

                        <div class="post" id="post-<?php the_ID(); ?>">

                            <h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to', 'buddypress') ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            <?php the_content(); ?>
                            <p class="post-info">Posted on <?php echo get_the_date(); ?><br />
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

            </div>

                <?php do_action('bp_after_archive') ?>

        </div><!-- .padder -->
    </div><!-- #content -->

                <?php // Tweets for Keeps  ?>

<?php elseif (is_category(324)) : ?>

    <div id="content">
        <div class="padder">

    <?php do_action('bp_before_archive') ?>

            <div class="page" id="blog-archives">
                <h1>Digest of Featured Resources <span class="feed"><a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2"><img src="/feed.png" alt="Feed Icon" /></a> <a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2">Feed for this topic</a></span></h1>
    <?php echo category_description(); ?>

                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>

            <?php do_action('bp_before_blog_post') ?>

                        <div class="post" id="post-<?php the_ID(); ?>">

                            <h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to', 'buddypress') ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                            <?php the_content(); ?>
                            <p class="post-info">Posted on <?php echo get_the_date(); ?><br />
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

            </div>

                <?php do_action('bp_after_archive') ?>

        </div><!-- .padder -->
    </div><!-- #content -->

                <?php // Featured ?>


<?php elseif (parent_category_is(28)) : ?>

    <div id="content">
        <div class="padder">

    <?php do_action('bp_before_archive') ?>

            <div class="page" id="blog-archives">
                <h1>Questions About Journalism and <?php wp_title(null); ?> <span class="feed"><a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2"><img src="/feed.png" alt="Feed Icon" /></a> <a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2">Feed for this topic</a></span></h1>
                <?php echo category_description(); ?>


                <?php if (have_posts()) : ?>

        <?php while (have_posts()) : the_post(); ?>

                            <?php do_action('bp_before_blog_post') ?>

                        <div class="post" id="post-<?php the_ID(); ?>">

                            <h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to', 'buddypress') ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            <?php $excerpt = strip_tags(get_the_excerpt());
            echo $excerpt; ?>
                            <p class="post-info">Posted by <?php the_author_link(); ?> on <?php echo get_the_date(); ?> (<?php comments_popup_link(__('No answers', 'buddypress'), __('1 answer', 'buddypress'), __('% answers', 'buddypress')); ?>) &mdash; <a href="<?php the_permalink() ?>">Answer this Question</a><br />
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

            </div>

    <?php do_action('bp_after_archive') ?>

        </div><!-- .padder -->
    </div><!-- #content -->

    <?php // Question Sub  ?>

<?php elseif (parent_category_is(25)) : ?>

    <div id="content">
        <div class="padder">

    <?php do_action('bp_before_archive') ?>

            <div class="page" id="blog-archives">
                <h1>Resources Related to Journalism and <?php wp_title(null); ?> <span class="feed"><a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2"><img src="/feed.png" alt="Feed Icon" /></a> <a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2">Feed for this topic</a></span></h1>
    <?php echo category_description(); ?>

                    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>

                        <?php do_action('bp_before_blog_post') ?>

                        <div class="post" id="post-<?php the_ID(); ?>">

                            <h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to', 'buddypress') ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                        <?php $excerpt = strip_tags(get_the_excerpt());
                        echo $excerpt; ?>
                            <p class="post-info">Posted on <?php echo get_the_date(); ?><br />
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

            </div>

    <?php do_action('bp_after_archive') ?>

        </div><!-- .padder -->
    </div><!-- #content -->

                <?php // Resource Sub ?>

            <?php elseif (parent_category_is(39)) : ?>

    <div id="content">
        <div class="padder">

    <?php do_action('bp_before_archive') ?>

            <div class="page" id="blog-archives">
                <h1>Blog Posts About Journalism and <?php wp_title(null); ?> <span class="feed"><a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2"><img src="/feed.png" alt="Feed Icon" /></a> <a href="/?cat=<?php echo get_query_var('cat'); ?>&amp;feed=rss2">Feed for this topic</a></span></h1>
    <?php echo category_description(); ?>

                <?php if (have_posts()) : ?>

                    <?php while (have_posts()) : the_post(); ?>

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

            </div>

    <?php do_action('bp_after_archive') ?>

        </div><!-- .padder -->
    </div><!-- #content -->

<?php endif; // Blog Sub  ?>

<?php locate_template(array('sidebar.php'), true) ?>

<?php get_footer(); ?>