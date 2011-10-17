=== Plugin Name ===
Contributors: deva1983
Donate link: http://qwertyuiopia.com/2008/04/23/qwerty-admin-panel-theme-plugin-for-wordpress
Tags: customization, admin panel, colors, options, css, permissions, limited interface
Requires at least: 2.7
Tested up to: 2.7
Stable tag: 0.3

This plugin overrides the admin panel style sheet for all users, allowing you to configure its colors through an options page. Also, utilizing the wordpress roles and capabilities, it can hide certain menus from end users.

== Description ==

The Qwerty Admin Panel Theme is a plugin for Wordpress 2.7 that allows you to style the admin panel and login screen for all users, allowing you to configure groups of colors through an options page, and styles through a stylesheet. Also, it allows you to swap the wordpress logos with your own logo images.

It can be useful in helping to maintain your design, color schemes and branding on multi-author blogs and sites with user-contributed content, adding a nice touch to sites developed for clients, or simply personalizing your blog's administration, look and feel.

== Installation ==

1. Upload the `qwerty-admin-theme-plugin` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. (optional) If you wish to use the Limited Interface feature, you will need to give your users the "Limited interface" capability. You will need a plugin to manage roles to do that. Read more about that in the Usage/Configuration section

== Usage / Configuration ==

While the Qwerty Admin Panel Theme Plugin has some defaults that you might like and keep, it's basic purpose is to make the customization process easy.

= Colors =

The options page is under Appearance --> Qwerty Admin Panel Theme. From there you can configure groups of colors for display, as well as which of the admin panel menus are to be hidden.

= Images =

In the qwerty-admin/images/ folder there are three images, which appear in the login screen (qwerty-logo-login.png), the lower left corner of the admin panel (qwerty-logo-ghost.png) and the upper right corner, on the panel's header (qwery-logo-head.png).

In order to show your own images, place them in the plugin's images folder with the names logo-login.png, logo-ghost.png and logo-head.png. If any of the images doesn't exist, the plugin will use the default qwerty- one. Don't substitute the qwerty- ones, as they may be overwritten when the plugin is updated.

= CSS =

If you need to enter any style information of your own, you can do so in a file named qwerty-admin-imports.css, which doesn't come with the theme but will be loaded if you create it. This way, you can modify css information without worrying of losing your customizations on auto-updates.

Currently the plugin uses the /wp-admin/css/colors-fresh.css file to get its base information from, and the only information it fiddles with is colors and images. Of course, you can change dimensions to suit your needs by entering your styles in the imports file. 

= Limited interface =

The limited interface options work with wordpress capabilities, checking if the user has the "Limited interface" capability and hiding certain parts of the admin interface in that case.

In order for it to work you have to give the "Limited interface" capability to a group of users. You need a role manager plugin to do so.

The options panel provides toggles to quickly hide the Dashboard, Media and Comments menus. You can place any other css styles you wish to apply for that role in a file named qwerty-admin-limited.css in your plugin directory and it will be automatically included.

Bear in mind that the plugin simply hides certain elements by setting their display property to none. Users that know a url hidden in this way may still visit it, so it shouldn't be used for security purposes but only to simplify the design. 

== Frequently Asked Questions ==

= Has anybody ever asked a question about this plugin? =

No, nobody yet.

== Screenshots ==

1. The login screen
2. The styled interface
3. The Limited interface

