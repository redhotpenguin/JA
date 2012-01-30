jQuery(document).ready(function($) {
		spin_wheel = $("<span class = 'jptc_spin_wheel'>  <img src='/wp-content/plugins/tweet-catcher/img/spin.gif' /> </span>");

		$('#get_tweets_button').click(search_twitter);

		function search_twitter(){
			search_query = $("#search_query_input").val();
			var data = {
				action: 'my_jptc',
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
				action: 'my_jptc',
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
					action: 'my_jptc',
					_function: 'delete_tweet',
					_tweet_id: tweet_id,
					_post_id : post_id
				};
				
				
					
			$.post(ajaxurl, data, function(response) {
				$('#saved_tweets').html('');
				$('#saved_tweets').html(response);
			});
			
		   });


	// capture Return key event, display the tweets instead of reloading the page
	$("#search_query_input").bind("keypress", function(e){ 
		if (e.keyCode == 13) {
			search_twitter();
			return false;
		}
	});
	
 
	$('.admin_delete_tweet').live("click", admin_delete_tweet);
	function admin_delete_tweet(){

		tweet_id = $(this).attr('id');
		parent_id = $(this).attr('name');
		
		var parent = $(this).parent().parent();
		
		tweet_counter = $('#jptc_tweet_counter');
	
		
		var data = {
					action: 'my_jptc',
					_function: 'admin_page_delete_tweet',
					_tweet_id: tweet_id,
					_post_id : parent_id
				};
				
				parent.fadeTo(1, 0.5);
		
		
			$.post(ajaxurl, data, function(response) {
					tweet_counter.text( parseInt( tweet_counter.text()) - 1 );
					parent.animate({ height: 'toggle', opacity: 'toggle', easing:'swing' }, 'slow', function(){
						parent.remove(); 
					});
			});
			
			

		return false;
	} 
	
	$("#jptc_more_tweets_btn").click(show_more_tweets);
	function show_more_tweets(){
		$(this).append(spin_wheel);
		var data = {
					action: 'my_jptc',
					_function: 'admin_page_more_tweets'
		};
		
		$.post(ajaxurl, data, function(response) {
			if(response != false){
				$("#last_tweets").append( $(response) .hide().fadeIn(300) );
			}
			else{
				no_more = $("<span class = 'jptc_no_more'> No more Tweets.</span>");
				$("#last_tweets").append( no_more );
				$(".jptc_no_more").delay(1000).fadeOut(1500);	
			}
			$('.jptc_spin_wheel').remove();
		});
		//mutex_comment_is_loading = false;
	}
/*	
var mutex_comment_is_loading = false;
$(document).scroll( handle_tweets_on_scroll );	
function handle_tweets_on_scroll(){
	if( is_near_bottom( 3 ) == true && !mutex_comment_is_loading ){
			mutex_comment_is_loading = true;
			show_more_tweets();
		}
}	

function is_near_bottom( factor ) {
var documentHeight = $(document).height(); 
var scrollPosition = $(window).height() + $(window).scrollTop(); 

virtualHeight = parseInt( documentHeight / factor) ;
return ( (scrollPosition == documentHeight ) || ( scrollPosition >= documentHeight - virtualHeight ) );
} 
*/

});
