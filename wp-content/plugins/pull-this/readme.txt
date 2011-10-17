=== Pull This ===
Contributors: smajda
Author URI: http://jon.smajda.com
Plugin URI: http://wordpress.org/extend/plugins/pull-this/
Tags: pull-quote, pull-this, post, quotes
Requires at least: 2.8
Tested up to: 3.0.1
Stable tag: 1.1

Select and place pull quotes inside your posts using shortcodes ([pullthis][/pullthis] and [pullshow]). Pull quotes are inserted with javascript.

== Description == 

Pull quotes are nice for long, text-heavy posts. They help you communicate your key points to skimmers and they visually help to break up long chunks of text. However, in situations where your post content is displayed with CSS beyond your control (such as in a feed reader), pull quotes that are hard-coded into your post content can be ugly and confusing.

To solve this problem, this plugin creates shortcodes you can use to indicate snippets of text that you would like to use as a pull quote and indicate where you want it to show up in the post as well. When viewing the post on your website in a javascript-capable browser, the pullquotes will be inserted with javascript in the specified place. However, when viewed in a feed reader, for example, the pullquotes will not appear.

Just indicate text you want to appear in a pullquote and where you want it to appear (using WP shortcodes: `[pullthis]` and `[pullshow]`) and the plugin will insert the pullquotes using javascript on each page load.

= Example #1: A Single Pull Quote =

Wrap your pullquote in `[pullthis]` and `[/pullthis]` and use `[pullshow]` where you want the pull quote to show up:

    [pullshow]Lorem ipsum dolor sit amet, consectetuer adipiscing elit. [pullthis]This 
    is my first pull quote.[/pullthis] Aliquam sit amet felis. Etiam congue. Donec 
    risus risus, pretium ac, tincidunt eu, tempor eu, quam.


= Example #2: Multiple Pull Quotes =

You can have multiple pull quotes by using the `id` attribute. It can have whatever value you want.

    [pullshow id="aliquam"]Lorem ipsum dolor sit amet, consectetuer adipiscing
    elit. [pullthis id="aliquam"]Aliquam sit amet felis.[/pullthis] Etiam congue. Donec
    risus risus, pretium ac, tincidunt eu, tempor eu, quam. 

    [pullshow id="donec"]Morbi blandit mollis magna. Suspendisse eu tortor. [pullthis
    id="donec"]Donec vitae felis nec ligula blandit rhoncus.[/pullthis] Ut a pede ac
    neque mattis facilisis. Nulla nunc ipsum, sodales vitae, hendrerit non,
    imperdiet ac, ante. Morbi sit amet mi. Ut magna. 

= Example #3: Only Show The Pull Quote =

What if you want something to *only* appear in a pull quote? You can add the `display` attribute to `pullthis` and set it to `outside`:

    [pullshow id="ex3"]Lorem ipsum dolor sit amet, consectetuer adipiscing
    elit. Vivamus vitae risus vitae lorem iaculis placerat. Aliquam sit amet
    felis. Etiam congue.  Donec risus risus, pretium ac, tincidunt eu, tempor
    eu, quam. Morbi blandit mollis magna.  [pullthis id="ex3"
    display="outside"]This sentence will be hidden inside the paragraph and
    only show as a pull quote.[/pullthis] 

In this case, the orignal `pullthis` text is still there in the HTML, but it's hidden with CSS (by setting `display: none` on the text).

== Installation ==

Just upload the `pull-this` directory to `/wp-content/plugins/` and activate. No settings or anything. 


== Frequently Asked Questions ==

= Are there any frequently asked questions? = 

Not yet!

== Screenshots ==

1. A screenshot of what example #1 under the Description heading produces. 

== Changelog ==

= 1.1 =
* Fixed bug that caused pull-this quotes to not be applied properly in index views. [Thanks voodoodoodoo](http://wordpress.org/support/topic/plugin-pull-this-note-about-display-in-multiple-entries).

= 1.0 =

* Initial release!
