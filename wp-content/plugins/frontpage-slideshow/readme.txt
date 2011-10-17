=== Plugin Name ===
Contributors: Jean-François “Jeff” VIAL
Donate link: http://www.modulaweb.fr/blog/wp-plugins-en/
Tags: frontpage, slideshow, pictures, no-flash, css, javascript, XHTML,
Requires at least: 2.7.0
Tested up to: 3.1
Stable tag: 0.9.9.3.4

Frontpage Slideshow provides a slide show like you can see in linux.com front page

== Description ==

This plugin allows you to put a slideshow on your Wordpress front-page like the one on linux.com website, or a «preview/next» slideshow.

= Features =

* No limits on how much slides to show
* Images are pre-loaded
* Automatic start when images are preloaded
* All aspects can be customized : sizes, colors, background-images, transitions, durations, parts to show, preload animation
* Navigation with per slide or previous/next buttons or no navigation at all
* SEO friendly
* Retrieve automaticaly all informations to show depending on global parameters but allow a per slide configuration
* Each slide is made from an article taken from one or more categories
* Admin box added on New/Edit articles formulars
* Complete admin page for whole slider parameters with preview
* Automatic insertion on frontpage or shortcode every where you want (on post bodies and sidebar text box or with small simple PHP snipet)
* Very high customization allowed by template system that allow to to control the HTML part but also the javascript and CSS parts !

= Usages = 

* As an articles slideshow to present hot news
* As a picture slideshow
* As an animated header background


== Installation ==

1. Upload `frontpage-slideshow.zip` to the `/wp-content/plugins/` directory and uncompress it.
1. Go to the plugin's administration page and configure it as you want it
1. Activate the plugin when you are ready to

When installed : make sure you have read the <a href="../other_notes/">"how to use" section</a>

== Frequently Asked Questions ==

= How to use this plugin ? =

See the <a href="../other_notes/">"how to use" section</a>

= Where to get support ? =

See the <a href="http://wordpress.org/tags/frontpage-slideshow">frontpage slideshow forum</a>, look for some topics tha could match your problem or question, then if no topic helps you, create a new topic.
 
= May I use more than one slideshow on my site ? =

Of course ! you just have to enable the shortcode way to display the slideshow then use as many shortcodes you want. See the <a href="../other_notes/">"how to use" section</a> for more infos.

= The plugin is messing up my Wordpress admin page ! What can I do ? =

The plugin just cant do that ! De-activate all plugins then re-activate them one by one and shoot the guilty !

= Another plugin or my theme is coded with feet and messes up with your plugin ! Could you help me ? =

I could give you some free advises but if you really want me to take precious time to debug your plugins or theme... consider to donate (I takes 45€/h)

= Hey ! Your plugin is messing up my beautiful website ! Can you fix it now ? =

This plugin uses the Wordpress API to include the javascript frameworks properly, so that this plugin does not cause problems if other plugins or themes you use are properly encoded. 

Otherwise, you got to troubleshoot by yourself (de-activate all plugins then re-active them one by one to determine if its the theme or a plugin, and wich plugin is messing up) then contact the guilty's author and ask him to use the Wordpress API to include javascript frameworks.

If you added those javascript files by yourself, consider having a look at the <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">wp_enqueue_script</a> function.

I could give you some free advises but if you really want me to take precious time to debug your plugins or theme... consider to donate (I takes 45€/h)

= I got a super idea ! I want you to implement it in your plugin ! Can you do it ? =

Yep ! Send me some interesting code and I will be pleased to add it to the plugin ! (and you could be co-author of the plugin!) but if you dont send code or if your demand is too far from my goals : I will not implement your suggestions.

If you really want me to implement some partcular features... consider to donate or hire me (You'll get invoice with VAT etc... I takes 45€/h)


== Changelog ==

* v 0.1 : very first release usable but no option page
* v 0.2 : some terrible graphic bugs fixed : option page under construction and preview
* v 0.3 : some minor javascript and CSS bugs fixed : now the plugin is ready for the option-page and fine tunes.
* v 0.4 : fully functional administration page with preview, reset to default
* v 0.5 : important bug fix when the plugin is loaded before jQuery and some CSS improvement
* v 0.6 : improving the loading of javascript frameworks needed, the shortcode functionality added, alternative picture option added, when no link is set, the URL of the post can be used
* v 0.6.1 : minor bug correction (replacing php short tags by long ones)
* v 0.7 : allow to use the WP Text Widget to display the slideshow by inserting the shortcode onto the text itself, modify the original WP Text Widget to allow the use of all other shortcodes
* v 0.7.1 : changes made on the admin page ; adding the plugins unique ID system to allow faster troubleshoot ; possibility to view the complete shortcode string
* v 0.7.2 : uses javascript to include specific css rather than plain html allowing to validate to XHTML
* v 0.7.3 : fixing bug introduced in 0.7.2 that was messing up the slideshow display in IE + XHTML validation improvements
* v 0.7.4 : fixing bugs introduced in 0.7.3 that causes no start of the slideshow sometimes + possibility to change slide transitions + order of slides + putting a different title on slides + option to hide the comment zone
* v 0.8 : Adding an admin zone to easily add or modify options for posts into the edit post page. + adding the complete shortcode under the preview + modifying the "How to use / Getting help section" + if no picture is available, display a default picture (a 1x1 transparent pixel GIF)
* v 0.9 : Port from PrototypeJS+ScriptAculoUs to jQuery ; Add ability to configure background-images of all elements and loader animation picture by simple drag-drop ; fixing default link bug ; fixing quick buttons bar show when image is has 100% width bug ; adding some way to load the image chooser on demand to allow people that have alot of images to not stand by all images to be loaded.
* v 0.9.1 : some improuvement and correcting svn files (js files were missing).
* v 0.9.2 : Improuve the way to find the URL of very first image of a post.
* v 0.9.3 : Uses the CDN copy of jQuery instead of the one included into Wordpress ans minor graphical bugs corrections in admin page and modify post page.
* v 0.9.4 : Correcting minor bug that was messing the «Slide comment» input field when html was present in it
* v 0.9.5 : Correcting jQuery libraries compatibility issues that was messing with normal behavior of WP admin area
* v 0.9.6 : Fixes a wrong behavior that dont load plugin specific JS and CSS when shortcode is used anywhere else than header.php template file.
* v 0.9.6.1 : Fixes some weird behaviors
* v 0.9.6.2 : Turn back to a fully usable state, adding beginning transitions effects changing
* v 0.9.7 : Fixes an issue that making the slider to slide too many times when clicking on the buttons many times. Add the auto-calculation of buttons height.
* v 0.9.8 : Passed all inline javascript to javascript blocs ; CSS styles are now inserted via Javascript, only on pages where the slideshow must appear ; added rounded corners option ; fixed visual bug onto the admin page ; fixed a functional bug into options datas validation process
* v 0.9.9 : Introduce the templating system : all functions that format the slider (html/css/js) are in template script, allowing people to create their very own templates.
* v 0.9.9.1 : Improving i18n and add french translation
* v 0.9.9.2 : Add the previous and next buttons ; Add the configuration of durations ; correct the color values validation
* v 0.9.9.3 : Add the "use the post excerpt as a slide comment" option
* v 0.9.9.3.1 : Minor changes on how javascript controls the slider and «use excerpt if no compment specified» option bad storage bug fix
* v 0.9.9.3.2 : «use excerpt if no compment specified» option bad storage bug fix
* v 0.9.9.3.3 : fix a bug that avoided the use of excerpt as slide comment and a bug introduced in 0.9.9.3.1 that disabled buttons use
* v 0.9.9.3.4 : fix a bug that not allowed to specify 1 or multiple categories by using shortcode ; silent the warning when the internet cannot be reached to get the plugin ID and add a message instead (+translation to french of this message)

== Screenshots ==

1. live at http://wwww.modulaweb.fr French web agency
2. live at http://www.smartyweb.org

== How to use ==

There are 2 ways to use this plugin:

1. If you are using a static page as the front-page, use the front-page mode
      With this mode, the slideshow will be automatically added in top of the front-page content, before all other content. You don't have anything else to do.
1. If you are not using a static page as the front-page, use the shortcode mode
      With this mode, you got to put a shortcode (like [FrontpageSlideshow]) where you want the slideshow to be displayed :
	2. Somewhere into your posts content
	2. Somewhere into some sidebar text-box
	2. Everywhere else into the pages by inserting the following code snippet into your theme's .php files where you want the slideshow to be displayed:

`<?php
// added by <yourname> in order to add the slideshow using the frontpage-slideshow plugin 
echo do_shortcode('[FrontpageSlideshow]');
?>`

Note that this plugin is using the Wordpress API In order to include its needed Javascript files. Some other plugins or themes that are not using that API could mess up with this plugin.

= Creating different slideshows with different parameters: =

You can use different slideshows with different parameters easily ! Simply use the shortcode way to insert slideshows, save this options, then configure the slider, make a preview, copy the shortcode relulting of those parameters, and insert this shortcode everywhere you want a slideshow to be displayed ! You can create as many different slideshow as you got posts and pages into your blog. Remember that only the fist slideshow displayed on a page will work.

= In case of trouble: =

* Make sure you have read the "How to use": http://wordpress.org/extend/plugins/frontpage-slideshow/other_notes/
* Read this page: http://wordpress.org/support/topic/322689
* Look at the other support questions there: http://wordpress.org/tags/frontpage-slideshow
* If you want to post a support question, create a new topic by using this link: http://wordpress.org/tags/frontpage-slideshow#postform

= Plugin unique ID =

In order to faster bug reports, troubleshoot and for some statistics, some informations are collected and sent to this plugin:'s author.
The informations that are sent are this site URL, this site admin email address, the Wordpress version, the used theme and its URI, and the used version of this plugin.
If you need help to troubleshoot, don't forget to transmit your plugin unique ID.
You can find this plugin unique ID by visiting the plugin admin page at the very end of the "How to use / Getting help" section
