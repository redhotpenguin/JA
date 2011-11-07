<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

	<head profile="http://gmpg.org/xfn/11">

		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
		
		<?php display_title(); ?>

		<?php do_action( 'bp_head' ) ?>

		<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>?<?php echo time(); ?>" type="text/css" media="screen" />

		<?php if ( function_exists( 'bp_sitewide_activity_feed_link' ) ) : ?>
			<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php _e('Site Wide Activity RSS Feed', 'buddypress' ) ?>" href="<?php bp_sitewide_activity_feed_link() ?>" />
		<?php endif; ?>

		<?php if ( function_exists( 'bp_member_activity_feed_link' ) && bp_is_member() ) : ?>
			<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php bp_displayed_user_fullname() ?> | <?php _e( 'Activity RSS Feed', 'buddypress' ) ?>" href="<?php bp_member_activity_feed_link() ?>" />
		<?php endif; ?>

		<?php if ( function_exists( 'bp_group_activity_feed_link' ) && bp_is_group() ) : ?>
			<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> | <?php bp_current_group_name() ?> | <?php _e( 'Group Activity RSS Feed', 'buddypress' ) ?>" href="<?php bp_group_activity_feed_link() ?>" />
		<?php endif; ?>

		<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> <?php _e( 'Blog Posts RSS Feed', 'buddypress' ) ?>" href="<?php bloginfo('rss2_url'); ?>" />
		<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> <?php _e( 'Blog Posts Atom Feed', 'buddypress' ) ?>" href="<?php bloginfo('atom_url'); ?>" />

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
		<?php if (is_single()) {
  	global $post;
  	$thumb=vp_get_thumb_url($post->post_content); 
  	if (get_post_meta($post->ID, 'image_src', true)) { ?><link rel="image_src" href="<?php echo get_post_meta($post->ID, 'image_src', true); ?>" /><?php }
  	elseif ($thumb!='') { echo "<link rel=\"image_src\" href=\"$thumb\" />"; }
  	else { echo "<link rel=\"image_src\" href=\"/ja-fb.jpg\" />"; }
  	} else { ?>
  <link rel="image_src" href="/ja-fb.jpg" />
  <?php } ?>
		<link rel="icon" href="/wp-content/uploads/2011/02/ja_dev_favicon.gif" type="image/gif"/>

		<?php wp_head(); ?>
		<?php if (is_home()) { ?>
		<script type="text/javascript" src="/js/jquery.cycle.all.min.js"></script>
		<script language="javascript" src="/js/tweet/jquery.tweet.js" type="text/javascript"></script>
		<script type="text/javascript">jQuery(document).ready(function() {jQuery('div#featured-slider').cycle({next: '#next', prev: '#prev', fx: 'fade', timeout: 10000, speed: 350, pause: 1, random: 1});});</script>
		<script type="text/javascript">
		jQuery.fn.equalizeHeights = function(){
 			 return this.height( Math.max.apply(this, jQuery(this).map(function(i,e){ return jQuery(e).height() }).get() ) )
}
		jQuery(document).ready(function() {
			jQuery('.box_content').equalizeHeights();
			jQuery('#home-tweet-container').tweet({ username: 'journaccel', count: 1, template: function(info) { return '<span class="intro"><a href="http://twitter.com/journaccel" target="_blank">@JournAccel</a> on Twitter</span> (' + info["time"] + ")<br />" + info["text"] } });
		});
		</script>
		<?php } ?>
		<?php if (is_category(25) || is_category(28)) { ?>
		<script type="text/javascript">
		jQuery.fn.equalizeHeights = function(){
 			 return this.height( Math.max.apply(this, jQuery(this).map(function(i,e){ return jQuery(e).height() }).get() ) )
		}
		jQuery(document).ready(function() {
			jQuery('.box_content').equalizeHeights();
			});
			</script>
		<?php } ?>
		<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>
		
		
		<?php
		global $bp;
		if($bp->current_component == BP_XPROFILE_SLUG){
   			?><script language="javascript" src="/js/tweet/jquery.tweet.js" type="text/javascript"></script>
   			<script src="http://platform.twitter.com/anywhere.js?id=gwaI4JpkTimXprMMaNdF0w&v=1" type="text/javascript"></script>
   			<?php
		}
		?>
	<meta name="google-site-verification" content="ae-oLPTbPxSIDVwIxM_mf6hEqwzbSf8L2ZZX_0O2f1Y" />	

	</head>

	<body <?php body_class('custom') ?> id="bp-default">

		<?php do_action( 'bp_before_header' ) ?>

		<div id="header">

			<?php ja_header(); ?>
			<a href="/about/what-is-the-journalism-accelerator/" id="beta-what">What is this?</a>

		</div><!-- #header -->

		<?php do_action( 'bp_after_header' ) ?>
		
		<div id="cat_nav">
			<h2><a href="/topics/questions/" class="questiontab <?php if (is_home()) { } elseif (in_category(28) || is_category(28) || parent_category_is(28)) { echo 'active'; } elseif (is_home()) { } else { } ?>">Questions</a> <a href="/resources/" class="resourcetab <?php if (is_home()) { } elseif (in_category(25) || is_category(25) || parent_category_is(25)) { echo 'active'; } else { } ?>">Resources</a> <a href="/blog/" class="<?php if (is_home()) { } elseif (in_category(39) || is_category(39) || parent_category_is(39)) { echo 'active'; } else { } ?>">Blog</a></h2>
			<?php if (in_category(28) || is_category(28) || parent_category_is(28)) : ?>
			<?php wp_nav_menu(array(
				'theme_location' => 'questions',
				'container' => 'none',
				'menu_class' => 'questions',
				'menu_id' => 'questions-menu'
			)); ?>
			<?php endif; ?>
			<?php if (in_category(25) || is_category(25) || parent_category_is(25) || in_slug('members') || is_page() || is_home()) : ?>
			<?php wp_nav_menu(array(
				'theme_location' => 'resources',
				'container' => 'none',
				'menu_class' => 'resources',
				'menu_id' => 'resource-menu'
			)); ?>
			<?php endif; ?>
			<?php if (is_category(39) || in_category(39) || parent_category_is(39) && !in_slug('members')) : ?>
			<?php wp_nav_menu(array(
				'theme_location' => 'blog',
				'container' => 'none',
				'menu_class' => 'blog',
				'menu_id' => 'blog-menu'
			)); ?>
			<?php endif; ?>
			<ul class="subnav">
				<li><a href="/about/what-is-the-journalism-accelerator/" class="first">About</a></li>
				<li><a href="/about/what-is-the-journalism-accelerator/#faq">FAQ</a></li>
				<li><a href="/members/">People</a></li>
				<?php if (current_user_can('publish_posts')) { ?> <li><a href="/wp-admin/">Dashboard</a></li><?php } ?>
			</ul>
			<div class="clear"></div>
		</div>
		
		<?php do_action( 'bp_before_container' ) ?>

		<div id="container">
