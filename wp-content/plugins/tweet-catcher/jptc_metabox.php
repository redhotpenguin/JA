<?php
if (!function_exists ('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
include('jptc_model.php');

class Tweet_Manager_Metabox{
	private $jptc_model;
	private $plugin_url;

    public function __construct($jptc_model, $plugin_url){
		$this->jptc_model = $jptc_model;
		$this->plugin_url  = $plugin_url;
		add_action( 'save_post', array($this,'jptc_save_postdata') );
        add_action( 'add_meta_boxes', array( &$this, 'add_metabox' ) );
		add_action('admin_head', array(&$this, 'add_js'));
		add_action('admin_head', array($this,'add_css'));
		add_action('wp_ajax_my_action', array($this,'get_tweets_stream_callback'));
		
    }

	public function add_metabox(){
		 add_meta_box( 'jptc_post_mb' , 'Tweet Catcher',array( &$this, 'metabox_content' ),'post'  ,'advanced' ,'high');
		 add_meta_box( 'jptc_page_mb' , 'Tweet Catcher',array( &$this, 'metabox_content' ),'page'  ,'advanced' ,'high');
    }
	
	public  function add_js(){ 
		echo '<script type="text/javascript" src="'.$this->plugin_url.'javascript/jptc.js" > </script>';
	}
	
	public function add_css(){
		echo  '<link rel="stylesheet" type="text/css" href="'. $this->plugin_url . 'css/jptc.css">' ; 
	}

	public function get_tweets_stream_callback(){ // ajax callbacks, crude controller
		$_function = $_POST['_function'];
			switch($_function){
				case 'get_tweets_stream': 
					$this->jptc_model->search_twitter($_POST['_search_query']);
					break;
				
				case 'save_tweet':
					$this->jptc_model->save_tweet($_POST);
					$this->jptc_model->the_saved_tweets($_POST['_post_id']);
				break;
				
				case 'delete_tweet':
					$this->jptc_model->delete_tweet($_POST);
					$this->jptc_model->the_saved_tweets($_POST['_post_id']);
				break;
			}
	
		die(); // this is required to return a proper result, not irl
    }
 
	public function jptc_save_postdata($post_id){
	global $post;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post->ID; // prevent the autosave function to alter our post metas
	}
		//update_post_meta
		$rt_query = $_POST['realtime_query_input'];
		$rt_query = str_replace('\'', '"', $rt_query);
		$rt_count = $_POST['realtime_count_input'];
	
		update_post_meta($post_id, 'jptc_rt_query', $rt_query);
		if(is_numeric($rt_count)) update_post_meta($post_id, 'jptc_rt_count', abs($rt_count));
	}
	
	public function metabox_content($post){
		 $rt_query = get_post_meta($post->ID, 'jptc_rt_query', true);
		 $rt_count = get_post_meta($post->ID, 'jptc_rt_count', true);
		
		$rt_query = str_replace('\'', '"', $rt_query);
		if(empty($rt_count) || $rt_count < 0) {
			 update_post_meta($post->ID, 'jptc_rt_count', 10); 
			 $rt_count = get_post_meta($post->ID, 'jptc_rt_count', true);	
		 }
		?>
		
		<div id="realtime_tweets_box">
			<form method="POST" action="">
				<label>Real-time query</label>
				<input type="text" name="realtime_query_input" id="realtime_query_input" value='<?php echo $rt_query; ?>'/>
				<label>get last</label>
				<input type="text" name="realtime_count_input" id="realtime_count_input" value="<?php echo $rt_count; ?>" />
				<label>Tweets</label>
				<input type="submit" id='get_rt_tweets_button' value="Save Query"/>
			</form>
			<div id="rt_help">
			<a href="http://support.twitter.com/groups/31-twitter-basics/topics/110-search/articles/71577-how-to-use-advanced-twitter-search" target="_blank">Help</a>
			(NEAR operator not supported)
			</div>
		</div>
		
		<div id="saved_tweets_wrapper">
		<div id='saved_tweets_box'>
			<input type="hidden" id='jptc_post_id' value="<?php echo $post->ID;?>"</div>
			<label>Search Twitter</label>
			<input type="text" id="search_query_input" />
			<span id='get_tweets_button' >Find Tweets</span>
			
			<div id="left_col">
			<b>Search Results:</b>
			<div id="jptc_tweet_stream"></div>
			</div>
			
			<div id="right_col">
				<b>Saved Tweets:</b>
				<div id="saved_tweets">
					<?php $this->jptc_model->the_saved_tweets($post->ID); ?>
				</div>
			</div>
		</div>
		</div>
	
	<div style='clear:both;'></div>
		<?php
    }
	


}
?>