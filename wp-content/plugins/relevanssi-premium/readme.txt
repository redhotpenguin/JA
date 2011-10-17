=== Relevanssi Premium - A Better Search ===
Contributors: msaari
Donate link: http://www.relevanssi.com/
Tags: search, relevance, better search
Requires at least: 2.7
Tested up to: 3.1
Stable tag: 1.3.1

Relevanssi Premium replaces the default search with a partial-match search that sorts results by relevance. It also indexes comments and shortcode content.

== Description ==

Relevanssi replaces the standard WordPress search with a better search engine, with lots of features
and configurable options. You'll get better results, better presentation of results - your users
will thank you.

= Key features =
* Search results sorted in the order of relevance, not by date.
* Fuzzy matching: match partial words, if complete words don't match.
* Find documents matching either just one search term (OR query) or require all words to appear (AND query).
* Search for phrases with quotes, for example "search phrase".
* Create custom excerpts that show where the hit was made, with the search terms highlighted.
* Highlight search terms in the documents when user clicks through search results.
* Search comments, tags, categories and custom fields.

= Advanced features =
* Adjust the weighting for titles, tags and comments.
* Log queries, show most popular queries and recent queries with no hits.
* Restrict searches to categories and tags using a hidden variable or plugin settings.
* Index custom post types and custom taxonomies.
* Index the contents of shortcodes.
* Google-style "Did you mean?" suggestions based on successful user searches.
* Automatic support for [WPML multi-language plugin](http://wpml.org/)
* Advanced filtering to help hacking the search results the way you want.

= Premium features (only in Relevanssi Premium) =
* Search result throttling to improve performance on large databases.
* Improved spelling correction in "Did you mean?" suggestions.
* WordPress Multisite support.
* Indexing and searching user profiles.
* Weights for post types, including custom post types.
* Limit searches with custom fields.
* Index internal links for the target document (sort of what Google does).
* Search using multiple taxonomies at the same time.

Relevanssi is available in two versions, regular and Premium. Regular Relevanssi is and will remain
free to download and use. Relevanssi Premium comes with a cost, but will get all the new features.
Standard Relevanssi will be updated to fix bugs, but new features will mostly appear in Premium.
Also, support for standard Relevanssi depends very much on my mood and available time. Premium
pricing includes support.

= Relevanssi in Facebook =
You can find [Relevanssi in Facebook](http://www.facebook.com/relevanssi).
Become a fan to follow the development of the plugin, I'll post updates on bugs, new features and
new versions to the Facebook page.

Relevanssi owes a lot to [wpSearch](http://wordpress.org/extend/plugins/wpsearch/) by Kenny
Katzgrau.

== Installation ==

1. Extract all files from the ZIP file, and then upload the plugin's folder to /wp-content/plugins/.
1. If your blog is in English, skip to the next step. If your blog is in other language, rename the file *stopwords* in the plugin directory as something else or remove it. If there is *stopwords.yourlanguage*, rename it to *stopwords*.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to the plugin settings and build the index following the instructions there.

To update your installation, simply overwrite the old files with the new, activate the new
version and if the new version has changes in the indexing, rebuild the index.

= Changes to templates =
None necessary! Relevanssi uses the standard search form and doesn't usually need any changes in
the search results template.

= How to index =
Check the options to make sure they're to your liking, then click "Save indexing options and
build the index". If everything's fine, you'll see the Relevanssi options screen again with a 
message "Indexing successful!"

If something fails, usually the result is a blank screen. The most common problem is a timeout:
server ran out of time while indexing. The solution to that is simple: just return to Relevanssi
screen (do not just try to reload the blank page) and click "Continue indexing". Indexing will
continue. Most databases will get indexed in just few clicks of "Continue indexing". You can
follow the process in the "State of the Index": if the amount of documents is growing, the 
indexing is moving along.

If the indexing gets stuck, something's wrong. I've had trouble with some plugins, for example
Flowplayer video player stopped indexing. I had to disable the plugin, index and then activate
the plugin again. Try disabling plugins, especially those that use shortcodes, to see if that
helps. Relevanssi shows the highest post ID in the index - start troubleshooting from the post
or page with the next highest ID. Server error logs may be useful, too.

= Using custom search results =
If you want to use the custom search results, make sure your search results template uses `the_excerpt()`
to display the entries, because the plugin creates the custom snippet by replacing the post excerpt.

If you're using a plugin that affects excerpts (like Advanced Excerpt), you may run into some
problems. For those cases, I've included the function `relevanssi_the_excerpt()`, which you can
use instead of `the_excerpt()`. It prints out the excerpt, but doesn't apply `wp_trim_excerpt()`
filters (it does apply `the_content()`, `the_excerpt()`, and `get_the_excerpt()` filters).

To avoid trouble, use the function like this:

`<?php if (function_exists('relevanssi_the_excerpt')) { relevanssi_the_excerpt(); }; ?>`

See Frequently Asked Questions for more instructions on what you can do with
Relevanssi.

= Uninstalling =
To uninstall the plugin, first click the "Remove plugin data" button on the plugin settins page
to remove options and database tables, then remove the plugin using the normal WordPress
plugin management tools.

= Combining with other plugins =
Relevanssi doesn't work with plugins that rely on standard WP search. Those plugins want to
access the MySQL queries, for example. That won't do with Relevanssi. [Search Light](http://wordpress.org/extend/plugins/search-light/),
for example, won't work with Relevanssi.

[Dave's WordPress Live Search](http://wordpress.org/extend/plugins/daves-wordpress-live-search/) is
an AJAX instant search plugin that works with Relevanssi. Versions up to 1.17 won't work, but
the next version after that should, as long as you have at least Relevanssi 2.5. The Live Search
will cause some strange search logs, but the search itself works.

Some plugins cause problems when indexing documents. These are generally plugins that use shortcodes
to do something somewhat complicated. One such plugin is [MapPress Easy Google Maps](http://wordpress.org/extend/plugins/mappress-google-maps-for-wordpress/).
When indexing, you'll get a white screen. To fix the problem, disable either the offending plugin 
or shortcode expansion in Relevanssi while indexing. After indexing, you can activate the plugin
again.

== Frequently Asked Questions ==

= Where is the Relevanssi search box widget? =
There is no Relevanssi search box widget.

Just use the standard search box.

= Where are the user search logs? =
See the top of the admin menu. There's 'User searches'. There. If the logs are empty, please note
showing the results needs at least MySQL 5.

= Displaying the number of search results found =

The typical solution to showing the number of search results found does not work with Relevanssi.
However, there's a solution that's much easier: the number of search results is stored in a
variable within $wp_query. Just add the following code to your search results template:

`<?php echo 'Relevanssi found ' . $wp_query->found_posts . ' hits'; ?>`

= Advanced search result filtering =

If you want to add extra filters to the search results, you can add them using a hook.
Relevanssi searches for results in the _relevanssi table, where terms and post_ids are listed.
The various filtering methods work by listing either allowed or forbidden post ids in the
query WHERE clause. Using the `relevanssi_where` hook you can add your own restrictions to
the WHERE clause.

These restrictions must be in the general format of 
` AND doc IN (' . {a list of post ids, which could be a subquery} . ')`

For more details, see where the filter is applied in the `relevanssi_search()` function. This
is stricly an advanced hacker option for those people who're used to using filters and MySQL
WHERE clauses and it is possible to break the search results completely by doing something wrong
here.

There's another filter hook, `relevanssi_hits_filter`, which lets you modify the hits directly.
The filter passes an array, where index 0 gives the list of hits in the form of an array of 
post objects and index 1 has the search query as a string. The filter expects you to return an
array containing the array of post objects in index 0 (`return array($your_processed_hit_array)`).

= Direct access to query engine =
Relevanssi can't be used in any situation, because it checks the presence of search with
the `is_search()` function. This causes some unfortunate limitations and reduces the general usability
of the plugin.

You can now access the query engine directly. There's a new function `relevanssi_do_query()`,
which can be used to do search queries just about anywhere. The function takes a WP_Query object
as a parameter, so you need to store all the search parameters in the object (for example, put the
search terms in `$your_query_object->query_vars['s']`). Then just pass the WP_Query object to
Relevanssi with `relevanssi_do_query($your_wp_query_object);`.

Relevanssi will process the query and insert the found posts as `$your_query_object->posts`. The
query object is passed as reference and modified directly, so there's no return value. The posts
array will contain all results that are found.

= Sorting search results =
If you want something else than relevancy ranking, you can use orderby and order parameters. Orderby
accepts $post variable attributes and order can be "asc" or "desc". The most relevant attributes
here are most likely "post_date" and "comment_count".

If you want to give your users the ability to sort search results by date, you can just add a link
to http://www.yourblogdomain.com/?s=search-term&orderby=date&order=desc to your search result
page.

Order by relevance is either orderby=relevance or no orderby parameter at all.

= Filtering results by date =
You can specify date limits on searches with `by_date` search parameter. You can use it your
search result page like this: http://www.yourblogdomain.com/?s=search-term&by_date=1d to offer
your visitor the ability to restrict their search to certain time limit (see
[RAPLIQ](http://www.rapliq.org/) for a working example).

The date range is always back from the current date and time. Possible units are hour (h), day (d),
week (w), month (m) and year (y). So, to see only posts from past week, you could use by_date=7d
or by_date=1w.

Using wrong letters for units or impossible date ranges will lead to either defaulting to date
or no results at all, depending on case.

Thanks to Charles St-Pierre for the idea.

= Caching =
Relevanssi has an included cache feature that'll store search results and
post excerpts in the database for reuse. It's something of an experimental 
feature right now, but should work and if there are lots of repeat queries,
it'll give some actual boost in performance.

= Displaying the relevance score =
Relevanssi stores the relevance score it uses to sort results in the $post variable. Just add
something like

`echo $post->relevance_score`

to your search results template inside a PHP code block to display the relevance score.

= Did you mean? suggestions =
To use Google-style "did you mean?" suggestions, first enable search query logging. The
suggestions are based on logged queries, so without good base of logged queries, the
suggestions will be odd and not very useful.

To use the suggestions, add the following line to your search result template, preferably
before the have_posts() check:

`<?php if (function_exists('relevanssi_didyoumean')) { relevanssi_didyoumean(get_search_query(), "<p>Did you mean: ", "?</p>", 5); }?>`

The first parameter passes the search term, the second is the text before the result,
the third is the text after the result and the number is the amount of search results
necessary to not show suggestions. With the default value of 5, suggestions are not
shown if the search returns more than 5 hits.

= Search shortcode =
Relevanssi also adds a shortcode to help making links to search results. That way users
can easily find more information about a given subject from your blog. The syntax is
simple:

`[search]John Doe[/search]`

This will make the text John Doe a link to search results for John Doe. In case you
want to link to some other search term than the anchor text (necessary in languages
like Finnish), you can use:

`[search term="John Doe"]Mr. John Doe[/search]`

Now the search will be for John Doe, but the anchor says Mr. John Doe.

One more parameter: setting `[search phrase="on"]` will wrap the search term in
quotation marks, making it a phrase. This can be useful in some cases.

= Restricting searches to categories and tags =
Relevanssi supports the hidden input field `cat` to restrict searches to certain categories (or
tags, since those are pretty much the same). Just add a hidden input field named `cat` in your
search form and list the desired category or tag IDs in the `value` field - positive numbers
include those categories and tags, negative numbers exclude them.

You can also set the restriction from general plugin settings (and then override it in individual
search forms with the special field). This works with custom taxonomies as well, just replace `cat`
with the name of your taxonomy.

If you want to restrict the search to categories using a dropdown box on the search form, use
a code like this:

`<form method="get" action="<?php bloginfo('url'); ?>">
	<div><label class="screen-reader-text" for="s">Search</label>
	<input type="text" value="<?php the_search_query(); ?>" name="s" id="s" />
<?php
	wp_dropdown_categories(array('show_option_all' => 'All categories'));
?>
	<input type="submit" id="searchsubmit" value="Search" />
	</div>
</form>`

This produces a search form with a dropdown box for categories. Do note that this code won't work
when placed in a Text widget: either place it directly in the template or use a PHP widget plugin
to get a widget that can execute PHP code.

= Restricting searches with taxonomies =

You can use taxonomies to restrict search results to posts and pages tagged with a certain 
taxonomy term. If you have a custom taxonomy of "People" and want to search entries tagged
"John" in this taxonomy, just use `?s=keyword&people=John` in the URL. You should be able to use
an input field in the search form to do this, as well - just name the input field with the name
of the taxonomy you want to use.

It's also possible to do a dropdown for custom taxonomies, using the same function. Just adjust
the arguments like this:

`wp_dropdown_categories(array('show_option_all' => 'All people', 'name' => 'people', 'taxonomy' => 'people'));`

This would do a dropdown box for the "People" taxonomy. The 'name' must be the keyword used in
the URL, while 'taxonomy' has the name of the taxonomy.

= Automatic indexing =
Relevanssi indexes changes in documents as soon as they happen. However, changes in shortcoded
content won't be registered automatically. If you use lots of shortcodes and dynamic content, you
may want to add extra indexing. Here's how to do it:

`if (!wp_next_scheduled('relevanssi_build_index')) {
	wp_schedule_event( time(), 'daily', 'relevanssi_build_index' );
}`

Add the code above in your theme functions.php file so it gets executed. This will cause
WordPress to build the index once a day. This is an untested and unsupported feature that may
cause trouble and corrupt index if your database is large, so use at your own risk. This was
presented at [forum](http://wordpress.org/support/topic/plugin-relevanssi-a-better-search-relevanssi-chron-indexing?replies=2).

= Highlighting terms =
Relevanssi search term highlighting can be used outside search results. You can access the search
term highlighting function directly. This can be used for example to highlight search terms in
structured search result data that comes from custom fields and isn't normally highlighted by
Relevanssi.

Just pass the content you want highlighted through `relevanssi_highlight_terms()` function. The
content to highlight is the first parameter, the search query the second. The content with
highlights is then returned by the function. Use it like this:

`if (function_exists('relevanssi_highlight_terms')) {
    echo relevanssi_highlight_terms($content, get_search_query());
}
else { echo $content; }`

= Multisite searching =
To search multiple blogs in the same WordPress network, use the `searchblogs` argument. You can
add a hidden input field, for example. List the desired blog ids as the value. For example, 
searchblogs=1,2,3 would search blogs 1, 2, and 3. 

The features are very limited in the multiblog search, none of the advanced filtering works, and
there'll probably be fairly serious performance issues if searching common words from multiple
blogs.

= What is tf * idf weighing? =

It's the basic weighing scheme used in information retrieval. Tf stands for *term frequency*
while idf is *inverted document frequency*. Term frequency is simply the number of times the term
appears in a document, while document frequency is the number of documents in the database where
the term appears.

Thus, the weight of the word for a document increases the more often it appears in the document and
the less often it appears in other documents.

= What are stop words? =

Each document database is full of useless words. All the little words that appear in just about
every document are completely useless for information retrieval purposes. Basically, their
inverted document frequency is really low, so they never have much power in matching. Also,
removing those words helps to make the index smaller and searching faster.

== Known issues and To-do's ==
* Known issue: The most common cause of blank screens when indexing is the lack of the mbstring extension. Make sure it's installed.
* Known issue: In general, multiple Loops on the search page may cause surprising results. Please make sure the actual search results are the first loop.
* Known issue: Relevanssi doesn't necessarily play nice with plugins that modify the excerpt. If you're having problems, try using relevanssi_the_excerpt() instead of the_excerpt().
* Known issue: Custom post types and private posts is problematic - I'm using default 'read_private_*s' capability, which might not always work.
* Known issue: There are reported problems with custom posts combined with custom taxonomies, the taxonomy restriction doesn't necessarily work.
* Known issue: Phrase matching is only done to post content; phrases don't match to category titles and other content.
* Known issue: User searches page requires MySQL 5.
* To-do: Option to set the number of search results returned.
* To-do: Tool to export and import Relevanssi settings.

== Thanks ==
* Cristian Damm for tag indexing, comment indexing, post/page exclusion and general helpfulness.
* Marcus Dalgren for UTF-8 fixing.
* Warren Tape.
* Mohib Ebrahim for relentless bug hunting.
* John Blackbourn for amazing internal link feature.

== Changelog ==

= 1.3.1 =
* Fixed the multiple taxonomy search logic to AND instead of OR.
* Some small security fixes.

= 1.3 =
* Bug fix: when choosing mark highlighting, the option screen showed wrong option.
* Category restrictions now include subcategories as well to mirror WordPress default behaviour.
* Internal links can be now indexed for the source, target or both source and target.
* It's now possible to limit searches by custom fields.
* It's now possible to use more than one taxonomy at the same time.

= 1.2 =
* Relevanssi can now highlight search terms from incoming queries.
* Spelling correction in Did you mean searches didn't work.
* Some shortcode plugins (Catablog, for example) were having trouble; fixed that.

= 1.1.2 =
* The plugin didn't update databases correctly, causing problems.

= 1.1.1 =
* Very small fix that improves plugin compatibility with Relevanssi when using shortcodes.

= 1.1 =
* Multisite WordPress support. See FAQ for instructions on how to search multiple blogs.
* Improved the fallback to fuzzy search if no hits are found with regular search.
* AND searches sometimes failed to work properly, causing unnecessary fallback to OR search. Fixed.
* When using WPML, it's now possible to choose if the searches are limited to current language.
* Adding stopwords from the list of 25 common words didn't work. It works now.
* The instructions to add a category dropdown to search form weren't quite correct. They are now.
* It's now possible to assign weights for post types.
* User profiles can be indexed and searched.

= 1.0 =
* First published version, matches Relevanssi 2.7.3.