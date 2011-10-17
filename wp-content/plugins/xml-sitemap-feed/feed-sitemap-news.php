<?php
/**
 * XML Sitemap Feed Template for displaying an XML Sitemap feed.
 *
 * @package XML Sitemap Feed plugin for WordPress
 */

status_header('200'); // force header('HTTP/1.1 200 OK') for sites without posts
header('Content-Type: text/xml; charset=' . get_bloginfo('charset'), true);

echo '<?xml version="1.0" encoding="'.get_bloginfo('charset').'"?>
<!-- generated-on="'.date('Y-m-d\TH:i:s+00:00').'" -->
<!-- generator="XML & Google News Sitemap Feed plugin for WordPress" -->
<!-- generator-url="http://4visions.nl/en/wordpress-plugins/xml-sitemap-feed/" -->
<!-- generator-version="'.XMLSF_VERSION.'" -->
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">';

$maxURLS = 1000;	// maximum number of URLs allowed in a news sitemap.

// editing below here is not advised!

// Register the filtering function
add_filter('posts_where', array('XMLSitemapFeed','xml_sitemap_feed_news_filter_where'), 10, 1 );

// Perform the query, the filter will be applied automatically
query_posts( array(
	'post_type' => 'post', 
//	'post_status' => 'publish', 
	'caller_get_posts' => 1,
//	'nopaging' => true,
	'posts_per_page' => -1 )
); 

global $wp_query;
$wp_query->is_404 = false;	// force is_404() condition to false when on site without posts
$wp_query->is_feed = true;	// force is_feed() condition to true so WP Super Cache includes
				// the sitemap in its feeds cache

// prepare counter to limit the number of URLs to the absolute max of 50.000
$counter = 1;

// loop away!
if ( have_posts() ) : while ( have_posts() && $counter < $maxURLS ) : the_post();

	// check if we are not dealing with an external URL :: Thanks, Francois Deschenes :)
	if(!preg_match('/^' . preg_quote(get_bloginfo('url'), '/') . '/i', get_permalink())) continue;
	
	// get the article language
	// qTranslate compatibility, xLanguage no longer developed ?
	//global $q_config;
	//if ( isset($q_config['language']) )
	//	$lang = $q_config['language'];
	//elseif ( get_option('WPLANG') )
	//	$lang = get_option('WPLANG');
	//else
	//	$lang = 'en';
	//$lang_arr = explode( '_', $lang, 5 );
	
	// get the article tags
	// TODO : include categories too ??

	$keys_arr = get_the_tags();
	
?><url><loc><?php echo esc_url( get_permalink() ) ?></loc><news:news><news:publication><news:name><?php bloginfo('name'); ?></news:name><news:language><?php echo get_option('rss_language'); ?></news:language></news:publication><news:publication_date><?php echo mysql2date('Y-m-d\TH:i:s+00:00', $post->post_date_gmt, false); ?></news:publication_date><news:title><?php the_title(); ?></news:title><news:keywords><?php $comma = 0; if ($keys_arr) foreach($keys_arr as $key) { if ( $comma == 1 ) { echo ', '; } echo $key->name; $comma = 1; } ?></news:keywords><news:genres>Blog</news:genres></news:news></url><?php 

	$counter++;

endwhile; endif; 
	// see what we can do for :
	//<news:access>Subscription</news:access> (for now always leave off)
	// and
	//<news:genres>Blog</news:genres> (for now leave as Blog)
	// http://www.google.com/support/news_pub/bin/answer.py?answer=93992
	
	// lees over indienen:
	// http://www.google.com/support/news_pub/bin/answer.py?hl=nl&answer=74289

?></urlset>
