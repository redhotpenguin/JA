<?php
/*
Plugin Name:  Twitter Feed for WordPress
Plugin URI:   http://pleer.co.uk/wordpress/plugins/wp-twitter-feed
Description:  A simple Twitter feed that outputs your latest tweets in HTML into your page, template or sidebar widget. Customisable and easy to install!
Version:      0.3.1
Author:       Alex Moss
Author URI:   http://alex-moss.co.uk/

Copyright (C) 2010-2010, Alex Moss
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
Neither the name of Alex Moss or pleer nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

Credit goes to Magpie RSS for RSS to PHP integration: http://magpierss.sourceforge.net/

*/

require_once("magpie/rss_fetch.inc");


function readRss($atts) {
    extract(shortcode_atts(array(
		"id" => '108306523',
		"username" => '',
		"mode" => 'feed',
		"num" => '3',
		"encoding" => '',
		"term" => '',
		"followlink" => 'yes',
		"searchlink" => 'yes',
		"anchor" => '',
		"userlinks" => 'yes',
		"hashlinks" => 'yes',
		"timeline" => 'yes',
		"conditional" => 'yes',
		"phptime" => 'j F Y \a\t h:ia',
		"linktotweet" => 'no',
		"divid" => '',
		"ulclass" => '',
		"liclass" => '',
		"linklove" => 'yes',
    ), $atts));

	if ($mode == "feed") { $twitter_rss = "http://twitter.com/statuses/user_timeline/".$id.".rss"; }
	if ($mode == "fav") { $twitter_rss = "http://twitter.com/favorites/".$id.".rss"; }
	if ($mode == "public") { $twitter_rss = "http://search.twitter.com/search.rss?q=".$username; }
	if ($mode == "search") { $twitter_rss = "http://search.twitter.com/search.rss?q=".$term; }
	$rss = fetch_rss($twitter_rss);

	ob_start();
	$count = 1;
	$now = time();
	$page = get_bloginfo('url');

	if ($divid != "") {
		$divstart = "<div id=\"".$divid."\">\n";
		$divend = "</div>";
	}

	if ($ulclass != "") {
		$ulstart = "<ul class=\"".$ulclass."\">" . $tweet;
	} else {
		$ulstart = "<ul>" . $tweet;
	}

	foreach ($rss->items as $item) {
		if ($num > 0) {
			if ($count > $num) {
				continue;
			}
		}
		$tweet = $item['description'];
		if ($encoding == "fix") {
			$tweet = htmlentities($tweet);
		}
		if ($page != "") {
			if (!strpos($tweet, $page) === false) {
				continue;
			}
		}
		$when = ($now - strtotime($item['pubdate']));
		$posted = "";
		if ($timeline != "no") {
			$when = ($now - strtotime($item['pubdate']));
			$posted = "";
			if ($conditional == "yes") {
				if ($when < 60) {
					$posted = $when . " seconds ago";
				}
				if (($posted == "") & ($when < 3600)) {
					$posted = "about " . (floor($when / 60)) . " minutes ago";
				}
				if (($posted == "") & ($when < 7200)) {
					$posted = "about 1 hour ago";
				}
				if (($posted == "") & ($when < 86400)) {
					$posted = "about " . (floor($when / 3600)) . " hours ago";
				}
				if (($posted == "") & ($when < 172800)) {
					$posted = "about 1 day ago";
				}
				if ($posted == "") {
					$posted = (floor($when / 86400)) . " days ago";
				}
			} else {
				$date = date($phptime, strtotime($item['pubdate']));
				$posted = $date;
			}
		$entry = $entry."\n<br />".$pubtext.$posted;
		}
		if ($username != "") {
			$tweet = str_replace($username . ": ", "", $tweet);
		}
		if ($mode != "search") {
			if ($anchor == "") {
				$tweet = preg_replace("/(http:\/\/)(.*?)\/([\w\.\/\&\=\?\-\,\:\;\#\_\~\%\+]*)/", "<a href=\"\\0\" rel=\"external nofollow\">\\0</a>", $tweet);
			} else {
				$tweet = preg_replace("/(http:\/\/)(.*?)\/([\w\.\/\&\=\?\-\,\:\;\#\_\~\%\+]*)/", "<a href=\"\\0\" rel=\"external nofollow\">".$anchor."</a>", $tweet);
			}
		} else {
			$hashlinks = "no";
			$author = substr($item['author'], 0, stripos($item['author'], "@") );
			$tweet = "@".$author.": ".$tweet;
		}
		if ( $userlinks == "yes" ) {
			if ($mode == "fav") { $tweet = "@".$tweet; }
			$tweet = preg_replace("(@([a-zA-Z0-9\_]+))", "<a href=\"http://twitter.com/\\1\" rel=\"external nofollow\">\\0</a>", $tweet);
		}
		if ( $hashlinks == "yes" ) {
			$tweet = preg_replace("(#([a-zA-Z0-9\_]+))", "<a href=\"http://twitter.com/search?q=%23\\1\" rel=\"external nofollow\">\\0</a>", $tweet);
		}
		if ($timeline == "yes") {
			if ($linktotweet == "yes") {
				$tweet = $tweet . " <a href=\"".$item['link']."\" rel=\"external nofollow\">(" . $posted . ")</a>";
			} else {
				$tweet = $tweet . " (" . $posted . ")";
			}
		}
		if ($liclass != ""){
			$entry = "\n<li class=\"".$liclass."\">".$tweet."</li>";
		} else {
			$entry = "\n<li>".$tweet."</li>";
		}
		$wholetweet = $wholetweet."".$entry;
		$count++;
	}
	ob_end_flush();
	if ($followlink == "yes"){
		if ($mode == "search") {
			$linktofeed = ("<a href=\"http://twitter.com/search?q=".$term."\" rel=\"external nofollow\">view search for \"".$term."\" on twitter</a>\n");
		} else {
			$linktofeed = ("<a href=\"http://twitter.com/".$username."\" rel=\"external nofollow\">follow ".$username." on twitter</a>\n");
		}
	}
	if ($linklove != "no"){ $pleer = "\nPowered by <a href=\"http://pleer.co.uk/wordpress/plugins/wp-twitter-feed\">Twitter Feed</a><br />\n"; }
	$whole = "\n<!-- WordPress Twitter Feed Plugin: http://pleer.co.uk/wordpress/plugins/wp-twitter-feed -->\n".$divstart.$ulstart.$wholetweet."\n</ul>\n".$linktofeed.$pleer.$divend."\n";
	return $whole;
	}
define('MAGPIE_CACHE_AGE', '1*3');
add_filter('widget_text', 'do_shortcode');
add_shortcode('twitter-feed', 'readRss');
?>