<?php
/*
Plugin Name: Links in Captions
Plugin URI: http://www.seodenver.com/lottery/
Description: Easily add links to image captions in the WordPress editor.
Author: Katz Web Services, Inc.
Version: 1.2
Author URI: http://www.katzwebservices.com
*/

/*
Copyright 2010 Katz Web Services, Inc.  (email: info@katzwebservices.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

add_shortcode('add_caption_link', 'add_link_to_caption_shortcode_shortcode');
add_filter('img_caption_shortcode', 'add_link_to_caption_shortcode', true, 3);
// This function is taken from wp-includes/media.php
function add_link_to_caption_replace_quotes2($string, $type = 'add') {
	$out = '}';
		if(is_array($string)) { $text = $string[1]; } else { $text = $string; }
		$text = str_replace("'",'%%squote%%', $text);
		$text = str_replace('&#39;','%%squote%%', $text);
		$text = str_replace('&quot;','%%dquote%%', $text);
		$text = str_replace('"','%%dquote%%', $text);
	
	$out .= $text.'{/link}';
	return $out;
}
function add_link_to_caption_replace_quotes($string, $type = 'add') {
	if($type == 'add') {
		if(is_array($string)) { $text = $string[1]; } else { $text = $string; }
		$text = str_replace("'",'%%squote%%', $text);
		$text = str_replace('&#39;','%%squote%%', $text);
		$text = str_replace('&quot;','%%dquote%%', $text);
		$text = str_replace('"','%%dquote%%', $text);
		return $text; 
	} else {
		$string = str_replace('%%squote%%', '\'', $string);
		$string = str_replace('%%dquote%%', '"', $string);
		return $string;
	}
}
function add_link_to_caption_shortcode($empty, $attr, $content) {
	$caption = '';
	if(!isset($attr['caption'])) {
		// Used double quotes for link
		$match = false; $string = $key = ''; $attrCopy = $attr; 
		foreach($attr as $key => $att) {
			if(preg_match('/\{link/ism', $att)) { $match = true; }
			if(!is_numeric($key)) {
				$attrCopy[$key] = $key.'="'.add_link_to_caption_replace_quotes($att).'"';
			}
		}

		if($match) {
			$string = implode(' ', $attrCopy);
			$string = preg_replace('/(href|url|rel|target|title|text)=(?:\s+)?(?:\'|&#39;)(.*?)(?:\'|&#39;)/ism', '$1=%%squote%%$2%%squote%%', $string);
			$string = preg_replace('/(href|url|rel|target|title|text)=(?:\s+)?(?:"|&quot;)(.*?)(?:"|&quot;)/ism', '$1=%%dquote%%$2%%dquote%%', $string);
			$string = preg_replace_callback('/((?:href|url|rel|target|title|text)=(?:%%dquote%%|%%squote%%)(?:.*?)(?:%%dquote%%|%%squote%%))/ism', 'add_link_to_caption_replace_quotes', $string);
			$string = preg_replace_callback('/\}(.*?)\{\/link\}/ism', 'add_link_to_caption_replace_quotes2', $string);
			$caption = preg_match('/caption(?:\s+)?\=(?:\s+)?(?:\'|"|&quot;|&#39;)(.*?)(?:\'|"|&quot;|&#39;)/ism', $string, $m);
			$caption = $m[1];
		}
	}
	extract(shortcode_atts(array(
		'id'	=> '',
		'align'	=> 'alignnone',
		'width'	=> '',
		'caption' => $caption
	), $attr));

	# BEGIN Added for this plugin
		// replaces {link rel="nofollow" url="http://www.example.com"}Text{/link}
		$caption = preg_replace('/\{link(.*?)\}(.*?)\{\/link\}/ism', '[add_caption_link$1]$2[/add_caption_link]', $caption);
		$caption = preg_replace('/\{a(.*?)\}(.*?)\{\/a}/ism', '[add_caption_link$1]$2[/add_caption_link]', $caption);

		// Added for this plugin it replaces {link rel="nofollow" url="http://www.example.com" text="Text" /}
		$caption = preg_replace('/\{a(.*?)\/\}/ism', '[add_caption_link $1 /]', $caption);
		$caption = str_replace('&quot;', '"', $caption);

	# END Added for this plugin
	if ( 1 > (int) $width || empty($caption) )
		return add_link_to_caption_replace_quotes($content, 'remove');

	if ( $id ) $id = 'id="' . esc_attr($id) . '" ';
			
	// Added do_shortcode() to the $caption for this plugin
	return '<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '" style="width: ' . (10 + (int) $width) . 'px">'
	. do_shortcode( $content ) . '<p class="wp-caption-text">' . add_link_to_caption_replace_quotes(do_shortcode($caption), 'remove') . '</p></div>';
}
function add_link_to_caption_shortcode_shortcode($attr, $content = null) {
	foreach($attr as $k => $v) {
		if($k != 'text' && $k != 'title') {
			$attr[$k] = str_replace('%%squote%%', '', str_replace('%%dquote%%', '', str_replace('"','', str_replace('&#39;', '', str_replace('&quot;', '', str_replace('\'', '', $v))))));
		}
	}
	extract(shortcode_atts(array(
		'url'		=> '',
		'href'		=> '',
		'target'	=> '',
		'title'		=> '',
		'rel'		=> '',
		'text'		=> ''
	), $attr));
	
	$text =  empty($text) ? '' : add_link_to_caption_replace_quotes($text, 'remove');
	$content = empty($content) ? '' : add_link_to_caption_replace_quotes($content, 'remove');
	$title = empty($title) ? '' : add_link_to_caption_replace_quotes($title, 'remove');
	
	if(!empty($url)) {
		$link = ' href="'.$url.'"';
	} elseif(!empty($href)) {
		$link = ' href="'.$href.'"';
	}
	
	$rel = empty($rel) ? '' :  ' rel="'.$rel.'"';
	$target = empty($target) ? '' :  ' target="'.$target.'"';
	$title = empty($title) ? '' :  ' title="'.$title.'"';
	
	if(empty($link) || (empty($content) && empty($text))) { return $content; }
	else if(empty($content)) { $content = $text; }
	
	return "<a{$link}{$target}{$title}{$rel}>$content</a>";
}


?>