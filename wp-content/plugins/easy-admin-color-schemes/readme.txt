=== Easy Admin Color Schemes ===
Contributors: jick
Donate link: http://www.jamesdimick.com/donate/
Tags: admin, administration, color, colors, control, custom, css, design, designs, edit, interface, panel, plugin, preview, profile, scheme, schemes, style, styles, theme, themes, wordpress
Requires at least: 2.7
Tested up to: 2.8.*
Stable tag: 3.2

The Easy Admin Color Schemes plugin allows users to easily customize the colors of the administration interface for WordPress.

== Description ==

The Easy Admin Color Schemes plugin allows users to easily customize the colors of the administration interface for WordPress. It works by adding a new page to the Settings area in the WordPress admin interface. Users can use the simple form to change the look of the admin interface without needing to know a great deal about WordPress. The plugin generates a new stylesheet file for each color scheme created. This allows for seamless integration with the color scheme functions already available in the WordPress 2.5+ core. In version 2.0 and later of Easy Admin Color Schemes, you can also import/export color schemes.

If you would like to use this plugin in versions of WordPress prior to 2.7 please download version 2.7 of this plugin. Version 3.0+ of the plugin is for WordPress 2.7 and up **only**.

**Get more support at [the official Easy Admin Color Schemes page](http://www.jamesdimick.com/creations/easy-admin-color-schemes/).**

== Installation ==

1. Extract all files from the ZIP archive, making sure to keep the file structure intact.
2. Upload the `easy-admin-color-schemes` folder to the `/wp-content/plugins/` directory.
3. Make sure the `schemes` folder, which is inside the `easy-admin-color-schemes` folder, is writable.
4. Activate the plugin through the `Plugins` menu in WordPress.
5. Go to the `Color Schemes` menu which is located under the `Settings` menu in the admin interface.
6. Have fun creating new color schemes!

**See Also:** ["Installing Plugins" article on the WP Codex](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins)

== Frequently Asked Questions ==

= This plugin rocks! Can I donate to you? =

Well, I'm glad you like it. I do appreciate all donations. You can donate to me by going to [my donate page](http://www.jamesdimick.com/donate/).

= I get "____" error =

Please report any errors you get to me. You can either [post here](http://www.jamesdimick.com/creations/easy-admin-color-schemes/) or [contact me directly](http://www.jamesdimick.com/contact/).

= What if I have questions that are not covered here? =

The best way to get in contact with me is [though my website](http://www.jamesdimick.com/contact/). Please contact me any time if you have questions, comments, etc. Also, you can leave a comment on [the official Easy Admin Color Schemes page](http://www.jamesdimick.com/creations/easy-admin-color-schemes/).

== Screenshots ==

1. The Easy Admin Color Schemes interface

== Changelog ==

= 3.2 =
* Fixed (hopefully) the image display problems in the lower preview pane when adding/editing a color scheme
* Fixed a bug in the last modified times function
* Replaced some of the old JavaScript with jQuery code of similar functionality
* Added a new Preview function which opens a preview of the selected color scheme in a lightbox
* Added some more in-depth permissions functionality
* Added the ability for the primary colors to actually affect the color scheme
* Added the ability to set a default color scheme which will affect all new users
* Added the ability to force a certain color scheme on all users regardless of what they choose
* Updated the included readme, screenshot, and .POT file to include the new changes

= 3.1 =
* Fixed an issue with the last modified times
* Added Russian translation thanks to fatcow (http://www.fatcow.com/)

= 3.0 =
* Updated the plugin interface to fit in better with the new WordPress 2.7 look and be more intuitive
* Added collapsible sections to help with overall plugin ease of use
* Added a toggle button for the live preview section
* Removed the Washedout color scheme because it is obsolete with the new default gray color scheme of 2.7
* Removed the For the Love of 2.3 scheme. It is too difficult to maintain with the constant interface changes in WordPress
* Added two new color schemes called Red and Green which are variations of the default scheme of 2.7
* Now allowing more special characters in the color scheme names and also scheme names up to 200 characters
* Also now allowing editing of color scheme names
* Added a Copy action which will copy the selected color scheme into the Create a Color Scheme form
* Can now export the default WordPress color schemes as well
* Now using a new and improved color picker
* Completely removed previously commented-out code to save on the overall file size
* Fixed a few small bugs here and there
* Updated the included readme, screenshot, and .POT file to include the new changes

= 2.7 =
* Fixed (really this time) the issue with exporting color schemes with Method 2 of the export functionality

= 2.6 =
* Fixed an issue with exporting color schemes with Method 2 of the export functionality
* Fixed a few minor issues with the localization parts of the plugin
* Also added a small bit to the Right Now section on the dashboard

= 2.5 =
* Fixed some issues caused by WordPress 2.6 including an issue with the For the Love of 2.3 scheme
* Also updated the Washedout color scheme a bit

= 2.4 =
* Fixed (hopefully) the issue with the last modified dates

= 2.3 =
* Fixed an issue with the new For the Love of 2.3 color scheme
* Moved the update preview button to just above the preview window
* Added a note just below the primary colors area in an attempt to relieve some confusion many have been having
* Updated the included screenshot to include the new changes

= 2.2 =
* Removed the link on the user profile page until it can be done more reliably
* Also added a new scheme called For the Love of 2.3 which attempts to bring back some of the old WordPress 2.3 styles

= 2.1 =
* Fixed the major issue some people were having with setting their current scheme from the plugin page
* As a positive side-effect, setting the scheme from the built-in scheme picker on the profile page now works correctly

= 2.0 =
* Added a new export feature which allows users to export color schemes in a couple different formats
* Also added an upload feature so color schemes that have been exported can be imported back in
* Fixed some issues with setting the current scheme from the plugin page
* Fixed a few spelling errors in various parts of the plugin
* Fixed some problems with the JavaScript
* Fixed a few issues with localization
* Improved the error reporting functions

= 1.8 =
* Corrected some things to allow for better localization

= 1.7 =
* Fixed the way the new link on the profile page works so only users with proper permissions can see it

= 1.6 =
* Added a link to the user profile page right by the Admin Color Scheme selector which links to the plugin

= 1.5 =
* Added error codes to aid in debugging and fixed some image issues with the new Washedout color scheme

= 1.4 =
* Added a better-looking default color scheme called Washedout

= 1.3 =
* Fixed an issue with slashes in the CSS content when you save a scheme

= 1.2 =
* Changed the way Last Modified dates are handled so the plugin still works instead of erroring out

= 1.1 =
* Changed some URL query variable names in an attempt to prevent clashing with other plugins

= 1.0 =
* The first version

== Known Issues ==

* None right now...
