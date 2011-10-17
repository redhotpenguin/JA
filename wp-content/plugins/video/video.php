<?php
/**
 * @package video
 * @category video
 * @author Automattic Inc
 * @link http://automattic.com/wordpress-plugins/#videopress VideoPress
 * @version 1.2.2
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* 
Plugin Name: VideoPress
Plugin URI: http://wordpress.org/extend/plugins/video/
Description: Upload new videos to <a href="http://videopress.com/">VideoPress</a>, edit metadata, and easily insert VideoPress videos into posts and pages using shortcodes. Requires a <a href="http://wordpress.com/">WordPress.com</a> account and a WordPress.com blog with the <a href="http://en.wordpress.com/products/#videopress">VideoPress upgrade</a>.
Author: Automattic, Niall Kennedy, Joseph Scott
Contributor: Hailin Wu
Author URI: http://automattic.com/wordpress-plugins/#videopress
Version: 1.2.2
Stable tag: 1.2.2
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

include_once( dirname(__FILE__) . '/settings.php' );

add_action( 'media_buttons', 'videopress_media_buttons', 999 );

/**
 * Add a video button to the post composition screen
 * @since 0.1.0
 */
function videopress_media_buttons( ) {
	echo '<a href="https://public-api.wordpress.com/videopress-plugin.php?page=video-plugin&amp;video_plugin=1&amp;iframe&amp;TB_iframe=true" id="add_video" class="thickbox" title="VideoPress"><img src="';
	echo esc_url( plugins_url( ) . '/' . dirname( plugin_basename( __FILE__ ) ) . '/camera-video.png' );
	echo '" alt="VideoPress" width="16" height="16" /></a>';
}

//allow either [videopress xyz] or [wpvideo xyz] for backward compatibility
add_shortcode( 'videopress', 'videopress_shortcode' );
add_shortcode( 'wpvideo', 'videopress_shortcode' );

/**
 * Validate user-supplied guid values against expected inputs
 *
 * @since 1.1
 * @param string $guid video identifier
 * @return bool true if passes validation test
 */
function __videopress_is_valid_guid( $guid ) {
	if ( !empty($guid) && ctype_alnum($guid))
		return true;
	else
		return false;
}

/**
 * Search a given content string for VideoPress shortcodes. Return an array of shortcodes with guid and attribute values.
 *
 * @see do_shortcode()
 * @param string $content post content string
 * @return array Array of shortcode data. GUID as the key and other customization parameters as value. empty array if no matches found.
 */
function find_all_videopress_shortcodes( $content ) {
	$r = preg_match_all( '/(.?)\[(wpvideo)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s', $content, $matches, PREG_SET_ORDER );

	if ( $r === false || $r === 0 ) 
		return array();

	unset( $r );

	$guids = array();
	foreach ( $matches as $m ) {
		// allow [[foo]] syntax for escaping a tag
		if ( $m[1] == '[' && $m[6] == ']' )
			continue;
		$attr = shortcode_parse_atts( $m[3] );
		if ( __videopress_is_valid_guid( $attr[0] ) ) {
			$guid = $attr[0];
			unset( $attr[0] );
			$guids[$guid] = $attr;
			unset( $guid );
		}
		unset( $attr );
	}

	return $guids;
}

/**
 * Insert video handlers into HTML <head> if posts with video shortcodes exist.
 * If video posts are present add SWFObject JS and attach events for each movie container's identifier.
 */
function video_embed_head() {
	global $posts;

	if ( is_feed() || !is_array($posts) || get_option( 'video_player_freedom', false )===true )
		return;

	$guids = array();
	foreach ($posts as $post) {
		$guids = array_merge( $guids, find_all_videopress_shortcodes( $post->post_content ) );
	}

	if ( is_array($guids) && count($guids) > 0 ) {
		echo '<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>';
		$embed_seq = 0;
		$script_block = '<script type="text/javascript" charset="utf-8">' . "\n" . '// <![CDATA[' . "\n"; // escape the JS block for strict parsers
		foreach ($guids as $guid) {
			$script_block .= "swfobject.registerObject('video$embed_seq','10.0.0');";
			$embed_seq++;
		}
		echo $script_block . "\n" . '// ]]>' . "\n" . '</script>';
	}
}
add_action('wp_head', 'video_embed_head');

/**
 * Extract the site's host domain for statistics and comparison against an allowed site list in the case of restricted embeds.
 *
 * @since 1.2
 * @param string $url absolute URL
 * @return bool|string host component of the URL, or false if none found
 */
function videopress_host( $url ) {
	if ( empty($url) || !function_exists('parse_url') )
		return false;

	if ( version_compare(PHP_VERSION, '5.1.2', '>=') ) {
		return parse_url($url, PHP_URL_HOST);
	} else {
		$url_parts = parse_url($url);
		if ( $url_parts!==false && isset($url_parts['host']) )
			return $url_parts['host'];
	}
}


/**
 * Prepare key-value pairs for inclusion as an URI's query parameters
 * Emulate http_build_query if current version of PHP does not include the function.
 *
 * @since 1.2
 * @uses build_http_query if present
 * @param array $args key-value pairs to include as query parameters
 * @return string prepped query parameter string
 */
function videopress_http_build_query( $args ) {
	if ( function_exists('http_build_query') ) {
		return http_build_query( $args, '', '&' );
	}
	else {
		$str_builder = array();
		foreach ( $args as $key => $value ) {
			$str_builder[] = urlencode($key) . '=' . urlencode($value);
		}
		return implode('&', $str_builder);
	}
}


/**
 * Only allow legitimate Flash parameters and their values
 *
 * @since 1.2
 * @link http://kb2.adobe.com/cps/127/tn_12701.html Flash object and embed attributes
 * @link http://kb2.adobe.com/cps/133/tn_13331.html devicefont
 * @link http://kb2.adobe.com/cps/164/tn_16494.html allowscriptaccess
 * @link http://www.adobe.com/devnet/flashplayer/articles/full_screen_mode.html full screen mode
 * @link http://livedocs.adobe.com/flash/9.0/main/wwhelp/wwhimpl/common/html/wwhelp.htm?context=LiveDocs_Parts&file=00001079.html allownetworking
 * @param array $flash_params Flash parameters expressed in key-value form
 * @return array validated Flash parameters
 */
function videopress_esc_flash_params( $flash_params ) {
	static $allowed_params = array(
		'swliveconnect' => array('true', 'false'),
		'play' => array('true', 'false'),
		'loop' => array('true', 'false'),
		'menu' => array('true', 'false'),
		'quality' => array('low', 'autolow', 'autohigh', 'medium', 'high', 'best'),
		'scale' => array('default', 'noorder', 'exactfit'),
		'align' => array('l', 'r', 't'),
		'salign' => array('l', 'r', 't', 'tl', 'tr', 'bl', 'br'),
		'wmode' => array('window', 'opaque', 'transparent'),
		'devicefont' => array('_sans', '_serif', '_typewriter'),
		'allowscriptaccess' => array('always', 'samedomain', 'never'),
		'allownetworking' => array('all','internal', 'none'),
		'seamlesstabbing' => array('true', 'false'),
		'allowfullscreen' => array('true', 'false'),
		'base',
		'bgcolor',
		'flashvars'
	);

	$allowed_params_keys = array_keys( $allowed_params );

	$filtered_params = array();
	foreach( $flash_params as $param=>$value ) {
		if ( empty($param) || empty($value) )
			continue;
		$param = strtolower($param);
		if ( in_array($param, $allowed_params_keys) ) {
			if ( isset( $allowed_params[$param] ) && is_array( $allowed_params[$param] ) ) {
				$value = strtolower($value);
				if ( in_array( $value, $allowed_params[$param] ) )
					$filtered_params[$param] = $value;
			} else {
				$filtered_params[$param] = $value;
			}
		}
	}
	unset( $allowed_params_keys );

	/**
	 * Flash specifies sameDomain, not samedomain. change from lowercase value for preciseness
	 */
	if ( isset( $filtered_params['allowscriptaccess'] ) && $filtered_params['allowscriptaccess'] === 'samedomain' )
		$filtered_params['allowscriptaccess'] = 'sameDomain';

	return $filtered_params;
}

/**
 * Display helper fallback markup for user agents unable to display embedded Flash content.
 *
 * @since 1.2
 * @return string p element with helper text
 */
function videopress_flash_fallback_markup() {
	return '<p class="robots-nocontent">' . sprintf( __('This movie requires <a rel="%s" href="%s">Adobe Flash</a> for playback.', 'video'), 'nofollow', 'http://www.adobe.com/go/getflashplayer') . '</p>';
}

/**
 * Build a HTML markup string for the double-baked object Flash player
 *
 * @since 1.2
 * @param object $video Video response
 * @param string $element_id element identifier determined by the embed sequence
 * @param int $width video player width
 * @param int $height video player height
 * @param string $element object|embed|video
 * @param string $params_define define runtime parameters inline or as flashvars param. any value other than 'inline' is ignored.
 * @param array $options allow extension of standard runtime variables with player customizations. autoplay
 */
function videopress_flash_object_markup( $video, $element_id, $width, $height, $element='object', $params_define='flashvars', $options=array() ) {
	if ( !isset($video->title) || empty($video->title) ) {
		$loading_text = esc_attr( __('Loading video...', 'video') );
		$title_fallback = '';
	} else {
		$loading_text = esc_attr($video->title);
		$title_fallback = '<p><strong>' . esc_html($video->title) . '</strong></p>';
	}
	if ( is_ssl() || $element==='embed' || !isset($video->posterframe) || empty($video->posterframe) ) {
		$thumbnail = '';
	} else {
		$thumbnail = '<div class="videopress-thumbnail"><img ';
		if ( isset($video->title) && !empty($video->title) )
			$thumbnail .= 'alt="' . esc_attr($video->title) . '" ';
		$thumbnail .= 'src="' . esc_url( $video->posterframe ) . "\" width=\"$width\" height=\"$height\" /></div>";
	}

	if ( $element==='embed' || $element==='object' ) {
		$flash_params = (array) $video->swf->params;
		if ( isset( $video->swf->vars ) && !empty( $video->swf->vars ) ) {
			$flash_vars = (array) $video->swf->vars;
			$flash_vars['site'] = 'wporg';
			if ( !empty($options) && isset( $options['autoplay'] ) && $options['autoplay']===true )
				$flash_vars['autoPlay'] = 'true';
			$flash_params['flashvars'] = videopress_http_build_query( $flash_vars );
			unset($flash_vars);
		}
		$flash_params = videopress_esc_flash_params( apply_filters( 'video_flash_params', $flash_params, 10, 1 ) );

		if ( $params_define === 'inline' && isset( $flash_params['flashvars'] ) ) {
			$src = esc_url( $video->swf->url . '&' . $flash_params['flashvars'], array( 'http' ) );
			unset( $flash_params['flashvars'] );
		} else {
			$src = esc_url( $video->swf->url, array('http') );
		}

		if ( $element === 'embed' ) {
			$params_attributes='';
			foreach ( $flash_params as $attribute=>$value ) {
				$params_attributes .= ' ' . esc_html($attribute) . '="' . esc_attr($value) . '"';
			}
			return "<embed id=\"$element_id\" type=\"application/x-shockwave-flash\" src=\"$src\" width=\"$width\" height=\"$height\" title=\"$loading_text\"$params_attributes /></embed>";
		} elseif ( $element === 'object' ) {
			$params_elements = '';
			foreach ( $flash_params as $name=>$value ) {
				$params_elements .= '<param name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" />' . "\n";
			}
			unset( $flash_params );
			$flash_help = videopress_flash_fallback_markup();
			return <<<OBJECT
<object id="$element_id" class="videopress" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="$width" height="$height" standby="$loading_text">
<param name="movie" value="$src" />
$params_elements
  <!--[if !IE]>-->
  <object type="application/x-shockwave-flash" data="$src" width="$width" height="$height" standby="$loading_text">
$params_elements
  <!--<![endif]-->
  {$thumbnail}{$title_fallback}{$flash_help}
  <!--[if !IE]>-->
  </object>
  <!--<![endif]-->
</object>
OBJECT;
		}
	} elseif ( $element === 'video' && isset( $video->ogv ) ) {
		$fallback_help_text = '<p class="robots-nocontent">' . sprintf( __('You do not have sufficient <a rel="%s" href="%s">freedom levels</a> to view this video.', 'video'), 'nofollow', 'http://www.gnu.org/philosophy/free-sw.html') . '</p>';
		$src = esc_url( $video->ogv->url , 'http' );
		if ( isset($options['autoplay']) && $options['autoplay']===true )
			$load .= 'autoplay="true"';
		else
			$load .= 'preload="none"';
		return <<<VIDEO
<video id="$element_id" width="$width" height="$height" poster="{$video->posterframe}" controls="true" $load>
<source src="$src" type="video/ogg; codecs=&quot;{$video->ogv->codecs}&quot;" />
{$thumbnail}{$title_fallback}{$fallback_help_text}
</video>
VIDEO;
	}
	return '';
}

/**
 * Request information for the given VideoPress guid and optional desired width from VideoPress servers
 *
 * @param string $guid VideoPress identifier
 * @param int $maxwidth Maximum desired video width, or 0 if no width specified.
 * @return 
 */
function videopress_remote_video_info( $guid, $maxwidth=0 ) {
	$blog_domain = videopress_host( get_bloginfo('url') );
	if ( empty($blog_domain) )
		return;
	$maxwidth = absint( $maxwidth );

	$request_params = array('guid'=>$guid, 'domain'=>$blog_domain);
	if ( $maxwidth > 0 )
		$request_params['maxwidth'] = $maxwidth;

	$response = wp_remote_get( 'http://videopress.com/data/wordpress.json?' . videopress_http_build_query( $request_params ) );
	unset( $request_params );
	$response_body = wp_remote_retrieve_body( $response );

	if ( is_wp_error($response) ) {
		if ( in_array( 'http_request_failed', $response->get_error_codes() ) ) {
			if ( $response->get_error_message('http_request_failed') === '403: Forbidden' )
				return new WP_Error( 'http403', __videopress_error_placeholder( __('Embed error', 'video'),  '<div>' . sprintf( __('<strong>%s</strong> is not an allowed embed site.', 'video'), esc_html($blog_domain) ) . '</div><div>' . __('Publisher limits playback of video embeds.', 'video') . '</div>' ) );
		}
		return;
	} elseif ( wp_remote_retrieve_response_code( $response ) != 200 || empty( $response_body ) ) {
		return;
	}

	return json_decode( $response_body );
}

/**
 * Replaces wpvideo shortcode and customization parameters with full HTML markup for video playback
 *
 * @since 0.1.0
 * @link http://codex.wordpress.org/Shortcode_API
 *
 * @param array $attr an array of attributes: video guid, width (w), height (h), freedom, and autoplay
 * @return string HTML markup enabling video playback for the given video, or empty string if incorrect syntax
 */
function videopress_shortcode( $attr ) {
	global $content_width, $current_blog;
	static $embed_seq = -1;

	$guid = $attr[0];
	if ( !__videopress_is_valid_guid($guid) )
		return '';

	extract( shortcode_atts( array(
		'w'=>0,
		'h'=>0,
		'freedom'=>false,
		'autoplay'=>false
	), $attr ) );

	$width = absint($w);
	unset($w);
	$height = absint($h);
	unset($h);
	$freedom = (bool) $freedom;
	$autoplay = (bool) $autoplay;

	if ( isset($content_width) && $width > $content_width )
		$width = $height = 0;

	if ( $width === 0 && isset($content_width) && $content_width > 0 )
		$width = $content_width;

	$key = "video-info-by-$guid-$width";
	$cached_video = wp_cache_get( $key, 'video-info' );
	if ( empty($cached_video) ) {
		$video = videopress_remote_video_info( $guid, $width );
		if ( is_wp_error($video) ) {
			if ( current_user_can('edit_posts') )
				return $video->get_error_message();
			else
				return '';
		} elseif( !empty($video) ) {
			wp_cache_set( $key, serialize($video), 'video_info', 24*60*60 );
		}
	} else {
		$video = unserialize($cached_video);
	}

	if ( empty($video) )
		return '';

	$embed_seq++;

	/**
	 * Width and/or height may be provided in the shortcode or in the response
	 * Maintain aspect ratio of the original video unless shortcode specifies both a width and a height
	 */
	if ( 0 === $width && 0 === $height ) {
		$width = $video->width;
		$height = $video->height;
	}

	if ( 0 == $width )
		$width = (int) ( ( $video->width * $height ) / $video->height );
	elseif (0 == $height)
		$height = (int) ( ( $video->height * $width ) / $video->width );

	if ( $width %2 === 1 )
		$width--;
	if ( $height %2 === 1 )
		$height--;

	$element_id = 'video' . $embed_seq;

	$options = array();
	if ( $autoplay===true )
		$options['autoplay'] = true;

	if ( get_option( 'video_player_freedom', false ) || $freedom===true ) {
		 // HTML5 video Ogg Theora, Vorbis
		return videopress_flash_object_markup( $video, $element_id, $width, $height, 'video', null, $options );
	} elseif ( is_feed() ) {
		/**
		 * Some feed readers such as Google Reader only accept an embed HTML element and ignore all parameters, including FlashVars.
		 * Create special embed markup and include FlashVars values in the SWF URI
		 */
		return videopress_flash_object_markup( $video, $element_id, $width, $height, 'embed', 'inline', $options );
	} else {
		// double-baked object
		return videopress_flash_object_markup( $video, $element_id, $width, $height, 'object', null, $options ); // object with FlashVars
	}
}

/**
 * Display a VideoPress error message if an embed is unsuccessful
 *
 * @param string $text main error display plaintext
 * @param string $subtext additional information about the error. may include HTML.
 * @param string $text_type type of the text message display
 * @param int $width width of the error display element
 * @param int $height height of the error display element
 */
function __videopress_error_placeholder( $text='', $subtext='', $text_type='error', $width=400, $height=300, $context='blog' ) {
	$text = esc_html( $text ); // escape text for inclusion in HTML
	if ($text_type == 'error' )
		$text = "<span class='video-plh-error'>$text</span>";
	$class = $width >= 380? 'video-plh-full' : 'video-plh-thumb';
	if ( $context == 'blog' ) {
		$align = 'center';
		$margin = 'margin:auto';
	}
	else {
		$align = 'left';
		$margin = '';
	}
	$mid_width = $width - 16;
	$res = '';
	if ( !is_feed() ) {
		$res = <<<STYLE
	<style type="text/css">
		.video-plh {font-family:Trebuchet MS, Arial, sans-serif;text-align:$align;margin:3px;}
		.video-plh-notice {background-color:black;color:white;display:table;#position:relative;line-height:1.0em;text-align:$align;$margin;}
		.video-plh-mid {text-align:$align;display:table-cell;#position:absolute;#top:50%;#left:0;vertical-align:middle;padding: 8px;}
		.video-plh-text {#position:relative;#top:-50%;text-align:center;line-height:35px;}
		.video-plh-sub {}
		.video-plh-full {font-size:28px;}
		.video-plh-full .video-plh-sub {font-size:14px;}
		.video-plh-thumb {font-size:18px;}
		.video-plh-thumb .video-plh-sub {font-size:12px;}
		.video-plh-sub {line-height: 120%; margin-top:1em;}
		.video-plh-error {color:#f2643d;}
	</style>
STYLE;
	}
	$res .= <<<BODY
	<div class="video-plh $class">
		<div class="video-plh-notice" style='width:{$width}px;height:{$height}px;'>
			<div class="video-plh-mid" style='width:{$mid_width}px;'>
				<div class="video-plh-text">
					$text
					<div class="video-plh-sub">$subtext</div>
				</div>
			</div>
		</div>
	</div>
BODY;
	return $res;
}

?>
