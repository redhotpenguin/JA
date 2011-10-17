=== WP Highrise Contact ===
Tags: contact, highrise
Contributors: marcan
Requires at least: 2.2.3
Tested up to: 2.9.2
Stable tag: 1.1.2

== Description ==
This plugin generates a form which you can drop on any page or post. When a visitor fills the form, the data will be sent to you via email and it will also be sent to your 37signals Highrise account where the following will be created:

* A contact populated with the form's data (name, email, company, title, phone, etc...)
* A note will be created, associated with the previously create contact
* A task associated to this note

This plugin offers different options like notification email address, due date for the task to be created, category of the task, etc...

This plugin's code was inspired by WP Contact Form developed by westi, ryanduff, firas. Thanks!

<h4>Support</h4>

We are happy to provide free community support for our plugin on the <a href="http://wordpress.org/tags/wp-highrise-contact?forum_id=10">dedicated plugin forum</a>. Should you require any professional support, you can contact us on the <a href="http://inboxinternational.com/support/">INBOX Support</a> site.

<h4>Feedback</h4>

We would love to get your feedback on the plugin. What's great, what's not, what improvment would you suggest. Please use the <a href="http://wordpress.org/tags/wp-highrise-contact?forum_id=10">dedicated plugin forum</a> to share any comment you may have.

== Installation ==

1. Upload to your plugins folder, usually `wp-content/plugins/`
2. Activate the plugin on the plugin screen.
3. Configure the plugin by clicking on Dashboard > Settings > WP Highrise Contact

== Frequently Asked Questions ==

= How do I add the contact form to a post/page? =

Simply use the following shortcode in any post or page [wp-highrise-contact].

= How can I edit the form itself ? =

The form has been built so you can easily customize it in your theme without having to hack the plugin. Simply copy plugins/wp-highrise-contact/form.inc.php into your theme/plugins/wp-highrise-contact/. Then edit it at will! If a file named form.inc.php is located at this very location in your theme, the plugin will use this file instead of the default one to display your form.

= The task does not seem to be created, even though the option is enabled in the plugin settings =

The task created on Highrise will be created under the name of the Highrise user account corresponding to the API token you have entered in the plugin settings. If another of your staff user log on Highrise, she will not see this task. By default, all tasks created on Highrise are only seen by their owner.

== Screenshots ==

1. An example of the contact form
2. Options of the plugin

== Changelog ==

= 1.1.2 =

* Adding a check in the plugin settings to ensure that the server is running PHP Version > 5 and that cURL is enabled

= 1.1.1 =

* Updated ReadMe
* Bug on the form on IE8

= 1.1 =

* Full multilingual support
* French translation
* Implementing an option for SSL-128 Encryption Highrise Account

= 1.0 =

* First release
