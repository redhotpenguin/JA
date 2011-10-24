<?php

class Tweet_Manager_Widget extends WP_Widget {
	private $jptc_model;
	private $plugin_url;
	private $rt_query;
	private $rt_count;
	private $tweets;
	
	function Tweet_Manager_Widget() {
		parent::WP_Widget( 'jptcwidget', $name = 'Tweet Catcher', array('description'=>'Ultimate Tweet Catcher') );
		$this->plugin_url = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		$this->jptc_model = new Tweet_Manager_Model();
		
		if(!is_admin()) {
			//add_action('wp_head', array(&$this, 'add_widget_css'));
		}
		add_action('wp_enqueue_scripts', array(&$this, 'register_js_scripts'));
		add_action('wp_head', array(&$this, 'register_styles'));
	}
	
	function register_js_scripts(){
		wp_register_script( 'jquery-cycle', $this->plugin_url.'javascript/jquery.cycle.all.min.js', array('jquery'));
		wp_register_script('jptc-widget', $this->plugin_url.'javascript/jptc_widget.js', array('jquery-cycle'));
	}
	
	public function register_styles(){
		wp_register_style('jptc-widget-style', $this->plugin_url.'css/jptc_widget.css');
	}
	
	function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
		}
		else {
			$title = __( 'Related Tweets', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget name:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<?php 
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}
	
	function widget($args, $instance) {
	$permalink =  get_permalink();
	$request =  'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	// make sure we load the widget in the right page

		if((is_single() || is_page() & ($permalink == $request))){
			$this->load_widget($args, $instance);
		} 
		else {return false;}
		
	}
	
	function load_widget($args, $instance){
	global $post;
	 $post_id = $post->ID;
    $this->rt_query = get_post_meta($post_id, 'jptc_rt_query', true);
	$this->rt_count = get_post_meta($post_id, 'jptc_rt_count', true);
	$this->tweets = $this->jptc_model->get_json_saved_tweets($post_id);

	if((!$this->rt_query && empty( $this->tweets)) || ( empty( $this->tweets) && $this->rt_count == 0) ) { return false; }
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title )
		echo $before_title . $title;
		echo '<span id="jptc_show_all_tweets">More</span>';

		echo $after_title; 
		$this->widget_content();
		echo $after_widget;
	}
	
	private function widget_content(){
		wp_print_scripts( 'jquery-cycle' );
		wp_print_scripts( 'jptc-widget' );
		wp_print_styles('jptc-widget-style');
		?>
			<script type="text/javascript">
				<?php 
				if(!empty($this->tweets)){
					// goal is to uniformize all tweets from the db & twitter.com using JSON
					echo 'var jptc_saved_tweets = { "results": ['; // create a json structure of db tweets
					foreach( $this->tweets as $tweet){
						echo $tweet.',';
					}
					echo ']};';
				}
				else {
					echo 'var jptc_saved_tweets="";';
				}
			?>
			var jptc_rt_count = '<?php echo $this->rt_count; ?>';
			var jptc_rt_query = '<?php echo urlencode($this->rt_query);?>';
			</script>

			<div id="jptc_box"></div>
			<div id="jptc_all_content">
				<div class="jptc_all_content_header">
					Tweets Related To: <?php echo '<a href="'.get_permalink().'">'.get_the_title().'</a>'; ?>
					<span id="jptc_close_popup_btn">close</span>
				</div>
				<div id="jptc_all_box"></div>
			</div>
			
			<div class="jptc_follow_btn">
				<a href="https://twitter.com/journaccel" class="twitter-follow-button" data-show-count="false">Follow @journaccel</a>
				<script src="//platform.twitter.com/widgets.js" type="text/javascript"></script>
			</div>
		<?php
	}
	
	

}
?>