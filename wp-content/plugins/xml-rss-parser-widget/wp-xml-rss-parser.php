<?php
/*
	Plugin Name: XML-RSS Parser Widget
	Plugin URI: http://jendela.web.id/2010/06/28/xml-rss-parser-widget-wordpress-plugin/
	Description:	A light xml-rss parser plugin for your wordpress widget to show user your favourite xml or rss site url with option for showing the total item you want to show to user. This plugin parse the general xml-rss item tag [title, link, description and pubDate]	into a html coding.
	Version: 1.0
	Author: Sastra Manurung
	Author URI: http://zourbuth.web.id
	License: Under GPL2
*/

/*  
	Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : zourbuth@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
include_once($plugin_path . 'parser.php');


function widget_gsearch_init() {
	if ( !function_exists('register_sidebar_widget') )
		return;
	function widget_gsearch($args) {
		extract($args);

		$options = get_option('widget_gsearch');
		$title = $options['title'];
		//$item_type = $options['item_type'];
		$item = $options['item'];
		$url = $options['url'];

		$url_flux_rss		= $url;
		$item_no			= $item; 
		$rss				= new parseXMLRSS;
		$rss->cache_dir		= $plugin_path. 'cache'; 
		$rss->cache_time	= 3600; 
		//$rss->date_format	= 'd/m/y'; 
		$rss->CDATA			= 'content';

		echo $before_widget . $before_title . $title . $after_title;
		$url_parts = parse_url(get_bloginfo('home'));

		if ($rs = $rss->get($url_flux_rss)) 
		{
			if (!is_numeric($item)) 
				$item_no = 3;
			for($i=0;$i<$item_no;$i++)
			{
				$img = str_replace("'",'"',$rs['items'][$i]['description']);
				$img = str_replace('&lt;','<',$img);
				$img = str_replace('&gt;','>',$img);
				$text = '<p><a href="'.$rs['items'][$i]['link'].'">' . $rs['items'][$i]['title'].'</a><br /><small>'.$rs['items'][$i]['pubDate'].'</small><br />'.$img.'</p>';
				echo $text;
			}
		}
		else 
		{
		  $text = 'Not available, please check again later!';
		  echo $text;
		}

		echo $after_widget;
	}

	function widget_gsearch_control() {

		$options = get_option('widget_gsearch');
		if ( !is_array($options) )
			$options = array('title'=>'', 'item'=>__('Google Search', 'widgets'), 'url'=>'');
		if ( $_POST['gsearch-submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['xparse-title']));
			//$options['item_type'] = strip_tags(stripslashes($_POST['xitem-type']));
			$options['item'] = strip_tags(stripslashes($_POST['gsearch-buttontext']));
			$options['url'] = strip_tags(stripslashes($_POST['xparse-url']));
			update_option('widget_gsearch', $options);
		}

		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$item_type = htmlspecialchars($options['item_type'], ENT_QUOTES);
		$item = htmlspecialchars($options['item'], ENT_QUOTES);
		$url = htmlspecialchars($options['url'], ENT_QUOTES);

		echo '<p><label for="xparse-title">' . __('Widget Title:') . '</label><input class="widefat" id="xparse-title" name="xparse-title" type="text" value="'.$title.'" /></p>';
		echo '<p><label for="xparse-url">' . __('Source Url:', 'widgets') . '</label><input class="widefat" id="xparse-url" name="xparse-url" type="text" value="'.$url.'" /><span style="font-size:0.8em">Example: http://rss.cnn.com/rss/edition.rss</span></p>';
		//echo '<p><label for="xitem-type">' . __('Item Type:', 'widgets') . '</label><input class="widefat" id="xitem-type" name="xitem-type" type="text" value="'.$item_type.'" /><br /><span style="font-size:0.8em">Item type/tag separate by coma, example: link, title, pubdate</span></p>';
		echo '<p><label for="gsearch-buttontext">' . __('Item Number:', 'widgets') . '</label><input class="widefat" id="gsearch-buttontext" name="gsearch-buttontext" type="text" value="'.$item.'" /><br /><span style="font-size:0.8em">The number of showed items, by default is 3</span></p>';

		echo '<input type="hidden" id="gsearch-submit" name="gsearch-submit" value="1" />';
		echo '<hr style="border: 0 none #FFFFFF;border-top: 1px solid #CCCCCC;height: 1px;/><br />';
	}
	register_sidebar_widget(array('XML-RSS Parser', 'widgets'), 'widget_gsearch');
	register_widget_control(array('XML-RSS Parser', 'widgets'), 'widget_gsearch_control');
}

add_action('widgets_init', 'widget_gsearch_init');
?>
