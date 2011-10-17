=== Simple Google Sitemap XML ===
Contributors: iTx Technologies
Tags: Google Webmaster Tools, sitemap, XML, SEO
Requires at least: 2.0
Tested up to: 3.1.0
Stable tag: 1.4

Simple Google Sitemap XML generates a valid Google XML sitemap.

== Description ==

Simple Google Sitemap XML generates a valid Google XML sitemap, which can then be used with Google's Webmaster Tools.

This plugin is meant to be simple.  It just works!  

You can now tweak the following settings in the Options panel :
     * enable/disable priority and/or frequency
     * enable/disable the tags
     * enable/disable the categories
     * choose to store your sitemap XML file in the root of your website or in the plugin's directory

Every time you publish a new post/page or delete a post/page, a new sitemap gets created automatically which means you don't have to do anything !

This plugin is offered to you by [iTx Technologies](http://itx-technologies.com/)

== Installation ==

1. Upload the Simple Google Sitemap XML plugin
2. Activate the plugin
3. Modify the plugin in the admin (using the Google Sitemap XML link).  You can choose the frequency, the priority, and where to store your XML sitemap.
4. In the admin, you can copy/paste the XML URL to your Google Webmaster Tools account, and tweak some settings.
5. Every time you create/delete a new post/page, a new XML sitemap gets created automatically.

== Screenshots ==
You can view a screenshot [here](http://itx-technologies.com/blog/simple-google-sitemap-xml-for-wordpress)

== Changelog ==

= 1.0 =
* Initial release.

= 1.1 =
* Fixed a bug in the permalink structure

= 1.2 =
* The generated XML sitemap now includes the URL's using the website's permalink structure
* Fixed a problem where users had a permission issue when installing their plugin

= 1.3 =
* A new option lets you choose where to store your XML sitemap.  Default is the plugin's directory.

= 1.3.1 = 
* Fixed a small typo in the frequency dropdown menu, which made the sitemap not validate when a monthly frequency was chosen

= 1.3.2 = 
* Improved the MySQL query

= 1.4.0 =
You can now tweak the following settings in the Options panel :
     
* enable/disable priority and/or frequency
* enable/disable the tags
* enable/disable the categories
* choose to store your sitemap XML file in the root of your website or in the plugin's directory

We also modified a few functions' name in order to prevent duplicate function names inside Wordpress

= 1.4.1 =
* Fixed all <? with <?php for servers only accepting <?php
* Added the possibility to enable/disable the "last modified" paramter"