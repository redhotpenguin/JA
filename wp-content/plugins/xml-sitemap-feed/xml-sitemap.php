<?php
/*
Plugin Name: XML Sitemap Feed
Plugin URI: http://4visions.nl/en/wordpress-plugins/xml-sitemap-feed/
Description: Creates a feed that complies with the XML Sitemap protocol ready for indexing by Google, Yahoo, Bing, Ask and others. Happy with it? Please leave me a <strong><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=XML%20Sitemap%20Feed&item_number=3%2e8&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8&lc=us">Tip</a></strong> for development and support time. Thanks :)
Version: 3.9.1
Author: RavanH
Author URI: http://4visions.nl/
*/

/*  Copyright 2010 RavanH  (http://4visions.nl/ email : ravanhagen@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* --------------------
 *  AVAILABLE HOOKS
 * --------------------
 *
 * FILTERS
 *	xml_sitemap_url	->	Filters the URL used in the sitemap reference in robots.txt
 *				(receives an ARRAY and MUST return one; can be multiple urls) 
 *				and for the home URL in the sitemap (receives a STRING and MUST)
 *				return one) itself. Useful for multi language plugins or other 
 *				plugins that affect the blogs main URL... See pre-defined filter
 *				XMLSitemapFeed::qtranslate() in XMLSitemapFeed.class.php as an
 *				example.
 * ACTIONS
 *	[ none at this point, but feel free to request, suggest or code one :) ]
 *	
 */

/* --------------------
 *      CONSTANTS
 * -------------------- */
define('XMLSF_VERSION','3.9.1');
define('XMLSF_MEMORY_LIMIT','128M');

if (file_exists(dirname(__FILE__).'/xml-sitemap-feed'))
	define('XMLSF_PLUGIN_DIR', dirname(__FILE__).'/xml-sitemap-feed');
else
	define('XMLSF_PLUGIN_DIR', dirname(__FILE__));		

/* -----------------
 *      CLASS
 * ----------------- */

if( class_exists('XMLSitemapFeed') || include( XMLSF_PLUGIN_DIR . '/XMLSitemapFeed.class.php' ) )
	XMLSitemapFeed::go();

/* -------------------------------------
 *      MISSING WORDPRESS FUNCTIONS
 * ------------------------------------- */

include_once(XMLSF_PLUGIN_DIR . '/hacks.php');

