<?php
/*
| --------------------------------------------------------
| File        : config.php
| Project     : Special Recent Posts plugin for Wordpress
| Version     : 1.9
| Description : This file contains the default global
|               enviroment values
| Author      : Luca Grandicelli
| Author URL  : http://www.lucagrandicelli.com
| Plugin URL  : http://www.lucagrandicelli.com/special-recent-posts-plugin-for-wordpress/
| --------------------------------------------------------
*/

/*
| ---------------------------------------------
| GLOBAL ENVIROMENT VALUES
| ---------------------------------------------
*/

// Defining global default plugin values.
global $srp_default_plugin_values;

$srp_default_plugin_values = array(
	'srp_version'              => '1.9',
	'srp_thumbnail_width'      => 100,
	'srp_thumbnail_height'     => 100,
	'srp_thumbnail_link'       => 'yes',
	'srp_thumbnail_url'        => SRP_PLUGIN_URL . SRP_DEFAULT_THUMB,
	'srp_excerpt_length'       => '100',
	'srp_excerpt_length_mode'  => 'chars',
	'srp_excerpt_allowed_tags' => '',
	'srp_title_length'         => '100',
	'srp_title_length_mode'    => 'fulltitle',
	'srp_string_break'         => '[...]',
	'srp_string_break_link'    => 'yes',
	'srp_date_content'         => 'F jS, Y',
	'srp_post_offset'          => 'no',
	'srp_category_title'       => 'no',
	'srp_noposts_message'      => __('No posts available', SRP_TRANSLATION_ID),
	'srp_themecss'             => '

/* The Recent Posts Container. */
div.srp-widget-container {
	display : table;
	clear   : both;
}

/* The Widget Title. */
div.srp-widget-container h3.widget-title{
	display       : table-caption;
	margin-bottom : 10px;
}

/* Single post entry box. */
div.srp-widget-singlepost {
	padding       : 0px 0px 10px 0px;
	margin        : 0px 0px 10px 0px;
	border-bottom : 1px solid #CCCCCC;
}

/* The thumbnail box. */
div.srp-thumbnail-box {
	display        : table-cell;
	vertical-align : top;
	padding-right  : 10px;
}

/* The content stuff box. */
div.srp-content-box {
	display        : table-cell;
	vertical-align : top;
}

/* The excerpt. */
p.srp-widget-excerpt {
	margin: 0px;
}

/* Single post title. */
h4.srp-widget-title {}

/* Single post title link. */
a.srp-widget-title-link {}

/* Thumbnail link. */
a.srp-widget-thmblink {}

/* Thumbnail image. */
img.srp-widget-thmb {}

/* The post date box. */
span.srp-widget-date {}

/* Stringbreak link. */
a.srp-widget-stringbreak-link {}

/* Stringbreak link image. */
a.srp-widget-stringbreak-link-image {}

/* Stringbreak. */
span.srp-widget-stringbreak {}
'
);


// Defining global default widget values.
global $srp_default_widget_values;

$srp_default_widget_values = array(
	'srp_post_type'                 => 'post',
	'srp_post_status_option'        => 'publish',
	'srp_custom_post_type_option'   => '',
	'srp_widget_title'              => __('Special Recent Posts', SRP_TRANSLATION_ID),
	'srp_widget_title_hide_option'  => 'no',
	'srp_thumbnail_option'          => 'yes',
	'srp_thumbnail_rotation'        => 'no',
	'srp_number_post_option'        => 5,
	'srp_wdg_excerpt_length'        => '100',
	'srp_wdg_excerpt_length_mode'   => 'chars',
	'srp_wdg_title_length'          => '100',
	'srp_wdg_title_length_mode'     => 'fulltitle',
	'srp_order_post_option'         => 'DESC',
	'srp_post_global_offset_option' => 0,
	'srp_orderby_post_option'       => '',
	'srp_filter_cat_option'         => '',
	'srp_content_post_option'       => 'titleexcerpt',
	'srp_post_date_option'          => 'yes',
	'srp_exclude_option'            => '',
	'srp_add_nofollow_option'       => 'no'
);
