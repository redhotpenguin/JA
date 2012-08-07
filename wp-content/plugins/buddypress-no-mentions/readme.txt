=== BuddyPress No Mentions ===
Contributors: r-a-y
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2BC635HQE9TG4
Tags: buddypress, mention, mentions
Requires at least: WP 3.0 & BuddyPress 1.2.6
Tested up to: WP 3.0.1 & BuddyPress 1.2.6
Stable tag: trunk

Disable the @mentions capability in BuddyPress. Perfect for users who don't understand Twitter!

== Description ==

Are @mentions confusing to your BuddyPress users?  Disable them with this small and nifty plugin!


== Installation ==

1. Download, install and activate the plugin.
1. That's it!
1. Read the FAQ for additional info.


== Frequently Asked Questions ==

#### Inline CSS ####

By default, the plugin uses inline CSS to hide a few elements.

If you prefer to **outright** remove these elements, read the following:

To remove the @mentions tab from the activity directory, copy over /bp-default/activity/index.php to your [child theme](http://codex.buddypress.org/theme-development/building-a-buddypress-child-theme/) and remove the hardcoded `<li id="activity-mentions">` list item.

To remove the `@user (?)` button from the member profile page, copy over /bp-default/members/single/member-header.php to your [child theme](http://codex.buddypress.org/theme-development/building-a-buddypress-child-theme/) and remove the hardcoded `<span class="highlight">` from the `<h2>`.

Then, disable the inline CSS by adding the following in wp-config.php:

`define( BP_NO_MENTIONS_DISABLE_INLINE_CSS', true );`



#### Where to find support? ####

I do not check the forums on wordpress.org.

The best place to reach me is on:

* [BuddyPress No Mentions - Support forum](http://buddypress.org/community/groups/buddypress-no-mentions)

Free support is provided when I have the time.


== Special Thanks ==
* [Jeff Sayre](http://buddypress.org/community/members/jeffsayre) - for his [WP Hook Sniffer plugin](http://wordpress.org/extend/plugins/wordpress-hook-sniffer/), which helped in debugging an action that wasn't firing!
* The Simpsons Stonecutters episode - for providing inspiration in the naming of this plugin! :)


== Donate! ==

If you downloaded this plugin and like it, please:

* [Donate!](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2BC635HQE9TG4) - Less than 0.1% of all the people who have downloaded my plugins have made a donation thus far. A small donation (no matter what the amount) does wonders!
* Rate this plugin
* Spread the gospel of BuddyPress


== Changelog ==

= 1.0.1 =
* Properly remove @mention email notifications in groups

= 1.0 =
* First version!