/* HELPERS*/
String.prototype.parseHashtag = function() {
		return this.replace(/[#]+[A-Za-z0-9-_]+/g, function(t) {
			var tag = t.replace("#","%23")
			return t.link("http://search.twitter.com/search?q="+tag);
		});
};
String.prototype.parseURL = function() {
	return this.replace(/[A-Za-z]+:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_:%&\?\/.=]+/g, function(url) {
		return url.link(url);
	});
};

String.prototype.parseUsername = function() {
	return this.replace(/[@]+[A-Za-z0-9-_]+/g, function(u) {
		var username = u.replace("@","")
		return u.link("http://twitter.com/"+username);
	});
};

/*
 * Function: prettyDate
 * JavaScript Pretty Date
 * Copyright (c) 2008 John Resig (jquery.com)
 * Licensed under the MIT license
 * Description: Takes an ISO time and returns a string representing how long ago the date represents.	
*/
function prettyDate(time){
	var date = new Date((time || "").replace(/-/g,"/").replace(/[TZ]/g," ")),
		diff = (((new Date()).getTime() - date.getTime()) / 1000),
		day_diff = Math.floor(diff / 86400);
			
	if ( isNaN(day_diff) || day_diff < 0 || day_diff >= 31 )
		return;
			
	return day_diff == 0 && (
			diff < 60 && "just now" ||
			diff < 120 && "1 minute ago" ||
			diff < 3600 && Math.floor( diff / 60 ) + " minutes ago" ||
			diff < 7200 && "1 hour ago" ||
			diff < 86400 && Math.floor( diff / 3600 ) + " hours ago") ||
		day_diff == 1 && "Yesterday" ||
		day_diff < 7 && day_diff + " days ago" ||
		day_diff < 31 && Math.ceil( day_diff / 7 ) + " weeks ago";
}

// If jQuery is included in the page, adds a jQuery plugin to handle it as well
if ( typeof jQuery != "undefined" )
	jQuery.fn.prettyDate = function(){
		return this.each(function(){
			var date = prettyDate(this.title);
			if ( date )
				jQuery(this).text( date );
		});
};

function printTweet(tweet, target){
	if(!tweet) return false;
	target.append(
		'<div class="jptc_tweet" id="jptc_tweet_'+ tweet.id_str + '">' +
			'<div class="jptc_tweet_avatar"> <img src="' + tweet.profile_image_url +'"/> </div>' +
			'<div class="jptc_tweet_content">'  + 
			'<p class="jptc_tweet_text">' +
			'<span class="jptc_tweet_username"><a href="https://twitter.com/#!/' + tweet.from_user + '"> '+ tweet.from_user + '</a>:</span> ' +
			tweet.text.parseURL().parseUsername().parseHashtag() + 
			'<br/><span class="jptc_tweet_meta">'+ 
			'<a href="http://twitter.com/#!/'+tweet.from_user+'/status/'+tweet.id_str+'">'+prettyDate(tweet.created_at)+'</a> '+
			'• <a href="http://twitter.com/intent/tweet?in_reply_to='+tweet.id_str+'">Reply</a> ' +
			'• <a href="http://twitter.com/intent/retweet?tweet_id='+tweet.id_str+'">Retweet</a> ' +
			'• <a href="http://twitter.com/intent/favorite?tweet_id='+tweet.id_str+'">Favorite</a> ' +

			'</span>' +
		'</p></div></div>'
	);
}	

var jptc_jq = jQuery;
jptc_jq(document).ready(function($) { // let's the fun begin
	// var jptc_rt_query, var jptc_saved_tweets, var jptc_jptc_rt_count are defined by jptc_widget.php
	var api_url = 'http://search.twitter.com/search.json?q=';
	var rt_tweets; // contains tweets fetched by the realtime (rt) query (client side, JSON FORMAT)
	var widget = $('#jptc_box');
	var all_tweets_btn  = $("#jptc_show_all_tweets");
	var close_popup_btn  = $("#jptc_close_popup_btn");
	var all_tweets_content = $("#jptc_all_content");
	var all_tweets_box = $("#jptc_all_box");
	
	// Insert saved tweets in the widget && in the all tweets popup
	if(jptc_saved_tweets){
		$.each(jptc_saved_tweets.results, function(i, item) {
			printTweet(item, widget );
			printTweet(item, all_tweets_box);
		});
	}
	
   // contact the twitter search API
   if(jptc_rt_query && jptc_rt_count > 0){
		request_url = api_url + jptc_rt_query + "&rpp="+jptc_rt_count+"&callback=?";
		$.getJSON(request_url, function(data) { // ASYNCHRONOUS!
			twitter_search_callback(data);
		});
	}
	else { // if no RT query set, rotate the saved tweets
		startAnimation();
	}

	// callback - exececuted when we receive the json result from twitter.com
	function twitter_search_callback(rt_tweets){ 
		$.each(rt_tweets.results, function(i, item){
			 printTweet(item, widget );
			 printTweet(item, all_tweets_box);
		});
		startAnimation();
	} 
	
	function startAnimation(){
		widget.parent().append('<div id="jptm_controls"></div>');
		widget_controls = $("#jptm_controls");
		widget_controls.append('<span id="jptc_up"> &uarr; </span>');
		widget_controls.append('<span id="jptc_down"> &darr; </span>');
		
		widget.cycle({ 
			fx:      'scrollVert', 
			speed:    1500, // speed of the transition
			timeout:  7000, // milliseconds between slide transitions
			prev:   '#jptc_up', 
			next:   '#jptc_down',
			height: 'auto',
			autostop: 0, 
			autostopCount: 0,
			pause: true // pause on hover
		});
	}
	
	// More Related Tweets Popup
	all_tweets_content.hide();

	function loadPopup(){
			all_tweets_content.css({
				'opacity': '0.98'
			});
			all_tweets_content.fadeIn(150);
	}
	
	function disablePopup(){
			all_tweets_content.fadeOut(10);
	}
	
	all_tweets_btn.click(function(){
		loadPopup();
	});

	close_popup_btn.click(function(){
		disablePopup();
	});
	
	all_tweets_content.click(function(e){ // stop from closing the window when click on a link
		 var target  = $(e.target);
			if( target.is('a') ) {
			return true; 
		}
		//disablePopup();
	});
	
}); // end jquery(document)



