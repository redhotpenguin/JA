<?php
if (!function_exists ('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
include('tweet.php');
class Tweet_Manager_Model{
	public function __construct(){

	}
	
	public function actually_search_twitter($search_query){
		$search_query = urlencode($search_query);
		$search_request = "http://search.twitter.com/search.json?q=$search_query&rpp=100";
		$stream = @wp_remote_get($search_request);
			
		if(!empty($stream)) {
			$decoded_stream = json_decode( $stream['body'], true );
			if(empty($decoded_stream['results'])) {  return -1; }
			
			foreach($decoded_stream['results'] as $tweet){ //store each tweet in an array of Tweet
				$tweet_id = $tweet['id_str'];
				$tweet_author = $tweet['from_user'];
				$tweet_text = $tweet['text'];
				$tweet_img = $tweet['profile_image_url'];
				$tweet_date = $tweet['created_at'];
				$tweet = new Tweet($tweet_id, $tweet_author, $tweet_text, $tweet_img ,  $tweet_date);
				$tweet_array[] = $tweet;
			}
				return $tweet_array;
		}
		else return false;
	}
	
	public function search_twitter($search_query){
		if(empty($search_query)) { echo 'Please enter a search query';  return false;}
		
		$tweet_array = $this->actually_search_twitter($search_query);
		
		
	if(!$tweet_array){
		echo '<a class="query_error" href="'.$search_request.'">Invalid Request :(</a>' ;return 0; 
	}
	elseif($tweet_array == -1){
		echo '<a class="query_error" href="'.$search_request.'">No results found :(</a>'; return 0;
	}
	
	foreach($tweet_array as $tweet){ ?>
		<div class="tweet" id="tweet-<?php echo $tweet->get_tweet_id(); ?>">
			<div class="tweet_img">
				<img class='tweet_avatar' src="<?php echo $tweet->get_tweet_avatar();?>"/>
			</div>
			
			<div class="tweet_content">
				<strong><a class='tweet_username' href="https://twitter.com/#!/<?php echo $tweet->get_tweet_username();?>"><?php echo $tweet->get_tweet_username();?></a></strong>
				<p class='tweet_text'><?php echo $tweet->get_tweet_text();?> 		</p>
				<span class="tweet_date"><a href="https://twitter.com/#!/pulistbook/status/<?php echo $tweet->get_tweet_id(); ?>"><?php echo $tweet->get_tweet_date() ;?></a></span>
				<span class='add_tweet_button' id='<?php echo $tweet->get_tweet_id(); ?>' >+</span>
		
			</div>
		</div>
		<?php
	}

	}
	
	public function save_tweet($param, $post_id = ''){
		if( is_array($param) ){
			$param['_tweet_text'] = stripslashes($param['_tweet_text']);
			$tweet = new Tweet($param['_tweet_id'], $param['_tweet_username'], $param['_tweet_text'], $param['_tweet_avatar'],  $param['_tweet_date'] );
			$post_id = $param['_post_id'];
		}
		elseif( is_object($param) ) $tweet = $param;
	
		if ( $this->is_tweet_saved( $tweet->get_tweet_id() , $post_id))
			return false; //
			
		if(add_post_meta($post_id, 'jptc_tweet', $tweet)) return true;
		else return false;
	}
	
	public function the_saved_tweets($post_id){
		$saved_tweets = $this->get_saved_tweets($post_id);
		if( empty($saved_tweets) )
			return false;
		
		$saved_tweets = (object) $saved_tweets; // convert the array to an object
		
		foreach($saved_tweets as $tweet){
			?>
			<div class="tweet">
				<div class="tweet_img">
					<img class='tweet_avatar' src="<?php echo $tweet->get_tweet_avatar();?>"/>
				</div>
				
				<div class="tweet_content">
					<strong><a class='tweet_username' href="https://twitter.com/#!/<?php echo $tweet->get_tweet_username();?>"><?php echo $tweet->get_tweet_username();?>:</a></strong>
					<p class='tweet_text'><?php echo $tweet->get_tweet_text();?> 		</p>
					<span class="tweet_date"><a href="https://twitter.com/#!/pulistbook/status/<?php echo $tweet->get_tweet_id(); ?>"><?php echo $tweet->get_tweet_date() ; ?></a></span>
					<span class='delete_tweet_button' id='<?php echo $tweet->get_tweet_id(); ?>' >-</span>
			
				</div>
			</div>
	
			<?php
		}
	}
	
		private function extract_object($a){
			return unserialize( $a[0] );
		}
	
		public function get_saved_tweets($post_id, $order = 'DESC'){
			global $wpdb;
			
			$get_saved_tweets_query = "SELECT meta_value
				FROM  $wpdb->postmeta
				WHERE post_id = $post_id
				AND meta_key =  'jptc_tweet'
				ORDER BY  meta_id $order";

			$saved_tweets = $wpdb->get_results($get_saved_tweets_query, ARRAY_N );
		
			
			if(empty($saved_tweets)){
				return false;
			}
				
			$saved_tweets = array_map(array(&$this, 'extract_object'), $saved_tweets);
		
				
			if(empty($saved_tweets)) return false;
			else return  (object) $saved_tweets; // convert the array to an object
		}
		
		public function get_json_saved_tweets($post_id){
			$saved_tweets = $this->get_saved_tweets($post_id);
			if(empty($saved_tweets)) return false;
				foreach($saved_tweets as $tweet){
		
				if(!is_object($tweet))
					continue;
				
					$tmp_tweet = array( // 
						'id_str' => $tweet->get_tweet_id(),
						'from_user' => $tweet->get_tweet_username(),
						'profile_image_url' => $tweet->get_tweet_avatar(),
						'text' => $tweet->get_tweet_text(),
						'created_at' => $tweet->get_tweet_date()
					);
					$json_tweets[] = json_encode($tmp_tweet);
				}
				return  $json_tweets;		 
		}
	
	public function delete_tweet($param){
		global $wpdb;
		 $post_id = $param['_post_id'];
		 $tweet_id = $param['_tweet_id'];
		 
		 if( empty($post_id) || empty($tweet_id) )
			return false;
		 
		 $delete_request = "DELETE FROM $wpdb->postmeta WHERE meta_value LIKE '%$tweet_id%' AND meta_key = 'jptc_tweet'  and post_id = $post_id";
		 if($wpdb->query($delete_request)) return true;
		 else return false;
	
	}
	
	public function is_tweet_saved($tweet_id, $post_id){
		global $wpdb;
	    $query = "SELECT * FROM $wpdb->postmeta WHERE meta_value LIKE '%$tweet_id%' AND post_id = $post_id ";
		 $result = $wpdb->get_row($query);
		 if( empty($result) )
			return false;
			
		else 
			return true;
		
	}
	
	public function get_tc_posts(){
		global $wpdb;
/*
		$posts_with_tc = "(SELECT DISTINCT(post_id)  FROM $wpdb->postmeta  
		WHERE meta_key = 'jptc_tweet'
		and meta_value != '')
		UNION 
	   (SELECT DISTINCT(post_id) FROM  $wpdb->postmeta 
		WHERE meta_key = 'jptc_rt_query'
		and meta_value != '' )
		ORDER BY  post_id DESC
		;";
		
*/

	 $posts_with_tc="
	(SELECT DISTINCT($wpdb->postmeta.post_id)  FROM $wpdb->postmeta , $wpdb->posts
		WHERE $wpdb->postmeta.meta_key = 'jptc_tweet'
		and $wpdb->postmeta.meta_value != ''
		and $wpdb->postmeta.post_id = wp_posts.ID
		and $wpdb->posts.post_status = 'publish')

		UNION(
		SELECT DISTINCT($wpdb->postmeta.post_id) FROM  $wpdb->postmeta , $wpdb->posts
		WHERE $wpdb->postmeta.meta_key = 'jptc_rt_query'
		and $wpdb->postmeta.meta_value != '' 
		and $wpdb->postmeta.post_id = wp_posts.ID
		and $wpdb->posts.post_status = 'publish'
		)ORDER BY  post_id DESC LIMIT 100;
	";
		
		
		$post_ids = $wpdb->get_results($posts_with_tc, ARRAY_N );
		if( empty ($post_ids))
			return false;
			
		else 
			return array_map(array(&$this, 'extract_first_el'), $post_ids );
	}
	
	private  function extract_first_el($a){
		return $a[0] ;
	}
	
	public function the_last_saved_tweets($limit = 10, $highlight_n =0 , $offset = 0){
//	echo "limit is $limit <br> highlight is $highlight_n <br> offset is $offset<br>";

			
		$tweets_added = $this->last_tweets_added($limit, $offset);
		if(empty($tweets_added)){
			return false;
		}

		if(!empty($highlight_n))
			$highlight_n -= 1;
		else 
			$highlight_n = false;
		
		foreach($tweets_added as $i=>$t){
			$parent_id =  $t[1]; 
			$permalink =  get_permalink($parent_id);
			$title = get_the_title($parent_id);
			$edit_link = get_edit_post_link($parent_id);
			
			if($highlight_n && $i <= $highlight_n)
				echo '<div class="last_tweet new">';
				
			else echo '<div class="last_tweet"> ';
			
			echo $tweet = unserialize( $t[0] ) ; // t[0] => Tweet
			$tweet_id = $tweet->get_tweet_id();
	
			echo "<div>Added to: <a href='$permalink' target='_blank class='permalink' > $title </a>";
			echo "<span id='$tweet_id'  name='$parent_id' class='admin_delete_tweet'>Delete</span>";
			echo "<a href='$edit_link' target='_blank' class='editlink' >Edit Post</a></div>";
			
			echo '</div>';
		}
	}
	
	public function last_tweets_added( $limit = 10, $offset = 0 ){
		global $wpdb;
		$last_tweets_query = "SELECT meta_value, post_id FROM $wpdb->postmeta WHERE meta_key = 'jptc_tweet' ORDER BY meta_id DESC LIMIT $limit OFFSET $offset";
		$last_saved_tweets = $wpdb->get_results($last_tweets_query, ARRAY_N );

		if(empty($last_saved_tweets)) return false;
		else return $last_saved_tweets;
	}
	
	public function get_total_saved_tweets(){
		global $wpdb;
		$count_all_tweets_query = "SELECT COUNT( * ) FROM  $wpdb->postmeta WHERE meta_key =  'jptc_tweet'";
		if($count = $wpdb->get_var($count_all_tweets_query))
			return $count;
		
		else return false;
	}
}


?>
