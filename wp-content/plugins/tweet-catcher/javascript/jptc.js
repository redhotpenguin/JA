jQuery(document).ready(function($) {

		$('#get_tweets_button').click(search_twitter);
		
		function search_twitter(){
			search_query = $("#search_query_input").val();
			var data = {
				action: 'my_action',
				_function: 'get_tweets_stream',
				_search_query : search_query	
			};
	
			$.post(ajaxurl, data, function(response) {
			$('#jptc_tweet_stream').html(response);
			});
		}
	

	    $(".add_tweet_button").live("click", function(){ // save tweet
			var tweet_id = $(this).attr('id');
			var post_id = $('#jptc_post_id').val();

			tweet = $('#tweet-'+tweet_id);
			tweet_username = tweet.find('.tweet_username').text();
			tweet_text = tweet.find('.tweet_text').text();
			tweet_avatar = tweet.find('.tweet_avatar').attr('src');
			tweet_date = tweet.find('.tweet_date').text();
			
			var data = {
				action: 'my_action',
				_function: 'save_tweet',
				_tweet_id: tweet_id,
				_tweet_username: tweet_username,
				_tweet_text: tweet_text,
				_tweet_avatar: tweet_avatar,
				_tweet_date: tweet_date,
				_post_id : post_id
			};
			
			$.post(ajaxurl, data, function(response) {
				$('#saved_tweets').html(response);
			});
		});
		
		
		  
		$(".delete_tweet_button").live("click", function(){ // delete tweet
				var tweet_id = $(this).attr('id');
				var post_id = $('#jptc_post_id').val();				
				var data = {
					action: 'my_action',
					_function: 'delete_tweet',
					_tweet_id: tweet_id,
					_post_id : post_id
				};
				
					
			$.post(ajaxurl, data, function(response) {
				$('#saved_tweets').html('');
				$('#saved_tweets').html(response);
			});
			
		   });


	// capture return key event, display the tweets instead of reloading the page
	$("#search_query_input").bind("keypress", function(e){ 
		if (e.keyCode == 13) {
			search_twitter();
			return false;
		}
	});


});


