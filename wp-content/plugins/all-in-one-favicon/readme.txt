=== All In One Favicon ===
Contributors: techotronic
Donate link: http://www.techotronic.de/donate/
Tags: theme, favicon, admin, blog, wordpress, image, images, graphic, graphics, icon, iphone, multisite
Requires at least: 2.8
Tested up to: 3.0
Stable tag: 3.1

Easily add a Favicon to your site and the WordPress admin pages. Complete with upload functionality. Supports all three Favicon types (ico,png,gif).

== Description ==

All In One Favicon adds favicons to your site and your admin pages.
You can either use favicons you already uploaded or use the builtin upload mechanism to upload a favicon to your WordPress installation.

All three favicon types are supported - .ico, .png and .gif (may be animated)
Also, Apple Touch Icons are supported.

See <a href="http://www.techotronic.de/plugins/all-in-one-favicon/">plugin page</a> for more information, a "first steps" guide and screenshots.

Localization

* Bahasa Indonesia (id_ID) by <a href="http://movableid.com/">EKO</a>
* Danish (da_DK) by <a href="http://wordpress.blogos.dk/">GeorgWP</a>
* English (en_EN) by <a href="http://www.techotronic.de/">Arne Franken</a>
* German (de_DE) by <a href="http://www.techotronic.de/">Arne Franken</a>
* Italian (it_IT) by <a href="http://www.valeriovendrame.it/">Valerio Vendrame</a>
* Polish (pl_PL) by <a href="http://www.facebook.com/piniu69/">Piotr Czarnecki</a>
* Spanish (es_ES) by <a href="mailto:jppm30@gmail.com">Juan Pablo Pérez Manes</a>
* Swedish (sv_SE) by <a href="http://www.theindiaexperience.se/">Christian Nilsson</a>

Is your native language missing? Translating the plugin is easy if you understand english and are fluent in another language. Just send me an email.

== Installation ==

###Updgrading From A Previous Version###

To upgrade from a previous version of this plugin, use the built in update feature of WordPress or copy the files on top of the current installation.

###Installing The Plugin###

Either use the built in plugin installation feature of WordPress, or extract all files from the ZIP file, making sure to keep the file structure intact, and then upload it to `/wp-content/plugins/`. Then just visit your admin area and activate the plugin. That's it!

###Configuring The Plugin###

Go to the settings page and and upload your Favicon(s) or add the path/URL to already existing Favicon(s).

**See Also:** <a href="http://codex.wordpress.org/Managing_Plugins#Installing_Plugins">"Installing Plugins" article on the WP Codex</a>

== Frequently Asked Questions ==

* When I try to upload a favicon, I get the error: "File type does not meet security guidelines. Try another.”

You are probably using a WordPress Multisite installation? Then you'll need to add "ico" to the allowed file types property on the "Super Admin -> Options" page.

* Why is All In One Favicon not available in my language?

I speak German and English fluently, but unfortunately no other language well enough to do a translation.

Would you like to help? Translating the plugin is easy if you understand English and are fluent in another language.

* How do I translate All In One Favicon?

Take a look at the WordPress site and identify your langyage code:
http://codex.wordpress.org/WordPress_in_Your_Language


I.e. the language code for German is "de_DE".


Step 1) download POEdit (http://www.poedit.net/)


Step 2) download All In One Favicon (from your FTP or from http://wordpress.org/extend/plugins/all-in-one-favicon/)


Step 3) copy the file localization/aio-favicon-en_EN.po and rename it. (in this case aio-favicon-de_DE.po)


Step 4) open the file with POEdit.


Step 5) translate all strings. Things like "{total}" or "%1$s" mean that a value will be inserted later.


Step 5a) The string that says "English translation by Arne ...", this is where you put your name, website (or email) and your language in. ;-)


Step 5b) (optional) Go to POEdit -> Catalog -> Settings and enter your name, email, language code etc


Step 6) Save the file. Now you will see two files, aio-favicon-de_DE.po and aio-favicon-de_DE.mo.


Step 7) Upload your files to your FTP server into the All In One Favicon directory (usually /wp-content/plugins/all-in-one-favicon/)


Step 8) When you are sure that all translations are working correctly, send the po-file to me and I will put it into the next All In One Favicon version.

* My question isn't answered here. What do I do now?

Feel free to write an email to blog [at] techotronic.de or open a thread at <a href="http://wordpress.org/tags/all-in-one-favicon?forum_id=10#postform">the All In One Favicon WordPress.org forum</a>.

I'll include new FAQs in every new version. Promise.

== Screenshots ==

<a href="http://www.techotronic.de/plugins/all-in-one-favicon/">Please visit my site for screenshots</a>.

== Changelog ==
= 3.1 (2011-01-16) =
* CHANGE: made plugin compatible to PHP4

= 3.0 (2011-01-15) =
* NEW: Added option to remove link from meta box.
* BUGFIX: Fixed a bug where the plugin would break WordPress 3.0 with Multisite enabled.
* NEW: Added latest donations and top donations to settings page
* NEW: Danish translation by <a href="http://wordpress.blogos.dk/">GeorgWP</a>
* NEW: Bahasa Indonesia translation by <a href="http://movableid.com/">EKO</a>
* NEW: Polish translation by <a href="http://www.facebook.com/piniu69/">Piotr Czarnecki</a>
* NEW: Swedish translation by <a href="http://www.theindiaexperience.se/">Christian Nilsson</a>
* NEW: Italian translation by <a href="http://www.valeriovendrame.it/">Valerio Vendrame</a>
* NEW: Spanish translation by <a href="mailto:jppm30@gmail.com">Juan Pablo Pérez Manes</a>

= 2.1 (2010-06-06) =
* BUGFIX: Fixing bug where favicons would not be displayed in certain cases.

= 2.0 (2010-06-03) =
* NEW: now supports Apple Touch Icons for backend and frontend
* NEW: more links to websites containing information.

= 1.0 (2010-05-06) =
* NEW: Initial release.