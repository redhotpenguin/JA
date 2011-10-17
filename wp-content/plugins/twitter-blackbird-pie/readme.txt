=== Twitter Blackbird Pie ===
Contributors: bradvin
Donate link: http://themergency.com/donate/
Tags: twitter, blackbird pie, blackbirdpie
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 0.5.1

Add awesome looking embedded HTML representations of actual tweets in your blog posts just by adding simple shortcodes.

== Description ==

** Now with Twitter Web Intents http://dev.twitter.com/pages/intents **

Add awesome looking embedded HTML representations of actual tweets in your blog posts just by adding simple shortcodes. Please read the blog post at http://themergency.com/twitter-blackbrid-pie-wordpress-plugin-03/ for more info and see a demo of the plugin in action at http://themergency.com/twitter-blackbird-pie-wordpress-plugin-demo/.

Big thanks must go out to Justin Shreve (http://justin.gs/) from Automattic, who let me reuse his code from the WordPress.com version of the Blackbird Pie plugin.

The plugins has the following features:

*   Supports Twitter Web Intents http://dev.twitter.com/pages/intents
*   TinyMCE editor button to easily search and embed a tweet
*   Support for the built-in embeds API (oEmbed), so a tweet URL on it's own line will be converted to a "pie"
*   Supports non-english tweets & names!
*	Now tweets looks pretty in RSS readers!
*   Exact same look and feel as the respective Twitter profile.
*   Allows for multiple "pies" in a single post.
*   Stores the generated HTML in a custom field (if possible), so the Twitter API is only called the first time.
*   The Custom fields used to store the tweet are hidden
*   Slightly better styling than the original Blackbird Pie
    *   Better use of the Twitter profile background image and color and tiling.
    *   Uses the Twitter profile text color.
    *   Uses the Twitter profile link color.
*   Dates are displayed like on Twitter i.e. "real time" datetime of when the tweet was tweeted. (see changelog)
*   Auto-linking of URLs, hashtags, usernames within the tweet text.
*   Use either the id or full URL of the tweet.

== Installation ==

1. Upload the plugin folder 'twitter-blackbird-pie' to your `/wp-content/plugins/` folder
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Insert shortcodes into your pages or posts e.g. [blackbirdpie id="13794126295"] or [blackbirdpie url="http://twitter.com/themergency/status/13968912427"]

== Screenshots ==

1. Example pies
2. Easily embed your tweet from the HTML editor
3. Search someone's twitter timeline to pick which tweet to embed
4. Tweets look pretty from RSS readers now :)
5. Twitter field added to WordPress user profile

== Changelog ==

= 0.5.1 =
* Adds a twitter field in the user profile page so each user can capture their twitter username. This username is then used as the 'related' user for web intents. 
* Included fix to allow for PHP 4 (replaced private variable declarations with var)
* included a json_decode override so the default is used on PHP 5 and falls back to function if PHP 4
* made some changes to the output tweet HTML and fixed up some CSS styling issues

= 0.5 =
* Included support for Twitter Web Intents
* bug fix : checks for mb_strlen and uses alternative if mb_str is not installed in php
* bug fix : JS error due to w3 Total cache minification of HTML (http://wordpress.org/support/topic/plugin-twitter-blackbird-pie-not-working)
* bug fix : Time stamp was 'frozen'

= 0.4.1.1 =
* Fixed JS bug in the search popup - no results were ever shown

= 0.4.1 =
* Twitters tweetproxy (http://media.twitter.com/tweetproxy/) was nuked so had to change the API calls
* PLEASE UPGRADE!

= 0.4 =
* Fixed tweets in RSS feeds!!! They now look great!
* create a custom filter that you can now use to override the HTML of the embedded tweet 'bbp_create_tweet'
* only the data that is used to display the tweet is saved, not the whole json response from the twitter API
* most styles moved to be inline. Hopefully this will solve the RSS fee issues
* tweet and user links now point to the new hashbang (#!) urls
* extensive code cleanup with some help from #plugindevbook
* refactored the tweet output HTML completely!

= 0.3.7 =
* Fixed bug in tinyMCE button search where the wrong tweet ID was being inserted

= 0.3.6 =
* Added support to allow for HTTPS tweet URLs

= 0.3.5 = 
* made a change to overcome a parse error some users had. The error details were "Parse error: syntax error, unexpected ‘”‘ in blackbird-pie.php on line 15" 

= 0.3.4 = 
* added support for NewTwitter URL formats
* renamed Autolink.php to autolink.php (lowercase) to overcome some issues

= 0.3.3 =
* fixed a plugin conflict issue with the Services_JSON class
* added class='bbpBox' to the generated div for a tweet, so you can modify some styling
* PLEASE UPGRADE!

= 0.3.1 - 0.3.2 =
* removed some embarresing print_r and echo statements used during testing

= 0.3 =
* Added a button into the tinyMCE editor to easily embed a tweet
* Added support for oEmbed, so embed your tweets just by pasting the tweet URL on it's own line. Read more here : http://www.viper007bond.com/2009/10/13/easy-embeds-for-wordpress-2-point-9/
* Changed the custom fields to be hidden (they are prefixed with an underscore)
* Fixed some bugs

= 0.2.6 =
* Fixed a bug with non english usernames showing as numbers

= 0.2.5 =
* Fixed a bug where the tweet was blank when it included quotes (")
* Removed some debugging echos (DOH!!!)
* Forcing new version

= 0.2.3 =
* Fixed bug with non english characters showing as numbers in the tweet text

= 0.2.2 =
* Fixed bug introduced in ver 0.2.1 where Twitter API was being called on every request

= 0.2.1 =
* Fixed bug introduced in ver 0.2 where the time was not updating (e.g. "1 hour ago" was being saved into the custom field
* Fixed JSON encoding bug

= 0.2 =
* Removed dependency on Jquery TimeAgo plugin and using a php function instead

= 0.1.5 =
* Updated the CSS incl. adding a few "!important" rules to make sure the theme CSS does not override it.
* Fixed bug for profile background image tile not working.
* Fixed bug for the date or the tweet. It now takes into account the timezone.

= 0.1 =
* Initial Relase. First version.

== Frequently Asked Questions ==

= How do I use this plugin? =
You insert shortcodes into your blog posts or pages, e.g. [blackbirdpie id="13794126295"] or [blackbirdpie url="http://twitter.com/themergency/status/13968912427"]

== Upgrade Notice ==

There is no upgrade notice