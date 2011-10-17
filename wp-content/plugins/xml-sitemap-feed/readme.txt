=== XML Sitemap & Google News Sitemap Feeds ===
Contributors: RavanH
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=XML%20Sitemap%20Feed&item_number=3%2e8&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8&lc=us
Tags: xml sitemap, news sitemap, sitemap.xml, Google, Google News, Yahoo, Bing, Live, MSN, seo, wpmu, feed, qtranslate, xlanguage
Requires at least: 2.6
Tested up to: 3.0.3
Stable tag: 3.9.1

Feeds that comply with the XML Sitemap and Google News protocol for fast indexing by Google, Yahoo, Bing, Ask and others. Multi-Site and Multi-Lingual compatible!

== Description ==

This plugin dynamically creates feeds that comply with the **XML Sitemap** and the **Google News Sitemap** protocol. There are no options to be set nor file or dir access rights to be tampered with and the feeds become instantly available. One XML Sitemap on yourblogurl.tld/sitemap.xml (or yourblogurl.tld/?feed=sitemap), ready for indexing by search engines like Google, Yahoo, MSN, Ask.com and others. And one Google News Sitemap on yourblogurl.tld/sitemap-news.xml (or yourblogurl.tld/?feed=sitemap-news), ready for indexing by Google News. Please read the FAQ's for info on how to get your articles listed on Google News.

**Compatible with caching plugins** like Super Cache, W3 Total Cache and Quick Cache that cache feeds, allowing better performance to serve the hungry (impatient) spider.

**qTranslate and xLanguage compatible!** Tested in Pre-Path Mode and Query Mode. Each language on your site will have its own XML Sitemap.

References are automatically added to the dynamically created **robots.txt** on yourblogurl.tld/robots.txt to tell search engines where to find your XML Sitemaps. 

**NOTES:** 

1. If you _do not use fancy URL's_ or you have WordPress installed in a _subdirectory_, a dynamic **robots.txt will NOT be generated**. You'll have to create your own and upload it to your site root! See FAQ's.

2. On large sites, it is advised to use a good caching plugin like **Quick Cache**, **WP Super Cache** or **W3 Total Cache** to improve your site _and_ sitemap performance.

= Advantages = 

* The main advantage of this plugin over other XML Sitemap plugins is **simplicity**. No need to change file or folder permissions, move files or spend time on a difficult plugin options page. In fact, there are no options at all!
* Completely **automatic** post URL _priority_ and _change frequency_ calculation based on post age and comment and trackback activity.
* Works out-of-the-box, even on **multi-site / shared codebase / multi-blog setups** like WordPress MU, WP 3.0 in MultiSite (WPMS) mode and others. 
* Also works upon **Network Activate** or placed in **/mu-plugins/** on WP 3.0 in MS mode and WPMU and even takes care to exclude any tags blogs to avoid malus points for link spamming.
* Compatible with multi-lingual sites using **qTranslate** or **xLanguage** to allow all languages to be indexed equally.

= Limitations =

* The feed contains the front page and all posts and pages but _excludes_ category, tag and other dynamic archive pages. This should not be a problem and by most it is even _advised_ to exclude them. There are SEO plugins around that actively block these archive pages from search engines.
* Except by _re-saving_ older posts from time to time (keeping the lastmod date fairly recent) there is no way to manually control the priority of individual posts/pages in the sitemap. See the Faq's for more.
* This plugin does not ping any search engines. But then, WordPress does this by default already via the Ping-o-Matic service so why bother? See the Faq's for more.
* Because the feed is dynamically created, on _very_ large sites the creation process might take a while. Search engines are said to have a short fuse about waiting for a sitemap, so you may want to consider using a cache plugin that also (pre)caches feeds. If you are unfamiliar with caching and server setup start with an easy caching plugin such as **Quick Cache**. For more options (and better performance?) you might find solace in **WP Super Cache** or **W3 Total Cache**.

= Translations =

There is nothing to translate. The sitemap protocol is international, there is no options page nor any front-end or widget output. Nothing to see here, please move along ;)  

= Plugin developers =

Since 3.8.5, there is a FILTER hook `xml_sitemap_url` available that lets you filter the URL for the sitemap reference in the generated robots.txt and the home URL in the sitemap. It sends both string (for single url) and array (for multiple urls) and should receive the same. See pre-packaged examples of it's use in xml-sitemap.php for the qTranslate and xLanguage plugins.  

= Credits =

XML Sitemap Feed was originally based on the (discontinued?) plugin Standard XML Sitemap Generator by Patrick Chia. Many thanks! Since then, it has been completely rewritten and extended in many ways.

== Installation ==

= Wordpress =

Quick installation: [Install now](http://coveredwebservices.com/wp-plugin-install/?plugin=xml-sitemap-feed) !

 &hellip; OR &hellip;

Search for "xml sitemap feed" and install with that slick **Plugins > Add New** back-end page.

 &hellip; OR &hellip;

Follow these steps:

1. Download archive.

2. Upload the zip file via the Plugins > Add New > Upload page &hellip; OR &hellip; unpack and upload with your favourite FTP client to the /plugins/ folder.

3. Activate the plugin on the Plug-ins page.

4. If you have been using another XML Sitemap plugin before, check your site root and remove any created sitemap.xml file that remained there.

Done! Check your sparkling new XML Sitemap by visiting yourblogurl.tld/sitemap.xml (adapted to your domain name ofcourse) with a browser or any online XML Sitemap validator. You might also want to check if the sitemap is listed in your yourblogurl.tld/robots.txt file.

= WordPress 3+ in Multi Site mode =

Same as above but do a **Network Activate** to make a XML sitemap available for each site on your network.

= Wordpress MU =

The plugin works best from the **/mu-plugins/** folder where it runs quietly in the background without bothering any blog owner with new options or the need for special knowledge of XML Sitemap submission. Just upload the complete package content to /mu-plugins/ and move the file xml-sitemap.php from the new /mu-plugins/xml-sitemap-feed/ to /mu-plugins/.

Installed alongside [WordPress MU Sitewide Tags Pages](http://wordpress.org/extend/plugins/wordpress-mu-sitewide-tags/), XML Sitemap Feed will **not** create a sitemap.xml nor change robots.txt for any **tag blogs**. This is done deliberately because they would be full of links outside the tags blogs own domain and subsequently ignored (or worse: penalised) by Google.

== Frequently Asked Questions ==

= How do I get my latest articles listed on Google News? =

Go to [Suggest News Content for Google News](http://www.google.com/support/news_pub/bin/request.py?contact_type=suggest_content) and submit your website info as detailed as possible there. Give them the URL(s) of your fresh new Google News Sitemap in the text field 'Other' at the bottom.

You will also want to add the sitemap to your [Google Webmasters Tools account](https://www.google.com/webmasters/tools/) to check its validity and performance. Create an account if you don't have one yet.

= My Google News Sitemap is empty! =

The rules of the Google News game are that you do not feed the cookie monster any stale food. Older than 2 days is bad. You need to bake him some fresh bread ;)

= How are the values for priority and changefreq calculated? =

The front page has a fixed priority of 100% (1.0). When your site has more posts than pages (you must be using WordPress for a blog), pages have a default priority of 40% (0.4) and posts  have a default priority of 80% (0.8). If your site has more pages than posts (you must be using WordPress as CMS), pages have a default priority of 80% (0.8) and posts have a default priority of 40% (0.4).

Page and post priority can vary between 0% (0.0) and 100% (1.0). Page priority depends on the page level (decreasing 10% for each sub-level) and relative number of comments. Post priority depends on relative number of comments and relative last comment age or (when the post has no comments) last post modification age. 

The changefreq of the front page is fixed to daily and calculated for pages and post to either daily, weekly, monthly or yearly depending on age and comment activity.

Dynamic pages like category pages, tag pages and archive pages are not listed in the XML Sitemap.

= Can I manipulate values for priority and changefreq? =

Yes and No. This plugin has no options page so there is no way to manually set the priority of urls in the sitemap. But there is automatic post priority calculation based on _post modifaction date_ and _comment activity_, that can either make post priority go to 100% (1.0) for posts with many and recent comments or 0% (0) for the oldest posts with no comments. 

This feature can be used to your advantage: by re-saving your most important older posts from time to time, keeping the **lastmod date** fairly recent, you can ensure a priority of at least 80% (0.8) for those URLs. And if you have enough comments on on those pages, the priority can even go up to 100% (1.0).

If you cannot live with these rules, edit the values `$min_priority`, `$max_priority` and `$frontpage_priority` in xml-sitemap-feed/feed-sitemap.php but be careful to NOT do an automatic upgrade or it will overwrite your customisation.

= Do I need to submit the sitemap to search engines? =

No. In normal circumstances, your site will be indexed by the major search engines before you know it. The search engines will be looking for a robots.txt file and (with this plugin activated) find a pointer in it to the XML Sitemap on your blog. The search engines will return on a regular basis to see if your site has updates. 
( Read more about _Ping-O-Matic_ under **Does this plugin ping search engines** (below) to make sure your site is under _normal circumstances_ ;) )

**But** if you have a server _without rewrite rules_, use your blog _without fancy URLs_ (meaning, you have WordPress Permalinks set to the old Default value) or have it installed in a _subdirectory_, read **Do I need to change my robots.txt** for more instructions.

= Does this plugin ping search engines? =

No. While other XML Sitemap plugins provide pinging to some search engines upon each post edit or publication, this plugin does not. There are two reasons for that:

1. WordPress has a built-in pinging feature. Go in your WP Admin section to Settings > Writing and make sure that the text area under **Update services** contains at least `http://rpc.pingomatic.com`. Read more on [Ping-O-Matic](http://pingomatic.com) about what excellent service you are actually getting _for free with every WordPress blog_ installation!
1. For the average website, in my experience, pinging Google or others after each little change does not benefit anything except a theoretical smaller delay in re-indexation of your website. This is only theoretical because if your site is popular and active, major search engines will likely be crawling your site on a very regular basis anyway. And if, on the other hand, your site is not high on the agenda of the major search engines, they will likely give no priority to your pings at all.

You can always take a [Google Webmasters Tools account](https://www.google.com/webmasters/tools/) which will tell you many interesting things about your website, sitemap downloads, search terms and your visitors. Try it!

= Do I need to change my robots.txt? =

That depends. In normal circumstances, if you have no physical robots.txt file in your site root, the new sitemap url will be automatically added to the dynamic robots.txt that is generated by WordPress. But in some cases this might not be the case.

If you use a static robots.txt file in your website root, you will need to open it in a text editor. If there is already a line with `Sitemap: http://yourblogurl.tld/sitemap.xml` you can just leave it like it is. But if there is no sitemap referrence there, add it (adapted to your site url) to make search engines find your XML Sitemap. 

Or if you have WP installed in a subdirectory, on a server without rewrite_rules or if you do not use fancy URLs in your Permalink structure settings. In these cases, WordPress will need a little help in getting ready for XML Sitemap indexing. Read on in the **WordPress** section for more.

= My WordPress powered blog is installed in a subdirectory. Does that change anything? =

That depends on where the index.php and .htaccess of your installation reside. If they are in the root while the rest of the WP files are installed in a subdir, so the site is accessible from your domain root, you do not have to do anything. It should work out of the box. But if the index.php is together with your wp-config.php and all other WP files in a subdir, meaning your blog is only accessible via that subdir, you need to manage your own robots.txt file in your **domain root**. It _has_ to be in the root (!) and needs a line starting with `Sitemap:` followed by the full URL to the sitemap feed provided by XML Sitemap Feed plugin. Like:
`
Sitemap: http://yourblogurl.tld/subdir/sitemap.xml
` 

If you already have a robots.txt file with another Sitemap reference like it, just add the full line below or above it.

= Do I need to use a fancy Permalink structure? =

No. While I would advise you to use any one of the nicer Permalink structures for better indexing, you might not be able to (or don't want to) do that. If so, you can still use this plugin: 

Check to see if the URL yourblogurl.tld/?feed=sitemap does produce a feed. Now manually upload your own robots.txt file to your website root containing: 
`
Sitemap: http://yourblogurl.tld/?feed=sitemap

User-agent: *
Allow: /
`
You can also choose to notify major search engines of your new XML sitemap manually. Start with getting a [Google Webmasters Tools account](https://www.google.com/webmasters/tools/) and submit your sitemap for the first time from there to enable tracking of sitemap downloads by Google! or head over to [XML-Sitemaps.com](http://www.xml-sitemaps.com/validate-xml-sitemap.html) and enter your sites sitemap URL.

= Can I change the sitemap name/URL? =

No. If you have fancy URL's turned ON in WordPress (Permalinks), the sitemap url that you manually submit to Google (if you are impatient) should be `yourblogurl.tld/sitemap.xml` but if you have the Permalinks' Default option set the feed is only available via `yourblogurl.tld/?feed=sitemap`.

= Where can I customize the xml output? =

You may edit the XML output in `xml-sitemap-feed/feed-sitemap.php` but be careful not to break Sitemap protocol compliance.  Read more on [Sitemaps XML format](http://www.sitemaps.org/protocol.php).

The stylesheet (to make the sitemap human readable) can be edited in `xml-sitemap-feed/sitemap.xsl.php`.

= I see no sitemap.xml file in my site root! =

The sitemap is dynamically generated just like a feed. There is no actual file created.

= I see a sitemap.xml file in site root but it does not seem to get updated! =

You are most likely looking at a sitemap.xml file that has been created by another XML Sitemap plugin before you started using this plugin. Just remove it and let the plugin dynamically generate it just like a feed. There is no actual file created.

If that's not the case, you are probably using a caching plugin or your browser does not update to the latest feed output. Please verify.

= I use a caching plugin but the sitemap is not cached =

Some caching plugins have the option to switch on/off caching of feeds. Make sure it is turned on. 

Frederick Townes, developer of **W3 Total Cache**, says: "There's a checkbox option on the page cache settings tab to cache feeds. They will expire according to the expires field value on the browser cache setting for HTML."

= I get an ERROR when opening the sitemap or robots.txt! = 

The following errors might be encountered:

**404 page instead of my sitemap.xml**

Try to refresh the Permalink structure in WordPress. Go to Settings > Permalinks and re-save them. Then reload the XML Sitemap in your browser with a clean browser cache. ( Try Ctrl+R to bypass the browser cache -- this works on most but not all browsers. )

**404 page instead of both sitemap.xml and robots.txt**

There are plugins like Event Calendar (at least v.3.2.beta2) known to mess with rewrite rules, causing problems with WordPress internal feeds and robots.txt generation and thus conflict with the XML Sitemap Feed plugin. Deactivate all plugins and see if you get a basic robots.txt file showing: 
`
User-agent: *
Disallow:
`
Reactivate your plugins one by one to find out which one is causing the problem. Then report the bug to the plugin developer. 

**404 page instead of robots.txt while sitemap.xml works fine**

There is a know issue with WordPress (at least up to 2.8) not generating a robots.txt when there are _no posts_ with _published_ status. If you use WordPress as a CMS with only _pages_, this will affect you. 

To get around this, you might either at least write one post and give it _Private_ status or alternatively create your own robots.txt file containing:
`
Sitemap: http://yourblogurl.tld/sitemap.xml

User-agent: *
Allow: /
`
and upload it to your web root...

** Error loading stylesheet: An unknown error has occurred **

On some setups (usually using the WordPress MU Domain Mapping plugin) this error occurs. The problem is known, the cause is not... Until I find out why this is happening, please take comfort in knowing that this only affects reading the sitemap in normal browsers but will NOT affect any spidering/indexing on your site. The sitemap is still readable by all search engines! 

= Can I run this on a WPMU / WP3+ Multi-Site setup? =

Yes. In fact, it has been designed for it. Tested on WPMU 2.9.2 and WPMS 3.0.1 both with normal activation and with Network Activate / Site Wide Activate.

= Can I run this plugin from /mu-plugins/ on WP3.0 MS or WPMU ? =

Yes. Upload the complete /xml-sitemap-feed/ directory to /wp-content/mu-plugins/ and move the file xml-sitemap.php one dir up.

== Screenshots ==

1. XML Sitemap feed viewed in a normal browser. For human eyes only ;)
2. XML Sitemap source as read by search engines.

== Upgrade Notice ==

= 3.9.1 =
XSS vulnerability fix release.

== Changelog ==

= 3.9.1 =
* BUGFIX: XSS vulnerability in sitemap.xsl.php

= 3.9 =
* Google News Sitemap
* Memory limit error workaround (for most sites)

= 3.8.8 =
* Bugfix: PHP4 compatibility
* Bugfix: stylesheet URL when installed in mu-plugins
* core change to class
* minified sitemap output by default

= 3.8.5 =
* **xLanguage support** based on code and testing by **Daniele Pelagatti**
* new FILTER HOOK `robotstxt_sitemap_url` for any translate and url changing plugins.
* BUGFIX: Decimal separator cannot be a comma! 

= 3.8.3 =
* filter out external URLs inserted by plugins like Page Links To (thanks, Francois)
* minify sitemap and stylesheet output
* BUGFIX: qTranslate non-default language home URL

= 3.8 =
* **qTranslate support**
* no more Sitemap reference in robots.txt on non-public blogs

= 3.7.4 =
* switch from `add_feed` (on init) to the `do_feed_$feed` hook
* BUGFIX: `is_404()` condition TRUE and Response Header 404 on sites without posts
* BUGFIX: `is_feed()` condition FALSE after custom query_posts
* BUGFIX: no lastmod on home url when only pages on a site
* BUGFIX: stylesheet url wrong when WP installed in a subdir

= 3.7 =
* massive changefreq calculation improvement
* further priority calulation improvement taking last comment date into account

= 3.6.1 =
* BUGFIX: wrong date calculation on blogs less than 1 year old

= 3.6 =
* massive priority calculation improvement

= 3.5 =
* complete rewrite of plugin internals
* speed improvements
* WP 3.0 (normal and MS mode) ready

= 3.4 =
* bugfix: use home instead of siteurl for blog url for sitemap reference in robots.txt
* code streamline and cleanup

= 3.3 =
* automatic exclusion of tags blog in wpmu

= 3.2 =
* rewrite and add_feed calls improvements
* bugfix: double entry when static page is frontpage

= 3.0 =
* added styling to the xml feed to make it human readable

= 2.1 =
* bugfix: lastmod timezone offset displayed wrong (extra space and missing double-colon)

= 2.0 =
* priority calculation based on comments and age
* changefreq based on comments 

= 1.0 =
* changed feed template location to avoid the need to relocate files outside the plugins folder
* bugfix: `get_post_modified_time` instead of `get_post_time`

= 0.1 =
* rework from Patrick Chia's [Standard XML Sitemaps](http://wordpress.org/extend/plugins/standard-xml-sitemap/)
* increased post urls limit from 100 to 1000 (of max. 50,000 allowed by the Sitemap protocol)
