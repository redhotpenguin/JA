=== Links in Captions ===
Tags: captions, links, link, link in captions, caption, image, images, editor, rich text editor, text editor, html editor
Requires at least: 2.5
Tested up to: 3.0.3
Stable tag: trunk
Contributors: katzwebdesign
Donate link:https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=zackkatz%40gmail%2ecom&item_name=Links%20in%20Captions&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8

Easily add links to image captions in the WordPress editor.

== Description ==

<blockquote><p>"Just a quick note to say that I've just downloaded the 'Links in Captions' WordPress plugin and it's working like a dream."<br /><cite>Pete</cite></p></blockquote>

<h3>Finally you can add links to your image captions.</h3>

Adding links to captions is the <a href="http://wordpress.org/extend/ideas/view/top-rated" rel="nofollow">9th most popular request</a> in the WordPress Ideas forum. 

With this plugin, you can also <strong>use <a href="http://codex.wordpress.org/Shortcode_API" rel="nofollow">shortcodes</a> in captions</strong>.

<h4>How to use</h4>
Inside the <code>caption</code> input, you can now add a link by using the following format: <code>{a href="http://www.example.com"}Anchor text here{/a}</code>, or alternatively you could use <code>{link url="http://www.example.com"}Anchor text here{/link}</code>.

<strong>You can also specify <code>target</code>, <code>title</code>, and <code>rel</code> attributes:</strong> <code>{link url="http://www.example.com" target="_blank" rel="nofollow" title="I never could do this before!"}Anchor text here{/link}</code>

> ####You may also be interested in:
> <strong><a href="http://wordpress.org/extend/plugins/rich-text-tags/">Rich Text Tags</a></strong> - Enable rich text editing of tags, categories, and taxonomies. Add value to your tag & category pages and improve your website' SEO.

== Installation ==

1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer
1. Activate the plugin
1. Edit a post or page and insert an image
1. Inside the <code>caption</code> input, you can now add a link by using the following format: <code>{link url="http://www.example.com"}Anchor text here{/link}</code>
	* Optional: You can also specify <code>target</code> and <code>rel</code> attributes: <code>{link url="http://www.example.com" target="_blank" rel="nofollow"}Anchor text here{/link}</code>

== Frequently Asked Questions == 

= What is the plugin license? =

* This plugin is released under a GPL license.

== Changelog ==

= 1.2 =
* Fixed issue where `title` wasn't displaying properly - this was because the plugin didn't generate wrapping quotes. (<a href="http://wordpress.org/support/topic/513451" rel="nofollow">Thanks, Tevya</a>)
* Added support for `{a href=""}Link{/a}` formatting instead of only `{link}`. Developers and people used to writing HTML should find this more natural.

= 1.1 = 
* Fixed issue with quotes: I did a ton of variations and possible combinations and made sure that each combination works. Now it shouldn't matter whether the caption uses single or double quotes, or even any attributes inside the link
* Added `title` attribute support

= 1.0 =
* Initial plugin release.

== Upgrade Notice ==

= 1.2 =
* Fixed issue where `title` wasn't displaying properly - this was because the plugin didn't generate wrapping quotes. (<a href="http://wordpress.org/support/topic/513451" rel="nofollow">Thanks, Tevya</a>)
* Added support for `{a href=""}Link{/a}` formatting instead of only `{link}`. Developers and people used to writing HTML should find this more natural.

= 1.1 =
* Fixed issue with quotes: I did a ton of variations and possible combinations and made sure that each combination works. Now it shouldn't matter whether the caption uses single or double quotes, or even any attributes inside the link
* Added `title` attribute support

= 1.0 = 
* Let's go linking!