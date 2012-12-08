<?php get_header() ?>

    <div id="content">
        <div class="padder">

            <?php do_action('bp_before_archive') ?>

            <div class="page" id="fourohfour">

<h2><span style="color: #339966;">404 Error:</span> The page you are looking for cannot be found</h2>

<h4><span style="color: #339966;">You tried to go to:</span> <?php
#some variables for the script to use
#if you have some reason to change these, do.  but wordpress can handle it
$adminemail = get_option('admin_email'); #the administrator email address, according to wordpress
$website = get_bloginfo('url'); #gets your blog's url from wordpress
$websitename = get_bloginfo('name'); #sets the blog's name, according to wordpress

echo " ".$website.$_SERVER['REQUEST_URI']; ?></h4> 
			  
<p>Having trouble finding something? Check our <a href="http://www.journalismaccelerator.com/blog-index/">blog index</a> or <a href="http://www.journalismaccelerator.com/resource-index/">resource index</a> to find what you're looking for.</p>

<div id="fourohfour-links-wrapper">
<div class="top-row">
<div class="fourohfour-singleitem">
<h3><a href="<?php get_bloginfo('url'); ?>/topics/questions/">Questions</a></h3>
<ul class="fourohfour-lists">
<?php
	$latest_listings = new WP_Query();
	$latest_listings->query('&post_type=questions_cpt&showposts=3');
	while ( $latest_listings->have_posts() ) {
		$latest_listings->the_post();
		?>
			<li class="title"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></li>
		<?php
	}
?>
</ul>
</div>

<div class="fourohfour-singleitem">
<h3><a href="<?php get_bloginfo('url'); ?>/topics/resources/">Resources</a></h3>
<ul class="fourohfour-lists">
<?php
	$latest_listings = new WP_Query();
	$latest_listings->query('&cat=25&showposts=5');
	while ( $latest_listings->have_posts() ) {
		$latest_listings->the_post();
		?>
			<li class="title"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></li>
		<?php
	}
?>
</ul>
</div>

<div class="fourohfour-singleitem">
<h3><a href="<?php get_bloginfo('url'); ?>/topics/blog/">Blog</a></h3>
<ul class="fourohfour-lists">
<?php
	$latest_listings = new WP_Query();
	$latest_listings->query('showposts=4');
	while ( $latest_listings->have_posts() ) {
		$latest_listings->the_post();
		?>
			<li class="title"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></li>
		<?php
	}
?>
</ul>
</div>
</div>
<div class="bottom-row">
<div class="fourohfour-singleitem">
<h3><a href="<?php get_bloginfo('url'); ?>/projects-archive/">Projects</a></h3>
<ul class="fourohfour-lists">
<?php
	$latest_listings = new WP_Query();
	$latest_listings->query('&post_type=projects&showposts=5');
	while ( $latest_listings->have_posts() ) {
		$latest_listings->the_post();
		?>
			<li class="title"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></li>
		<?php
	}
?>
</ul>
</div>

<div class="fourohfour-singleitem">
<h3><a href="<?php get_bloginfo('url'); ?>/about/what-is-the-journalism-accelerator/">About</a></h3>
<ul class="fourohfour-lists">
<li><a href="/about/what-is-the-journalism-accelerator/">What is This?</a></li>
<li><a href="/about/ja-team/">JA Team</a></li>
<li><a href="/about/ja-special-thanks/">Special Thanks</a></li>
<li><a href="/blog/the-journalism-accelerator-has-taken-the-pledge/">TAO of Journalism</a></li>
<li><a href="/about/privacy-policy/">Your Privacy Matters</a></li>
<li><a href="/link-to-us/">Link to Us</a></li>
</ul>
</div>

<div class="fourohfour-singleitem">
<h3><a href="<?php get_bloginfo('url'); ?>/contact-us/">Connect</a></h3>
<ul class="fourohfour-lists">
<li><a href="https://twitter.com/journaccel" class="twitter-follow-button" data-show-count="true" data-lang="en">Follow @journaccel</a></li>
<li class="facebook-icon"><a href="http://www.facebook.com/journalismaccelerator" title="JA on Facebook" target="_blank">Facebook</a></li>
<li class="linkedin-icon"><a href="http://www.linkedin.com/company/journalism-accelerator/" title="JA on LinkedIn" target="_blank">LinkedIn</a></li>
<li class="rss-icon"><a href="/feed/">RSS Feed</a></li>
<li class="ja-link"><a href="/link-to-us/">Link to Us</a></li>
</ul>
</div>
</div>
<div class="clear">&nbsp;</div>
</div>

<p><strong>Still having trouble finding what you're looking for?</strong><br />Try using the search bar below. Or send us a message on <a href="https://twitter.com/journaccel">Twitter</a>, <a href="http://www.facebook.com/journalismaccelerator">Facebook</a>, or <a href="http://www.linkedin.com/company/journalism-accelerator/">LinkedIn</a>, and we'll do our best to help you find the content you need.</p>
<?php include(TEMPLATEPATH . "/searchform.php"); ?>

</div>
    <?php do_action('bp_after_archive') ?>

        </div><!-- .padder -->
    </div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>

