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

// $sharing_url = get_permalink();
$postid = get_the_ID();
$sharing_url = "";

$url_change_date = strtotime("7/18/2012");
$post_date = strtotime(get_the_date());
// $sharing_url = get_permalink( $post->ID );
// $category = get_the_category( $post->ID ); 
// $url_prefix = "/" . $category[0]->cat_name;
// $sharing_url = str_replace("journalismaccelerator.com/journalism-questions", "journalismaccelerator.com/questions" . $url_prefix, $sharing_url);

if ( $postid == '13569' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/craft/how-can-the-media-consistently-deliver-the-political-coverage-citizens-deserve/";

 } elseif ( $postid == '13567' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/craft/what-are-the-best-ways-to-call-out-misconstrued-facts-in-politics/";

} elseif ( $postid == '13537' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/craft/how-do-you-manage-the-ethical-minefields-of-access-journalism/";

} elseif ( $postid == '13438' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/craft/what-are-ethical-challenges-to-social-accountability-in-highstakes-political-reporting/";

} elseif ( $postid == '9526' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/technology/how-could-collaboration-increase-revenue-in-journalism/";

} elseif ( $postid == '8381' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/community/niche-news-startups-how-do-you-make-it-work-financially/";

} elseif ( $postid == '8302' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/community/local-news-startups-how-do-you-make-it-work-financially";

} elseif ( $postid == '6587' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/technology/what-kind-of-election-coverage-do-you-want-to-bring-to-your-community-in-2012";

} elseif ( $postid == '5324' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/community/what-is-the-value-of-local-tv-news/";

} elseif ( $postid == '4103' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/community/how-are-other-community-news-startups-like-the-terminal-who-have-been-in-business-for-a-few-years-building-and-sustaining-their-business-online/";

} elseif ( $postid == '2713' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/community/which-journalism-hashtags-on-twitter-do-you-find-most-useful/";

} elseif ( $postid == '1566' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/revenue/what-tools-and-metrics-are-organizations-using-to-measure-social-media-impact/";

} elseif ( $postid == '161' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/education/how-can-j-schools-teach-students-the-entrepreneurial-skills-necessary-to-succeed-in-the-marketplace/";

} elseif ( $postid == '155' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/revenue/how-are-news-producing-nonprofits-raising-up-to-33-percent-of-their-funding-through-underwriting/";

} elseif ( $postid == '151' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/experiments/what-are-some-of-the-innovative-ways-journalists-are-using-web-scraping-to-access-and-organize-data/";

} elseif ( $postid == '126' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/technology/as-mobile-news-consumption-grows-how-are-news-sites-gearing-up-with-a-consistent-user-experience-across-technology-devices/";

} elseif ( $postid == '122' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/community/how-are-news-and-information-producers-working-together-to-deliver-content-across-different-news-categories/";

} elseif ( $postid == '112' ) {

$sharing_url = "http://www.journalismaccelerator.com/questions/technology/what-sort-of-access-and-potential-applications-do-journalists-have-for-satellite-based-systems-like-modis-for-reporting/";

 }
?>
<!-- AddThis Button BEGIN -->
		<div class="addthis_toolbox addthis_default_style ">
			<ul id="addthis_list_nav">
<!-- New Print Friendly button -->
			<li><script>var pfHeaderImgUrl = 'http://www.journalismaccelerator.com/ja_logo_502.png';var pfHeaderTagline = 'Journalism%20Accelerator';var pfdisableClickToDel = 1;var pfDisablePDF = 0;var pfDisableEmail = 1;var pfDisablePrint = 0;var pfCustomCSS = '';var pfBtVersion='1';(function(){var js, pf;pf = document.createElement('script');pf.type = 'text/javascript';if('https:' == document.location.protocol){js='https://pf-cdn.printfriendly.com/ssl/main.js'}else{js='http://cdn.printfriendly.com/printfriendly.js'}pf.src=js;document.getElementsByTagName('head')[0].appendChild(pf)})();</script><a href="http://www.printfriendly.com" style="color:#6D9F00;text-decoration:none;" class="printfriendly" onclick="window.print();return false;" title="Printer Friendly and PDF"><img style="border:none;" src="http://cdn.printfriendly.com/button-print-gry20.png" alt="Print Friendly and PDF"/></a></li>

<!-- New Twitter button -->
				<li><a href="https://twitter.com/share" class="twitter-share-button" data-counturl="<?php echo $sharing_url; ?>" data-url="<?php echo $sharing_url; ?>" data-via="journaccel">Tweet This!</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></li>

<!-- New G+ button -->			
				<li><div class="g-plusone" data-size="medium" data-href="<?php echo $sharing_url; ?>"></div></li>
<!-- Place this tag after the last +1 button tag. -->
<script type="text/javascript">(function() {var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true; po.src = 'https://apis.google.com/js/plusone.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s); })();</script>

<!-- New Facebook button -->			
				<li><div class="fb-like" data-href="<?php echo $sharing_url; ?>" data-send="false" data-layout="button_count" data-width="120" data-show-faces="false" data-font="arial"></div></li>
<!-- New LinkedIn button -->							
				<li><script src="http://platform.linkedin.com/in.js" type="text/javascript"></script><script type="IN/Share" data-counter="right" data-url="<?php echo $sharing_url; ?>"></script></li>				
			</ul>
		</div>
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
<!-- AddThis Button BEGIN -->
		<div class="addthis_toolbox addthis_default_style ">
			<ul id="addthis_list_nav">
				<li id="print_friendly_btn"><a class="addthis_button_printfriendly"><img style="border:none;" src="http://cdn.printfriendly.com/button-print-gry20.png" alt="Print Friendly and PDF"/></a></li>
				<li><a class="addthis_button_tweet" tw:url="<?php echo $bitly; ?>" tw:counturl="<?php echo get_permalink($post->ID); ?>" tw:via="journaccel"></a></li>
				<li><a class="addthis_button_google_plusone" g:plusone:size="medium"></a></li>
				<li><a class="addthis_button_facebook_like" fb:like:layout="button_count"></a></li>
				<li><a class="addthis_button_linkedin_counter"></a></li>
			</ul>
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