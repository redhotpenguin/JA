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
	

	public function search_twitter($search_query){
		if(empty($search_query)) { echo 'Please enter a search query';  return false;}
	
		$search_query = urlencode($search_query);
		$search_request = "http://search.twitter.com/search.json?q=$search_query&rpp=100";

		$stream = @file_get_contents($search_request, true);
		if(empty($stream)) { echo '<a class="query_error" href="'.$search_request.'">Invalid Request :(</a>' ;return 0; }
		$decoded_stream = json_decode($stream,true);
		if(empty($decoded_stream['results'])) { echo '<a class="query_error" href="'.$search_request.'">No results found :(</a>'; return 0;}
		
	foreach($decoded_stream['results'] as $tweet){ //store each tweet in an array of Tweet
			$tweet_id = $tweet['id_str'];
			$tweet_author = $tweet['from_user'];
			$tweet_text = $tweet['text'];
			$tweet_img = $tweet['profile_image_url'];
			$tweet_date = $tweet['created_at'];
			$tweet = new Tweet($tweet_id, $tweet_author, $tweet_text, $tweet_img ,  $tweet_date);
			$tweet_array[] = $tweet;
	}
	
	foreach($tweet_array as $tweet){ ?>
		<div class="tweet" id="tweet-<?php echo $tweet->get_tweet_id(); ?>">
			<div class="tweet_img">
				<img class='tweet_avatar' src="<?php echo $tweet->get_tweet_avatar();?>"/>
			</div>
			
			<div class="tweet_content">
				<strong><a class='tweet_username' href="https://twitter.com/#!/<?php echo $tweet->get_tweet_username();?>"><?php echo $tweet->get_tweet_username();?></a></strong>
				<p class='tweet_text'><?php echo $tweet->get_tweet_text();?> 		</p>
				<span class="tweet_date"><a href="https://twitter.com/#!/pulistbook/status/<?php echo $tweet->get_tweet_id(); ?>"><?php echo $tweet->get_tweet_date();?></a></span>
				<span class='add_tweet_button' id='<?php echo $tweet->get_tweet_id(); ?>' >+</span>
		
			</div>
		</div>
		<?php
	}

	}
	
	public function save_tweet($param){
		$param['_tweet_text'] = stripslashes($param['_tweet_text']);
		$tweet = new Tweet($param['_tweet_id'], $param['_tweet_username'], $param['_tweet_text'], $param['_tweet_avatar'],  $param['_tweet_date'] );
		
		$post_id = $param['_post_id'];
		if(add_post_meta($post_id, 'jptc_tweet', $tweet)) return true;
		else return false;
	}
	
	public function the_saved_tweets($post_id){
		
		$saved_tweets = get_post_meta($post_id, 'jptc_tweet');
		$saved_tweets = (object) $saved_tweets; // convert the array to an object
		foreach($saved_tweets as $tweet){
			?>
			<div class="tweet">
				<div class="tweet_img">
					<img class='tweet_avatar' src="<?php echo $tweet->get_tweet_avatar();?>"/>
				</div>
				
				<div class="tweet_content">
					<strong><a class='tweet_username' href="https://twitter.com/#!/<?php echo $tweet->get_tweet_username();?>"><?php echo $tweet->get_tweet_username();?></a></strong>
					<p class='tweet_text'><?php echo $tweet->get_tweet_text();?> 		</p>
					<span class="tweet_date"><a href="https://twitter.com/#!/pulistbook/status/<?php echo $tweet->get_tweet_id(); ?>"><?php echo $tweet->get_tweet_date();?></a></span>
					<span class='delete_tweet_button' id='<?php echo $tweet->get_tweet_id(); ?>' >-</span>
			
				</div>
			</div>
	
			<?php
		}
	}
	
		public function get_saved_tweets($post_id){
			$saved_tweets = get_post_meta($post_id, 'jptc_tweet');
			if(empty($saved_tweets)) return false;
			else return  (object) $saved_tweets; // convert the array to an object
		}
		
		public function get_json_saved_tweets($post_id){
			$saved_tweets = get_post_meta($post_id, 'jptc_tweet');
			if(empty($saved_tweets)) return false;
			/*
				This version of PHP doesn't support well json_encode with objects
				we first have to turn our objects into arrays before we can json_encode them.
			*/
				foreach($saved_tweets as $tweet){
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
		 $delete_request = "DELETE FROM wp_postmeta WHERE meta_value LIKE '%$tweet_id%' AND meta_key = 'jptc_tweet'  and post_id = $post_id";
		 if($wpdb->query($delete_request)) return true;
		 else return false;
	
	}

}


?>