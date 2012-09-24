<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en-US" prefix="og: http://ogp.me/ns#">

	<head profile="http://gmpg.org/xfn/11">

		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
		
		<?php display_title(); ?>

		<?php do_action( 'bp_head' ) ?>

		<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />

<link rel="stylesheet" href="<?php $bloginfo = get_bloginfo( 'wpurl' ); ?>/wp-content/themes/ja_buddypress/lightbox.css" type="text/css" media="screen" />

		
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>

<script src="<?php $bloginfo = get_bloginfo( 'wpurl' ); ?>/js/jquery-ui-1.8.18.custom.min.js" type="text/javascript"></script>
<script src="<?php $bloginfo = get_bloginfo( 'wpurl' ); ?>/js/jquery.smooth-scroll.min.js" type="text/javascript"></script>
<script src="<?php $bloginfo = get_bloginfo( 'wpurl' ); ?>/js/lightbox.js" type="text/javascript"></script>

<!-- For making comment window stop -->

    <script type="text/javascript" src="<?php $bloginfo = get_bloginfo( 'wpurl' ); ?>/js/jquery.stickyPanel.min.js"></script>

    <script type="text/javascript">
        $().ready(function () {
            var stickyPanelOptions = {
                topPadding: 0,
                afterDetachCSSClass: "BoxGlow_Grey2",
                savePanelSpace: true
            };
/*            var stickyBarOptions = {
                topPadding: 250,
                afterDetachCSSClass: "BoxGlow_Grey2",
                savePanelSpace: true
            };
*/
            // multiple panel example (you could also use the class ".stickypanel" to select both)
            $("#commentwrapper").stickyPanel(stickyPanelOptions);

            $("#stickybar").stickyPanel(stickyBarOptions);

        });
    </script>
					
		<?php wp_enqueue_script('tabs_script', get_bloginfo('stylesheet_directory') . '/tab-includes/js/organictabs.jquery.js');?>
		
		<?php wp_enqueue_style('tabs', get_bloginfo('stylesheet_directory') . '/tab-includes/css/tabstyle.css');?>

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
		<link rel="icon" href="<?php echo get_site_url();?>/favicon.gif" type="image/gif" />

		<?php wp_head(); ?>
		<?php if (is_home()) { ?>
		<script type="text/javascript" src="/js/jquery.cycle.all.min.js"></script>
		<script language="javascript" src="/js/tweet/jquery.tweet.js" type="text/javascript"></script>
		<script type="text/javascript">jQuery(document).ready(function() {
			jQuery('div#featured-slider').cycle({
				next: '#next', 
				prev: '#prev', 
				fx: 'fade',
				timeout: 10000,
				speed: 350,
				pause: 1,
				random: 1
			});});</script>
			
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
	
	<script type="text/javascript">

//<![CDATA[ 
jQuery(window).load(function(){
jQuery(function() {
  var a = function() {
    var b = jQuery(window).scrollTop();
    var d = jQuery("#sticky-anchor").offset({scroll:false}).top;
    var c=jQuery(".lockit");
    if (b>d) {
      c.css({position:"fixed",top:"0px"})
    } else {
      if (b<=d) {
        c.css({position:"relative",top:""})
      }
    }
  };
  jQuery(window).scroll(a);a()
});
});//]]>  

</script>

		
		<?php
		global $bp;
		if($bp->current_component == BP_XPROFILE_SLUG){
   			?><script language="javascript" src="/js/tweet/jquery.tweet.js" type="text/javascript"></script>
   			<script src="http://platform.twitter.com/anywhere.js?id=gwaI4JpkTimXprMMaNdF0w&v=1" type="text/javascript"></script>
   			<?php
		}
		?>
	<meta name="google-site-verification" content="ae-oLPTbPxSIDVwIxM_mf6hEqwzbSf8L2ZZX_0O2f1Y" />	
			<?php if (is_single()) : ?>
		<?php global $post; ?>
		<?php endif; ?>		
	</head>

	<body <?php body_class('custom') ?> id="bp-default">
	<a name="topofpage" id="top-of-page"></a>
		<?php do_action( 'bp_before_header' ) ?>

		<div id="header">

			<?php ja_header(); ?>

		</div><!-- #header -->

		<?php do_action( 'bp_after_header' ) ?>
				
		
<div class="global_follow_btn">
<!-- LinkedIn -->
<div class="linkedin">
<a href="http://www.linkedin.com/company/journalism-accelerator/" title="JA on LinkedIn" target="_blank"><img src="/wp-content/uploads/2012/08/linkedin.png" alt="LinkedIn icon" /><span>LinkedIn</span></a>
</div>
<!-- Facebook  -->
<div class="facebook">
<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2Fjournalismaccelerator&amp;send=false&amp;layout=button_count&amp;width=100&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=verdana&amp;height=21&amp;appId=223081781069699" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height:21px;" allowTransparency="true"></iframe>
</div>
<!-- Twitter -->
<div class="twitter">
				<a href="https://twitter.com/journaccel" class="twitter-follow-button" data-show-count="true" data-lang="en">Follow @journaccel</a>
<script type="text/javascript">!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
</div>
</div>
		
<div id="cat_nav">
	<h2><a href="/topics/questions/" class="questiontab <?php if (is_home()) { } elseif (in_category(28) || is_category(28) || parent_category_is(28)) { echo 'active'; } elseif (is_home()) { } else { } ?>">Questions</a> <a href="/resources/" class="resourcetab <?php if (is_home()) { } elseif (in_category(25) || is_category(25) || parent_category_is(25)) { echo 'active'; } else { } ?>">Resources</a> <a href="/blog/" class="<?php if (is_home()) { } elseif (in_category(39) || is_category(39) || parent_category_is(39)) { echo 'active'; } else { } ?>">Blog</a> <a href="/projects-archive/" class="projecttab <?php if (is_home()) { } elseif (is_page('projects-archive') || 'projects' == get_post_type()) { echo 'active'; } else { } ?>">Projects</a> <a href="/about/what-is-the-journalism-accelerator/" class="abouttab <?php if (is_home()) { } elseif (is_page( 'about' ) || '2134' == $post->post_parent) { echo 'active'; } else { } ?>">About</a></h2>

<!-- Determine what submenu/pills to display -->
<?php 
if (is_page('projects-archive') || is_page( array( 2189, 2172, 9592, 2138, 10058, 2167, 2185, 2281 )) || 'projects' == get_post_type()) :

echo "<div class='menu_reference'>Resources:</div>";
wp_nav_menu(array(
		'theme_location' => 'resources',
		'container' => 'none',
		'menu_class' => 'resources',
		'menu_id' => 'resource-menu'
	));

elseif( is_home() ):
echo "<div class='menu_reference'>Resources:</div>";
	wp_nav_menu(array(
		'theme_location' => 'resources',
		'container' => 'none',
		'menu_class' => 'resources',
		'menu_id' => 'resource-menu'
	));

elseif (in_category(28) || is_category(28) || parent_category_is(28)) :

echo "<div class='menu_reference'>Questions:</div>";
	wp_nav_menu(array(
		'theme_location' => 'questions',
		'container' => 'none',
		'menu_class' => 'questions',
		'menu_id' => 'questions-menu'
	)); ?>
	<?php elseif (in_category(25) || 
	is_category(25) || 
	parent_category_is(25) || 
	in_slug('members') || 
	is_page() || 
	is_home()) :

	echo "<div class='menu_reference'>Resources:</div>";	
	wp_nav_menu(array(
		'theme_location' => 'resources',
		'container' => 'none',
		'menu_class' => 'resources',
		'menu_id' => 'resource-menu'
	)); ?>
	<?php elseif (is_category(39) || in_category(39) || parent_category_is(39) && !in_slug('members') && !is_home()) :
	
	echo "<div class='menu_reference'>Blog:</div>";
	wp_nav_menu(array(
		'theme_location' => 'blog',
		'container' => 'none',
		'menu_class' => 'blog',
		'menu_id' => 'blog-menu'
	)); ?>
	<?php endif; ?>

<ul class="subnav">
	<li><a href="/" class="first">Home</a></li>
<!-- Commenting out because its now apart of main navigation
	<li><a href="/about/what-is-the-journalism-accelerator/">About</a></li>
-->
	<li><a href="/about/what-is-the-journalism-accelerator/#faq">FAQ</a></li>
	<li><a href="/members/">People</a></li>
	<?php if (current_user_can('publish_posts')) { ?> 
	<li><a href="/wp-admin/">Dashboard</a></li><?php } ?>
</ul>
<div class="clear"></div>
</div>
		
<?php do_action( 'bp_before_container' ) ?>

<div id="container">