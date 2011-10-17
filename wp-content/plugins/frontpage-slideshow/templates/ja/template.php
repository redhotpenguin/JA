<?php
/*
Plugin Name: Frontpage-Slideshow
Plugin URI: http://wordpress.org/extend/plugins/frontpage-slideshow/
File description: This is the default template for the slider.
Author: Jean-François VIAL
Author URI: http://www.modulaweb.fr/
*/
/*  Copyright 2009 Jean-François VIAL  (email : jeff@modulaweb.fr)
 
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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
/*
How to create your own template:
1) copy/paste this file into another folder in the template folder.
2) modify thos 2 functions to feet your needs.
3) choose this new template to use it into the plugin's admin page

Do not hesitate to post your creation ! It could be included to the plugin and
make other people happy !
Enjoy !
*/
function frontpageSlideshow_TPL($fsentries) {
	// this is the HTML part
	$fscontent = '<!-- Template: default v0.9.9.3.1 -->
<div id="fs-main">
	<div id="fs-slide">
		<div id="fs-picture">
			<div id="fs-placeholder">
				<a id="fs-prev" href="#frontpage-slideshow-prev">&nbsp</a>
				<a id="fs-link" href="#frontpage-slideshow">&nbsp;</a>
				<a id="fs-next" href="#frontpage-slideshow-next">&nbsp</a>
			</div>
			<div id="fs-text">
				<div id="fs-title">&nbsp;</div>
				<div id="fs-excerpt">&nbsp;</div>
			</div>
		</div>
	</div>
	<ul>';
	foreach ($fsentries as $id=>$entry) {
		$fscontent .= '<li id="fs-entry-'.$id.'" class="fs-entry">';
		$fscontent .= '<div id="fs-entry-title-'.$id.'" class="fs-title">'.str_replace('&nbsp;','',$entry['title']).'</div>';
		$fscontent .= '<div id="fs-entry-button-comment-'.$id.'" class="fs-comment">'.$entry['button-comment'].'</div>';
		$fscontent .= '<img id="fs-entry-img-'.$id.'" class="fs-skip fs-img" alt=" " src="'.$entry['image'].'" />';
		$fscontent .= '<span id="fs-entry-comment-'.$id.'" class="fs-skip">'.$entry['comment'].'</span>';
		$fscontent .= '<span id="fs-entry-link-'.$id.'" class="fs-skip">'.$entry['link'].'</span>';
		$fscontent .= '</li>';
	}
	$fscontent .= '	</ul>
</div>';
	return $fscontent;
}

function frontpageSlideshow_JS($options,$fslast) {
	// this is the Javascript part.
	ob_start();
?>
var fslast = <?php echo $fslast?>;
var fsid = -1; 
var fsinterval = 0;
var clicked = false;
var hovered = false;
var initialized = false;
function fsChangeSlide(id) {
	if (!clicked) {
		clicked = true;
		window.clearInterval(fsinterval);
		jQuery("#fs-entry-"+fsid).removeClass("fs-current");
		fsid=id;
		window.clearInterval(fsinterval);
		<?php echo frontpageSlideshow_JS_effect($options['values']['fs_transition']); ?>
		
	}
}
function fsChangeSlide2() {
	jQuery('#fs-picture').css({backgroundImage : "url("+jQuery("#fs-entry-img-"+fsid).attr("src")+")"});
	if (jQuery('#fs-entry-link-'+fsid).text() != '')
		jQuery('#fs-title').html('<a href="' + jQuery('#fs-entry-link-'+fsid).text() + '">' + jQuery('#fs-entry-title-'+fsid).html() + '</a>');
	else
		jQuery('#fs-title').html(jQuery('#fs-entry-title-'+fsid).html());
	jQuery('#fs-excerpt').html(jQuery('#fs-entry-comment-'+fsid).html());
	<?php echo frontpageSlideshow_JS_effect($options['values']['fs_transition_on'],'In'); ?>
	
	jQuery("#fs-entry-"+fsid).addClass('fs-current');
	frontpageSlideshow();
}
function fsDoSlide() {
	jQuery("#fs-slide").css({width : jQuery("#fs-slide").css('width')});
	
	if (fsid>-1) jQuery("#fs-entry-"+fsid).removeClass("fs-current");
	fsid++;
	if (fsid>fslast) fsid = 0; // new loop !
	fsChangeSlide(fsid);
}
function frontpageSlideshow() {
	if (!initialized)
		jQuery('#fs-link').click(function() {
			if (fsid>-1 && jQuery('#fs-entry-link-'+fsid).text() != '')
				jQuery(this).attr('href',jQuery('#fs-entry-link-'+fsid).text());
		});
	window.clearInterval(fsinterval);
	fsinterval = window.setInterval('fsDoSlide()',<?php echo $options['values']['fs_pause_duration'];?>);
	clicked = false;
}
function fsPrevNext(nextprev) {
	var id = fsid + nextprev;
	if (id==-1) id = fslast;
	if (id>fslast) id = 0;
	fsChangeSlide(id);
}
jQuery('#fs-prev').click(function() {fsPrevNext(-1); return false;});
jQuery('#fs-next').click(function() {fsPrevNext(1); return false;});
jQuery('#fs-prev,#fs-next').hover(
	function(){jQuery(this).stop(true, true).fadeTo('fast',0.6);},
	function(){jQuery(this).stop(true, true).fadeTo('fast',0.15);}
);
jQuery('.fs-img').last().load(function() {fsDoSlide()});
<?php
for ($i=0;$i<=$fslast;$i++)
	echo "jQuery('#fs-entry-{$i}').click(function() {fsChangeSlide({$i})});\n";
?>
<?php 
	$js = ob_get_contents();
	define('FS_JS',$js);
	ob_end_clean();
}

function frontpageSlideshow_CSS($options,$fslast) {
	//Here comes the CSS ruleset
	ob_start(); ?>
#fs-main {
	width: <?php echo $options['values']['fs_main_width']?>!important;
	height: <?php echo $options['values']['fs_main_height']?>!important;
	border: 1px solid <?php echo $options['values']['fs_main_border_color']?>;
<?php
if ($options['values']['fs_rounded']) {
?>
	-moz-border-radius: 5px;
	-khtml-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
<?php
}
?>
	overflow: hidden;
	background: <?php echo $options['values']['fs_main_color']?> <?php 
				if ($options['values']['fs_main_background_image'] != '' && $options['values']['fs_main_background_image'] != 'none') {
					$url = $options['values']['fs_main_background_image'];
					(is_ssl()) ? $url = str_replace('http://','https://',$url) : $url = str_replace('https://','http://',$url); echo 'url('.$url.')';
				} else {
					echo 'none';
				}
			  ?> repeat scroll center center!important;
	color: <?php echo $options['values']['fs_font_color']?>;
	font-family: Verdana, Sans, Helvetica, Arial, sans-serif!important;
	text-align: left;
}

#fs-slide {
	float: <?php  if ($options['values']['fs_buttons_position']=='right') echo 'left'; else echo 'right'; ?>;
	width: <?php  if ($options['values']['fs_show_buttons']) echo $options['values']['fs_slide_width']; else echo '100%'; ?>;
	height: 100%;
<?php
if ($options['values']['fs_rounded']) {
?>
	-moz-border-radius: 5px;
	-khtml-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
<?php
}
?>}
#fs-picture {
	width: 100%;
	height: 100%;
	background-position: center center;
	background-repeat: no-repeat;
	background-image: url(<?php
					if ($options['values']['fs_loader_image'] != '') {
						$url = $options['values']['fs_loader_image'];
					} else {
						$url = get_bloginfo('url').'/wp-content/plugins/frontpage-slideshow/images/loading_black.gif';
					}
					(is_ssl()) ? $url = str_replace('http://','https://',$url) : $url = str_replace('https://','http://',$url); echo $url ?>);
<?php
if ($options['values']['fs_rounded']) {
?>
	-moz-border-radius: 5px;
	-khtml-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
<?php
}
?>}
#fs-placeholder {
	height: <?php echo $options['values']['fs_placeholder_height']?>;
}
#fs-link {
	display: block;
	float: left;
	height: 100%;
	width: <?php if($options['values']['fs_show_prevnext_buttons']){?>60<?php } else {?>100<?php }?>%;
	text-decoration: none;
	color: transparent;
	border: none;
}
#fs-prev , #fs-next {
	display: block;
	height: 100%;
	min-width: <?php if($options['values']['fs_show_prevnext_buttons']){?>50px<?php } else {?>0<?php }?>;
	width: <?php if($options['values']['fs_show_prevnext_buttons']){?>20%<?php } else {?>0<?php }?>;
	text-decoration: none;
	color: transparent;
	background-color: transparent;
	background-repeat: no-repeat;
}
#fs-prev {
	float: left;
	background-image: url(<?php
					if ($options['values']['fs_previous_image'] != '') {
						$url = $options['values']['fs_previous_image'];
					} else {
						$url = get_bloginfo('url').'/wp-content/plugins/frontpage-slideshow/images/prev.png';
					}
					(is_ssl()) ? $url = str_replace('http://','https://',$url) : $url = str_replace('https://','http://',$url); echo $url ?>);
	background-position: left center;
	opacity: 0.15;
}
#fs-next {
	float: right;
	background-image: url(<?php
					if ($options['values']['fs_next_image'] != '') {
						$url = $options['values']['fs_next_image'];
					} else {
						$url = get_bloginfo('url').'/wp-content/plugins/frontpage-slideshow/images/next.png';
					}
					(is_ssl()) ? $url = str_replace('http://','https://',$url) : $url = str_replace('https://','http://',$url); echo $url ?>);
	background-position: right center;
	opacity: 0.15;
}
/*#fs-prev:hover , #fs-next:hover {
	opacity: 0.6;
}*/

#fs-placeholder a:hover {
	text-decoration: none;
}
#fs-text {
<?php if ($options['values']['fs_show_comment']) {?>
	opacity: <?php  echo intval(str_replace('%','',$options['values']['fs_text_opacity'])) / 100; ?>;
	background-color: <?php echo $options['values']['fs_text_bgcolor']?>;
	/*margin-top: 10px;*/
	padding: 10px;
<?php } else { ?>
	display: none;
<?php } ?>
}
#fs-text a {
	color: #c0e7f8;
	text-decoration: underline;
}
#fs-text a:visited {
	color: #99fbac;
	text-decoration: underline;
}
#fs-title {
	font-weight: bold;
	font-size: 14px!important;
	line-height: 1.1em;
	margin-bottom: 0.25em;
	font-family: Verdana, Sans, Helvetica, Arial, sans-serif!important;
}
#fs-title a , #fs-title a:visited {
	color: <?php echo $options['values']['fs_font_color']?>;
	text-decoration: none;
}
#fs-title a:hover {
	text-decoration: underline;
}
.fs-title {
	font-weight: bold;
	font-size: 11px!important;
	line-height: 1.4em;
	margin: 0!important;
	padding: 5px 5px 0!important;
	margin-bottom: 0.25em;
	font-family: Verdana, Sans, Helvetica, Arial, sans-serif!important;
}
#fs-excerpt {
	font-size: 14px!important;
	padding-left: 10px;
	line-height: 1.4em;
}
.fs-comment {
	font-size: 8px!important;
	line-height: 1.2em;
	font-family: Verdana, Sans, Helvetica, Arial, sans-serif!important;
	padding: 0 5px 5px!important;
}
#fs-main ul {
	display: block;
	float: <?php echo $options['values']['fs_buttons_position']?>!important;
	clear: none!important;
	margin: 0!important;
	padding: 0!important;
	width: <?php  if ($options['values']['fs_slide_width']=='100%' || !$options['values']['fs_show_buttons']) echo '0'; else echo $options['values']['fs_buttons_width']?>!important;
	height: 100%;
	list-style: none!important;
	background: <?php echo $options['values']['fs_ul_background_color']?> <?php 
				if ($options['values']['fs_ul_background_image'] != '' && $options['values']['fs_ul_background_image'] != 'none') {
					$url = $options['values']['fs_ul_background_image'];
					(is_ssl()) ? $url = str_replace('http://','https://',$url) : $url = str_replace('https://','http://',$url); echo 'url('.$url.')';
				} else {
					echo 'none';
				}
			  ?> repeat scroll center center!important;
<?php
if ($options['values']['fs_rounded']) {
?>
	-moz-border-radius: 5px;
	-khtml-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
<?php
}
?>	z-index: 99999;
}
#fs-main li {
	display: block!important;
	padding: 0!important;
	margin: 0!important;
	width: 100%!important;
	height: <?php
	// auto calculate the height of buttons
	$number = str_replace('px','',str_replace('%','',$fslast));
	$height = str_replace('px','',str_replace('%','',trim($options['values']['fs_main_height'])));
	
	$h = floor($height/$number);
	
	echo $h.'px';
	?>!important;
<?php
if ($options['values']['fs_rounded']) {
?>
	-moz-border-radius: 3px;
	-khtml-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
<?php
}
?>
	cursor: pointer;
}
#fs-main li:before { content:""; }
#fs-main li:after { content:""; }

.fs-entry {
	background: <?php echo $options['values']['fs_button_normal_color']?> <?php 
				if ($options['values']['fs_button_background_image'] != '' && $options['values']['fs_button_background_image'] != 'none') {
					$url = $options['values']['fs_button_background_image'];
					(is_ssl()) ? $url = str_replace('http://','https://',$url) : $url = str_replace('https://','http://',$url); echo 'url('.$url.')';
				} else {
					echo 'none';
				}
			  ?> repeat scroll center center!important;
	margin: 0!important;
	overflow: hidden!important;
}
.fs-entry:hover {
	background: <?php echo $options['values']['fs_button_hover_color']?> <?php 
				if ($options['values']['fs_button_hover_background_image'] != '' && $options['values']['fs_button_hover_background_image'] != 'none') {
					$url = $options['values']['fs_button_hover_background_image'];
					(is_ssl()) ? $url = str_replace('http://','https://',$url) : $url = str_replace('https://','http://',$url); echo 'url('.$url.')';
				} else {
					echo 'none';
				}
			  ?> repeat scroll center center!important;
}
.fs-current {
	background: <?php echo $options['values']['fs_button_current_color']?> <?php 
				if ($options['values']['fs_current_button_background_image'] != '' && $options['values']['fs_current_button_background_image'] != 'none') {
					$url = $options['values']['fs_current_button_background_image'];
					(is_ssl()) ? $url = str_replace('http://','https://',$url) : $url = str_replace('https://','http://',$url); echo 'url('.$url.')';
				} else {
					echo 'none';
				}
			  ?> repeat scroll center center!important;
}
.fs-skip {
	position: absolute!important;
	top: -300000px!important;
	width: 0!important;
	height: 0!important;
}
<?php
	$css = ob_get_contents();
	define('FS_CSS',$css);
	ob_end_clean();
}

?>
