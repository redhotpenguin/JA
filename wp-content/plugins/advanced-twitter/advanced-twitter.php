<?php
/*
Plugin Name: Advanced Twitter
Plugin URI: 
Description: Pull Twitter feeds according to a set of parameters and display it in the sidebar.
Version: 1.0
Author: Jonas Palmero
Author URI: 
*/

require_once($_SERVER['DOCUMENT_ROOT'] . "/wp-content/themes/ja_buddypress/wpalchemy/MetaBox.php");

if (is_admin()) { wp_enqueue_style('custom_meta_css', get_bloginfo('stylesheet_directory') . '/metatwitter.css'); }

$custom_metabox = new WPAlchemy_MetaBox(array
(
	'id' => '_twitter_meta',
	'title' => 'Advanced Twitter Settings',
	'template' => 'advanced_twitter_meta.php',
	'priority' => 'high',
	'context' => 'side'
));


wp_enqueue_script( 'jquery_tweet', '/js/tweet/jquery.tweet.js', array('jquery') );

// $wpalchemy_media_access = new WPAlchemy_MediaAccess();

// Create advanced-twitter Widget

function widget_advanced_twitter($args) {
		global $custom_metabox;

			   $custom_metabox->the_field('twitter_query_type');
			   $query_type = $custom_metabox->get_the_value();
			
			   $custom_metabox->the_field('twitter_query');
			   $query = $custom_metabox->get_the_value();
			  
			   $custom_metabox->the_field('twitter_count');
			   $count = $custom_metabox->get_the_value();

		extract($args);
		echo $before_widget;
		
	if ((is_single() || is_page()) && !empty($query)) {
		
		echo $before_title; 
		?>
		Related Tweets 
		
		<a href="#showquery" class="expand_tweet_query"><span class="expandquerylink">Query</span></a>
	
		<?php echo $after_title;?>
		
			<div class="twitter_queries" style="display:none;">
			<?php echo $query;?>
		</div>
		
<div class="text-widget2"><script src="http://widgets.twimg.com/j/2/widget.js"></script> 
<script type='text/javascript'>
new TWTR.Widget({
  version: 2,
	type: '<?php echo $query_type;?>',
    <?php 
		if($query_type== 'search') 
			{
				echo "search:'$query' ,";
				echo "height: $count*75 ,";
			}
		else{
				echo "height: $count*75 ,";
			}
	?>
  interval: 6000,
  title: ' ',
  subject: ' ',
  width: 'auto',
  theme: {
    shell: {
      background: '#ffffff',
      color: '#ffffff'
    },
    tweets: {
      background: '#ffffff',
      color: '#000000',
      links: '#046abd'
    }
  },
  features: {
    scrollbar: false,
    loop: true,
    live: true,
    hashtags: true,
    timestamp: true,
    avatars: true,
    toptweets: true,
    behavior: 'default'
  }
  <?php 
	if($query_type== 'search') echo "}).render().start()";
	else echo "}).render().setUser('$query').start()";
	?>

</script>
</div> 		
		<?php
		}
		echo $after_widget;
}

function widget_advanced_twitter_init()
{
  register_sidebar_widget(__('Advanced Twitter'), 'widget_advanced_twitter');
}

add_action("plugins_loaded", "widget_advanced_twitter_init");
	
function expand_twiter_query(){
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("a.expand_tweet_query").click(function(event) {
		event.preventDefault();
		jQuery(".twitter_queries").toggle('slow');
		});
});
</script>
<?php
} // end expand_twitter_query
add_action("wp_head", "expand_twiter_query");
	
?>