<?php // Hook for adding admin menus
if ( is_admin() ){ // admin actions
  add_action('admin_menu', 'smooth_slider_settings');
  add_action( 'admin_init', 'register_mysettings' ); 
} 

function smooth_slider_admin_scripts() {
  if ( is_admin() ){ // admin actions
  // Settings page only
	if ( isset($_GET['page']) && ('smooth-slider' == $_GET['page'] or 'smooth-slider-settings' == $_GET['page'] )  ) {
	wp_register_script('jquery', false, false, false, false);
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'stepcarousel', smooth_slider_plugin_url( 'js/stepcarousel.js' ),
		array('jquery'), SMOOTH_SLIDER_VER, false); 
	wp_enqueue_style( 'smooth_slider_css', smooth_slider_plugin_url( 'css/smooth-slider.css' ),
		false, SMOOTH_SLIDER_VER, 'all');
	}
  }
}

add_action( 'admin_init', 'smooth_slider_admin_scripts' );

function smooth_slider_admin_head() {
global $smooth_slider;
if ( is_admin() ){ // admin actions
   
  // Sliders page only
    if ( isset($_GET['page']) && 'smooth-slider' == $_GET['page'] ) {
	  $sliders = ss_get_sliders(); 
	?>
		<script type="text/javascript">
            // <![CDATA[
        jQuery(document).ready(function() {
                jQuery(function() {
                    jQuery("#slider_tabs").tabs(); 
				<?php foreach($sliders as $slider){?>
                    jQuery("#sslider_sortable_<?php echo $slider['slider_id'];?>").sortable();
                    jQuery("#sslider_sortable_<?php echo $slider['slider_id'];?>").disableSelection();
			    <?php } ?>
                });
        });
        function confirmRemove()
        {
            var agree=confirm("This will remove selected Posts/Pages from Slider.");
            if (agree)
            return true ;
            else
            return false ;
        }
        function confirmRemoveAll()
        {
            var agree=confirm("Remove all Posts/Pages from Smooth Slider??");
            if (agree)
            return true ;
            else
            return false ;
        }
        function confirmSliderDelete()
        {
            var agree=confirm("Delete this Slider??");
            if (agree)
            return true ;
            else
            return false ;
        }
        function slider_checkform ( form )
        {
          if (form.new_slider_name.value == "") {
            alert( "Please enter the New Slider name." );
            form.new_slider_name.focus();
            return false ;
          }
          return true ;
        }
        </script>
        <style type="text/css">
        /************************************************
        *	ui-tabs  									*
        ************************************************/
        .ui-tabs { padding: .2em; zoom: 1; }
        .ui-tabs .ui-tabs-nav { list-style: none; position: relative; padding: .2em .2em 0; }
        .ui-tabs .ui-tabs-nav li { position: relative; float: left; border-bottom-width: 0 !important; margin: 0 .2em -1px 0; padding: 0;  background-color:#B9B9B9;}
        .ui-tabs .ui-tabs-nav li a { float: left; text-decoration: none; padding: .5em 1em; color:#FFFFFF;}
        .ui-tabs .ui-tabs-nav li.ui-tabs-selected { border-bottom-width: 0; background-color:#ABD37E;}
        .ui-tabs .ui-tabs-nav li.ui-tabs-selected a, .ui-tabs .ui-tabs-nav li.ui-state-disabled a, .ui-tabs .ui-tabs-nav li.ui-state-processing a { cursor: text; color:#FFF;}
        .ui-tabs .ui-tabs-nav li a, .ui-tabs.ui-tabs-collapsible .ui-tabs-nav li.ui-tabs-selected a { cursor: pointer; } /* first selector in group seems obsolete, but required to overcome bug in Opera applying cursor: text overall if defined elsewhere... */
        .ui-tabs .ui-tabs-panel { padding: 1em 1.4em; display: block; border-width: 0; background: none; }
        .ui-tabs .ui-tabs-hide { display: none !important; }
        /*tabs complete*/
        #divFeedityWidget span[style] {
                display:none !important;
        }
        div#smooth_sldr_donations a{
           color:#366C94 !important;
           text-decoration:none;
        }
        div#smooth_sldr_donations a:hover{
           text-decoration:underline;
        }
        #sldr_message {background-color:#FEF7DA;clear:both;width:72%;}
        #sldr_close {float:right;} 
        </style>
<?php
   } //Sliders page only
   
   // Settings page only
  if ( isset($_GET['page']) && 'smooth-slider-settings' == $_GET['page']  ) {
		wp_print_scripts( 'farbtastic' );
		wp_print_styles( 'farbtastic' );
?>
<script type="text/javascript">
	// <![CDATA[
jQuery(document).ready(function() {
		jQuery('#colorbox_1').farbtastic('#color_value_1');
		jQuery('#color_picker_1').click(function () {
           if (jQuery('#colorbox_1').css('display') == "block") {
		      jQuery('#colorbox_1').fadeOut("slow"); }
		   else {
		      jQuery('#colorbox_1').fadeIn("slow"); }
        });
		var colorpick_1 = false;
		jQuery(document).mousedown(function(){
		    if (colorpick_1 == true) {
    			return; }
				jQuery('#colorbox_1').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
		    colorpick_1 = false;
		});
//for second color box
		jQuery('#colorbox_2').farbtastic('#color_value_2');
		jQuery('#color_picker_2').click(function () {
           if (jQuery('#colorbox_2').css('display') == "block") {
		      jQuery('#colorbox_2').fadeOut("slow"); }
		   else {
		      jQuery('#colorbox_2').fadeIn("slow"); }
        });
		var colorpick_2 = false;
		jQuery(document).mousedown(function(){
		    if (colorpick_2 == true) {
    			return; }
				jQuery('#colorbox_2').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
		    colorpick_2 = false;
		});
//for third color box
		jQuery('#colorbox_3').farbtastic('#color_value_3');
		jQuery('#color_picker_3').click(function () {
           if (jQuery('#colorbox_3').css('display') == "block") {
		      jQuery('#colorbox_3').fadeOut("slow"); }
		   else {
		      jQuery('#colorbox_3').fadeIn("slow"); }
        });
		var colorpick_3 = false;
		jQuery(document).mousedown(function(){
		    if (colorpick_3 == true) {
    			return; }
				jQuery('#colorbox_3').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
		    colorpick_3 = false;
		});
//for fourth color box
		jQuery('#colorbox_4').farbtastic('#color_value_4');
		jQuery('#color_picker_4').click(function () {
           if (jQuery('#colorbox_4').css('display') == "block") {
		      jQuery('#colorbox_4').fadeOut("slow"); }
		   else {
		      jQuery('#colorbox_4').fadeIn("slow"); }
        });
		var colorpick_4 = false;
		jQuery(document).mousedown(function(){
		    if (colorpick_4 == true) {
    			return; }
				jQuery('#colorbox_4').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
		    colorpick_4 = false;
		});
//for fifth color box
		jQuery('#colorbox_5').farbtastic('#color_value_5');
		jQuery('#color_picker_5').click(function () {
           if (jQuery('#colorbox_5').css('display') == "block") {
		      jQuery('#colorbox_5').fadeOut("slow"); }
		   else {
		      jQuery('#colorbox_5').fadeIn("slow"); }
        });
		var colorpick_5 = false;
		jQuery(document).mousedown(function(){
		    if (colorpick_5 == true) {
    			return; }
				jQuery('#colorbox_5').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
		    colorpick_5 = false;
		});
//for sixth color box
		jQuery('#colorbox_6').farbtastic('#color_value_6');
		jQuery('#color_picker_6').click(function () {
           if (jQuery('#colorbox_6').css('display') == "block") {
		      jQuery('#colorbox_6').fadeOut("slow"); }
		   else {
		      jQuery('#colorbox_6').fadeIn("slow"); }
        });
		var colorpick_6 = false;
		jQuery(document).mousedown(function(){
		    if (colorpick_6 == true) {
    			return; }
				jQuery('#colorbox_6').fadeOut("slow");
		});
		jQuery(document).mouseup(function(){
		    colorpick_6 = false;
		});
		jQuery('#sldr_close').click(function () {
			jQuery('#sldr_message').fadeOut("slow");
		});
});
</script>
<style type="text/css">
.color-picker-wrap {
		position: absolute;
 		display: none; 
		background: #fff;
		border: 3px solid #ccc;
		padding: 3px;
		z-index: 1000;
	}
#divFeedityWidget span[style] {
        display:none !important;
}
div#smooth_sldr_donations a{
   color:#366C94 !important;
   text-decoration:none;
}
div#smooth_sldr_donations a:hover{
   text-decoration:underline;
}
#sldr_message {background-color:#FEF7DA;clear:both;width:72%;}
#sldr_close {float:right;} 
</style>
<style type="text/css" media="screen">#smooth_sldr{width:<?php echo $smooth_slider['width']; ?>px;height:<?php echo $smooth_slider['height']; ?>px;background-color:<?php if ($smooth_slider['bg'] == '1') { echo "transparent";} else { echo $smooth_slider['bg_color']; } ?>;border:<?php echo $smooth_slider['border']; ?>px solid <?php echo $smooth_slider['brcolor']; ?>;}#smooth_sldr_items{padding:10px <?php if ($smooth_slider['prev_next'] == 1) {echo "18";} else {echo "12";} ?>px 0px <?php if ($smooth_slider['prev_next'] == 1) {echo "26";} else {echo "12";} ?>px;}#smooth_sliderc{width:<?php if ($smooth_slider['prev_next'] == 1) {echo ($smooth_slider['width'] - 44);} else {echo ($smooth_slider['width'] - 24);} ?>px;height:<?php if ($smooth_slider['goto_slide'] == "1"){$nav_size = $smooth_slider['content_fsize'];} elseif ($smooth_slider['goto_slide'] == "2"){$nav_size = $smooth_slider['navimg_ht'];} else {$nav_size = 10;} $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { $extra_height = $smooth_slider['title_fsize'] + $nav_size + 5 + 18; } else { $extra_height = $nav_size + 5 + 5 + 18;  } echo ($smooth_slider['height'] - $extra_height); ?>px;}.smooth_slideri{width:<?php if ($smooth_slider['prev_next'] == 1) {echo ($smooth_slider['width'] - 54);} else {echo ($smooth_slider['width'] - 24);} ?>px;height:<?php if ($smooth_slider['goto_slide'] == "1"){$nav_size = $smooth_slider['content_fsize'];} elseif ($smooth_slider['goto_slide'] == "2"){$nav_size = $smooth_slider['navimg_ht'];} else {$nav_size = 10;} $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { $extra_height = $smooth_slider['title_fsize'] + $nav_size + 5 + 18; } else { $extra_height = $nav_size + 5 + 5 + 18;  } echo ($smooth_slider['height'] - $extra_height); ?>px;}.sldr_title{font-family:<?php echo $smooth_slider['title_font']; ?>, Arial, Helvetica, sans-serif;font-size:<?php echo $smooth_slider['title_fsize']; ?>px;font-weight:<?php if ($smooth_slider['title_fstyle'] == "bold" or $smooth_slider['title_fstyle'] == "bold italic" ){echo "bold";} else { echo "normal"; } ?>;font-style:<?php if ($smooth_slider['title_fstyle'] == "italic" or $smooth_slider['title_fstyle'] == "bold italic" ){echo "italic";} else {echo "normal";} ?>;color:<?php echo $smooth_slider['title_fcolor']; ?>;}#smooth_sldr_body h2{line-height:<?php echo ($smooth_slider['ptitle_fsize'] + 3); ?>px;font-family:<?php echo $smooth_slider['ptitle_font']; ?>, Arial, Helvetica, sans-serif;font-size:<?php echo $smooth_slider['ptitle_fsize']; ?>px;font-weight:<?php if ($smooth_slider['ptitle_fstyle'] == "bold" or $smooth_slider['ptitle_fstyle'] == "bold italic" ){echo "bold";} else {echo "normal";} ?>;font-style:<?php if ($smooth_slider['ptitle_fstyle'] == "italic" or $smooth_slider['ptitle_fstyle'] == "bold italic"){echo "italic";} else {echo "normal";} ?>;color:<?php echo $smooth_slider['ptitle_fcolor']; ?>;margin:<?php $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { echo "10"; } else {echo "0";} ?>px 0 5px 0;}#smooth_sldr_body h2 a{color:<?php echo $smooth_slider['ptitle_fcolor']; ?>;}#smooth_sldr_body span{font-family:<?php echo $smooth_slider['content_font']; ?>, Arial, Helvetica, sans-serif;font-size:<?php echo $smooth_slider['content_fsize']; ?>px;font-weight:<?php if ($smooth_slider['content_fstyle'] == "bold" or $smooth_slider['content_fstyle'] == "bold italic" ){echo "bold";} else {echo "normal";} ?>;font-style:<?php if ($smooth_slider['content_fstyle']=="italic" or $smooth_slider['content_fstyle'] == "bold italic"){echo "italic";} else {echo "normal";} ?>;color:<?php echo $smooth_slider['content_fcolor']; ?>;}.smooth_slider_thumbnail{float:<?php echo $smooth_slider['img_align']; ?>;margin:<?php $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { echo "10"; } else {echo "0";} ?>px <?php if($smooth_slider['img_align'] == "left") {echo "5";} else {echo "0";} ?>px 0 <?php if($smooth_slider['img_align'] == "right") {echo "5";} else {echo "0";} ?>px;max-height:<?php echo $smooth_slider['img_height']; ?>px;border:<?php echo $smooth_slider['img_border']; ?>px solid <?php echo $smooth_slider['img_brcolor']; ?>;}#smooth_sldr_body p.more a{color:<?php echo $smooth_slider['ptitle_fcolor']; ?>;font-family:<?php echo $smooth_slider['content_font']; ?>, Arial, Helvetica, sans-serif;font-size:<?php echo $smooth_slider['content_fsize']; ?>px;}#smooth_sliderc_nav li{border:1px solid <?php echo $smooth_slider['content_fcolor']; ?>;font-size:<?php echo $smooth_slider['content_fsize']; ?>px;font-family:<?php echo $smooth_slider['content_font']; ?>, Arial, Helvetica, sans-serif;}#smooth_sliderc_nav li a{color:<?php echo $smooth_slider['ptitle_fcolor']; ?>;}.sldrlink{padding-right:<?php if ($smooth_slider['prev_next'] == 1) {echo "40";} else {echo "25";} ?>px;}.sldrlink a{color:<?php echo $smooth_slider['content_fcolor']; ?>;}</style>
<?php
   } //for smooth slider option page
 }//only for admin
}

add_action('admin_head', 'smooth_slider_admin_head');

// function for adding settings page to wp-admin
function smooth_slider_settings() {
    // Add a new submenu under Options:
  //  add_options_page('Smooth Slider', 'Smooth Slider', 9, basename(__FILE__), 'smooth_slider_settings_page');
	add_menu_page( 'Smooth Slider', 'Smooth Slider', 'manage_options','smooth-slider', 'smooth_slider_create_multiple_sliders', smooth_slider_plugin_url( 'images/smooth_slider_icon.gif' ) );
	add_submenu_page('smooth-slider', 'Smooth Sliders', 'Sliders', 'manage_options', 'smooth-slider', 'smooth_slider_create_multiple_sliders');
	add_submenu_page('smooth-slider', 'Smooth Slider Settings', 'Settings', 'manage_options', 'smooth-slider-settings', 'smooth_slider_settings_page');
}
include('sliders.php');
// This function displays the page content for the Smooth Slider Options submenu
function smooth_slider_settings_page() {
global $smooth_slider;
?>

<div class="wrap" style="clear:both;">

<div id="poststuff" class="metabox-holder has-right-sidebar" style="float:right;width:30%;"> 
   <div id="side-info-column" class="inner-sidebar"> 
			<div class="postbox"> 
			  <h3 class="hndle"><span>About this Plugin:</span></h3> 
			  <div class="inside">
                <ul>
                <li><a href="http://www.clickonf5.org/smooth-slider" title="Smooth Slider Homepage" >Plugin Homepage</a></li>
                <li><a href="http://www.clickonf5.org" title="Visit Internet Techies" >Plugin Parent Site</a></li>
                <li><a href="http://www.clickonf5.org/phpbb/smooth-slider-f12/" title="Support Forum for Smooth Slider" >Support Forum</a></li>
                <li><a href="http://www.clickonf5.org/about/tejaswini" title="Smooth Slider Author Page" >About the Author</a></li>
                <li><a href="http://www.clickonf5.org/go/smooth-slider/" title="Donate if you liked the plugin and support in enhancing Smooth Slider and creating new plugins" >Donate with Paypal</a></li>
                </ul> 
              </div> 
			</div> 
     </div>
     
   <div id="side-info-column" class="inner-sidebar"> 
			<div class="postbox"> 
			  <h3 class="hndle"><span></span>Our Facebook Fan Page</h3> 
			  <div class="inside">
                <script type="text/javascript" src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php/en_GB"></script><script type="text/javascript">FB.init("2aeebe9fb014836a6810ec4426d26f7e");</script><fb:fan profile_id="127760528543" stream="" connections="8" width="270" height="250"></fb:fan>
              </div> 
			</div> 
     </div>

     <div id="side-info-column" class="inner-sidebar"> 
			<div class="postbox"> 
			  <h3 class="hndle"><span>Credits:</span></h3> 
			  <div class="inside">
                <ul>
                <li><a href="http://www.dynamicdrive.com" title="Step Carousel jQuery plugin by Dynamic Drive" >Step Carousel Viewer</a></li>
                <li><a href="http://www.bioxd.com/featureme" title="FeatureMe Wordpress Plugin by Oscar Alcalá" >FeatureMe Wordpress Plugin</a></li>
                <li><a href="http://acko.net/dev/farbtastic" title="Farbtastic Color Picker by Steven Wittens" >Farbtastic Color Picker</a></li>
                <li><a href="http://code.google.com/p/timthumb/" title="TimThumb script by Tim McDaniels and Darren Hoyt with tweaks by Ben Gillbanks" >TimThumb script</a></li>
                <li><a href="http://jquery.com/" title="jQuery JavaScript Library - John Resig" >jQuery JavaScript Library</a></li>
                </ul> 
              </div> 
			</div> 
     </div>
     
          <div id="side-info-column" class="inner-sidebar"> 
			<div class="postbox"> 
			  <h3 class="hndle"><span>Support &amp; Donations</span></h3> 
			  <div class="inside">
                <div id="smooth_sldr_donations">
                 <ul>
                    <li><a href="http://malamedconsulting.com/" target="_blank">Connie Malamed - $25</a></li>
                    <li><a href="http://www.jacobwiechman.com/wordpress/" target="_blank">Jacob Wiechman - $30</a></li>
                    <li><a href="http://www.whatsthebigidea.com/" target="_blank">WhatsTheBigIdea.com,Inc. - $20</a></li>
                    <li><a href="http://uwaterloo.ca/" target="_blank">Trevor Bain - $25</a></li>
                    <li><a href="http://thule-italia.com/wordpress/" target="_blank">Marco Linguardo - $10</a></li>
                    <li><a href="http://eircom.net" target="_blank">Paul Goode - $5</a></li>
                    <li><a href="http://www.windowsobserver.com/" target="_blank">Richard Hay - $10</a></li>
                    <li><a href="http://www.maximotimes.com/maximo/" target="_blank">Chonbury Neth - $10</a></li>
                    <li><a href="http://www.yobeat.com/" target="_blank">Brooke Geery - $10</a></li>
                 </ul>
					<script language="JavaScript" type="text/javascript">
                    <!--
                        // Customize the widget by editing the fields below
                        // All fields are required
                    
                        // Your Feedity RSS feed URL
                        feedity_widget_feed = "http://feedity.com/rss.aspx/clickonf5-org/UlVTUldR";
                    
                        // Number of items to display in the widget
                        feedity_widget_numberofitems = "10";
                    
                        // Show feed item published date (values: yes or no)
                        feedity_widget_showdate = "no";
                    
                        // Widget box width (in px, pt, em, or %)
                        feedity_widget_width = "220px";
                    
                        // Widget background color in hex or by name (eg: #ffffff or white)
                        feedity_widget_backcolor = "#ffffff";
                    
                        // Widget font/link color in hex or by name (eg: #000000 or black)
                        feedity_widget_fontcolor = "#000000";
                    //-->
                    </script>
                    <script language="JavaScript" type="text/javascript" src="http://feedity.com/js/widget.js"></script>
                </div>
              </div> 
			</div> 
     </div>  
 </div> <!--end of poststuff --> 

<h2 style="float:left;">Smooth Slider Settings </h2>
<form  style="float:left;" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="8046056">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<?php $url = sslider_admin_url( array( 'page' => 'smooth-slider' ) );?>
<a href="<?php echo $url; ?>" title="Go to Sliders page where you can re-order the slide posts, delete the slides from the slider etc.">Go to Sliders Admin</a>

<form method="post" action="options.php">
<h2 style="clear:left;">Preview</h2> 
<?php settings_fields('smooth-slider-group'); ?>
<div style="width:70%;">
<?php 
get_smooth_slider();
?> </div>

<h2>Slider Box</h2> 
<p>Customize the looks of the Slider box wrapping the complete slideshow from here</p> 

<div style="float:left;width:70%;">
<table class="form-table">

<tr valign="top">
<th scope="row">Slide Pause Interval</th>
<td><input type="text" name="smooth_slider_options[speed]" id="smooth_slider_speed" class="small-text" value="<?php echo $smooth_slider['speed']; ?>" />&nbsp;(in secs)</td>
</tr>

<tr valign="top">
<th scope="row">Slide Transition Speed</th>
<td><input type="text" name="smooth_slider_options[transition]" id="smooth_slider_transition" class="small-text" value="<?php echo $smooth_slider['transition']; ?>" />*100(in millisecs)-duration of the slide animation Lower value indicates faster<small style="color:#FF0000"> (IMP!! Enter numeric value > 0)</small></td>
</tr>

<tr valign="top">
<th scope="row"></th>
<td><label for="smooth_slider_autostep"> 
<input name="smooth_slider_options[autostep]" type="checkbox" id="smooth_slider_autostep" value="1" <?php checked("1", $smooth_slider['autostep']); ?> /> 
 Enable autostepping of slides</label></td>
</tr>

<tr valign="top">
<th scope="row">Number of Posts in the Slideshow</th>
<td><input type="text" name="smooth_slider_options[no_posts]" id="smooth_slider_no_posts" class="small-text" value="<?php echo $smooth_slider['no_posts']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Background Color</th>
<td><input type="text" name="smooth_slider_options[bg_color]" id="color_value_1" value="<?php echo $smooth_slider['bg_color']; ?>" />&nbsp; <img id="color_picker_1" src="<?php echo smooth_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="Pick the color of your choice" /><div class="color-picker-wrap" id="colorbox_1"></div> &nbsp; &nbsp; &nbsp; 
<label for="smooth_slider_bg"><input name="smooth_slider_options[bg]" type="checkbox" id="smooth_slider_bg" value="1" <?php checked('1', $smooth_slider['bg']); ?>  /> Use Transparent Background</label> </td>
</tr>
 
<tr valign="top">
<th scope="row">Slider Height</th>
<td><input type="text" name="smooth_slider_options[height]" id="smooth_slider_height" class="small-text" value="<?php echo $smooth_slider['height']; ?>" />&nbsp;px</td>
</tr>


<tr valign="top">
<th scope="row">Slider Width</th>
<td><input type="text" name="smooth_slider_options[width]" id="smooth_slider_width" class="small-text" value="<?php echo $smooth_slider['width']; ?>" />&nbsp;px</td>
</tr>

<tr valign="top">
<th scope="row">Border Thickness</th>
<td><input type="text" name="smooth_slider_options[border]" id="smooth_slider_border" class="small-text" value="<?php echo $smooth_slider['border']; ?>" />&nbsp;px &nbsp;(put 0 if no border is required)</td>
</tr>

<tr valign="top">
<th scope="row">Border Color</th>
<td><input type="text" name="smooth_slider_options[brcolor]" id="color_value_6" value="<?php echo $smooth_slider['brcolor']; ?>" />&nbsp; <img id="color_picker_6" src="<?php echo smooth_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="Pick the color of your choice" /><div class="color-picker-wrap" id="colorbox_6"></div></td>
</tr>

<tr valign="top"> 
<th scope="row">Navigation Buttons</th> 
<td><fieldset><legend class="screen-reader-text"><span>Navigation Buttons</span></legend> 
<label for="smooth_slider_prev_next"> 
<input name="smooth_slider_options[prev_next]" type="checkbox" id="smooth_slider_prev_next" value="1" <?php checked("1", $smooth_slider['prev_next']); ?> /> 
 Show Prev/Next navigation arrows</label><br /> 
<label for="smooth_slider_goto_slide">Show go to slide number links at the bottom as 1, 2, 3 etc. or images</label><br />
<input name="smooth_slider_options[goto_slide]" type="radio" id="smooth_slider_goto_slide" value="0" <?php checked('0', $smooth_slider['goto_slide']); ?>  /> None <br /> 
<input name="smooth_slider_options[goto_slide]" type="radio" id="smooth_slider_goto_slide" value="1" <?php checked('1', $smooth_slider['goto_slide']); ?>  /> Numbers <br /> 
<input name="smooth_slider_options[goto_slide]" type="radio" id="smooth_slider_goto_slide" value="2" <?php checked('2', $smooth_slider['goto_slide']); ?>  /> Custom Images for Navigation &nbsp; &nbsp;Width: <input type="text" name="smooth_slider_options[navimg_w]" id="smooth_slider_navimg_w" class="small-text" value="<?php echo $smooth_slider['navimg_w']; ?>" /> px &nbsp;Height: <input type="text" name="smooth_slider_options[navimg_ht]" id="smooth_slider_navimg_ht" class="small-text" value="<?php echo $smooth_slider['navimg_ht']; ?>" /> px<br /> 
<input name="smooth_slider_options[goto_slide]" type="radio" id="smooth_slider_goto_slide" value="3" <?php checked('3', $smooth_slider['goto_slide']); ?>  /> Enter Custom Text or HTML &nbsp; &nbsp; 
<input type="text" name="smooth_slider_options[custom_nav]" class="regular-text code" value="<?php echo htmlentities($smooth_slider['custom_nav'], ENT_QUOTES); ?>" />
</fieldset></td> 
</tr> 

</table>

<h2>Slider Title</h2> 
<p>Customize the looks of the main title of the Slideshow from here</p> 
<table class="form-table">

<tr valign="top">
<th scope="row">Text</th>
<td><input type="text" name="smooth_slider_options[title_text]" id="smooth_slider_title_text" value="<?php echo $smooth_slider['title_text']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Font</th>
<td><select name="smooth_slider_options[title_font]" id="smooth_slider_title_font" >
<option value="Arial" <?php if ($smooth_slider['title_font'] == "Arial"){ echo "selected";}?> >Arial</option>
<option value="Book Antiqua" <?php if ($smooth_slider['title_font'] == "Book Antiqua"){ echo "selected";}?> >Book Antiqua</option>
<option value="Bookman Old Style" <?php if ($smooth_slider['title_font'] == "Bookman Old Style"){ echo "selected";}?> >Bookman Old Style</option>
<option value="Calibri" <?php if ($smooth_slider['title_font'] == "Calibri"){ echo "selected";}?> >Calibri</option>
<option value="Century Schoolbook" <?php if ($smooth_slider['title_font'] == "Century Schoolbook"){ echo "selected";}?> >Century Schoolbook</option>
<option value="Courier New" <?php if ($smooth_slider['title_font'] == "Courier New"){ echo "selected";}?> >Courier New</option>
<option value="Geneva" <?php if ($smooth_slider['title_font'] == "Geneva"){ echo "selected";}?> >Geneva</option>
<option value="Georgia" <?php if ($smooth_slider['title_font'] == "Georgia"){ echo "selected";} ?> >Georgia</option>
<option value="Helvetica" <?php if ($smooth_slider['title_font'] == "Helvetica"){ echo "selected";}?> >Helvetica</option>
<option value="Monotype Corsiva" <?php if ($smooth_slider['title_font'] == "Monotype Corsiva"){ echo "selected";}?> >Monotype Corsiva</option>
<option value="Times New Roman" <?php if ($smooth_slider['title_font'] == "Times New Roman"){ echo "selected";}?> >Times New Roman</option>
<option value="Trebuchet MS" <?php if ($smooth_slider['title_font'] == "Trebuchet MS"){ echo "selected";}?> >Trebuchet MS</option>
<option value="Verdana" <?php if ($smooth_slider['title_font'] == "Verdana"){ echo "selected";}?> >Verdana</option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row">Font Color</th>
<td><input type="text" name="smooth_slider_options[title_fcolor]" id="color_value_2" value="<?php echo $smooth_slider['title_fcolor']; ?>" />&nbsp; <img id="color_picker_2" src="<?php echo smooth_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="Pick the color of your choice" /><div class="color-picker-wrap" id="colorbox_2"></div></td>
</tr>

<tr valign="top">
<th scope="row">Font Size</th>
<td><input type="text" name="smooth_slider_options[title_fsize]" id="smooth_slider_title_fsize" class="small-text" value="<?php echo $smooth_slider['title_fsize']; ?>" />&nbsp;px</td>
</tr>

<tr valign="top">
<th scope="row">Font Style</th>
<td><select name="smooth_slider_options[title_fstyle]" id="smooth_slider_title_fstyle" >
<option value="bold" <?php if ($smooth_slider['title_fstyle'] == "bold"){ echo "selected";}?> >Bold</option>
<option value="bold italic" <?php if ($smooth_slider['title_fstyle'] == "bold italic"){ echo "selected";}?> >Bold Italic</option>
<option value="italic" <?php if ($smooth_slider['title_fstyle'] == "italic"){ echo "selected";}?> >Italic</option>
<option value="normal" <?php if ($smooth_slider['title_fstyle'] == "normal"){ echo "selected";}?> >Normal</option>
</select>
</td>
</tr>
</table>

<h2>Post Title</h2> 
<p>Customize the looks of the title of each of the sliding post here</p> 
<table class="form-table">

<tr valign="top">
<th scope="row">Font</th>
<td><select name="smooth_slider_options[ptitle_font]" id="smooth_slider_ptitle_font" >
<option value="Arial" <?php if ($smooth_slider['ptitle_font'] == "Arial"){ echo "selected";}?> >Arial</option>
<option value="Book Antiqua" <?php if ($smooth_slider['ptitle_font'] == "Book Antiqua"){ echo "selected";}?> >Book Antiqua</option>
<option value="Bookman Old Style" <?php if ($smooth_slider['ptitle_font'] == "Bookman Old Style"){ echo "selected";}?> >Bookman Old Style</option>
<option value="Calibri" <?php if ($smooth_slider['ptitle_font'] == "Calibri"){ echo "selected";}?> >Calibri</option>
<option value="Century Schoolbook" <?php if ($smooth_slider['ptitle_font'] == "Century Schoolbook"){ echo "selected";}?> >Century Schoolbook</option>
<option value="Courier New" <?php if ($smooth_slider['ptitle_font'] == "Courier New"){ echo "selected";}?> >Courier New</option>
<option value="Geneva" <?php if ($smooth_slider['ptitle_font'] == "Geneva"){ echo "selected";}?> >Geneva</option>
<option value="Georgia" <?php if ($smooth_slider['ptitle_font'] == "Georgia"){ echo "selected";} ?> >Georgia</option>
<option value="Helvetica" <?php if ($smooth_slider['ptitle_font'] == "Helvetica"){ echo "selected";}?> >Helvetica</option>
<option value="Monotype Corsiva" <?php if ($smooth_slider['ptitle_font'] == "Monotype Corsiva"){ echo "selected";}?> >Monotype Corsiva</option>
<option value="Times New Roman" <?php if ($smooth_slider['ptitle_font'] == "Times New Roman"){ echo "selected";}?> >Times New Roman</option>
<option value="Trebuchet MS" <?php if ($smooth_slider['ptitle_font'] == "Trebuchet MS"){ echo "selected";}?> >Trebuchet MS</option>
<option value="Verdana" <?php if ($smooth_slider['ptitle_font'] == "Verdana"){ echo "selected";}?> >Verdana</option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row">Font Color</th>
<td><input type="text" name="smooth_slider_options[ptitle_fcolor]" id="color_value_3" value="<?php echo $smooth_slider['ptitle_fcolor']; ?>" />&nbsp; <img id="color_picker_3" src="<?php echo smooth_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="Pick the color of your choice" /><div class="color-picker-wrap" id="colorbox_3"></div></td>
</tr>

<tr valign="top">
<th scope="row">Font Size</th>
<td><input type="text" name="smooth_slider_options[ptitle_fsize]" id="smooth_slider_ptitle_fsize" class="small-text" value="<?php echo $smooth_slider['ptitle_fsize']; ?>" />&nbsp;px</td>
</tr>

<tr valign="top">
<th scope="row">Font Style</th>
<td><select name="smooth_slider_options[ptitle_fstyle]" id="smooth_slider_ptitle_fstyle" >
<option value="bold" <?php if ($smooth_slider['ptitle_fstyle'] == "bold"){ echo "selected";}?> >Bold</option>
<option value="bold italic" <?php if ($smooth_slider['ptitle_fstyle'] == "bold italic"){ echo "selected";}?> >Bold Italic</option>
<option value="italic" <?php if ($smooth_slider['ptitle_fstyle'] == "italic"){ echo "selected";}?> >Italic</option>
<option value="normal" <?php if ($smooth_slider['ptitle_fstyle'] == "normal"){ echo "selected";}?> >Normal</option>
</select>
</td>
</tr>
</table>

<h2>Thumbnail Image</h2> 
<p>Customize the looks of the thumbnail image for each of the sliding post here</p> 
<table class="form-table">

<tr valign="top"> 
<th scope="row">Image Pick Preferences <small>(The first one is having priority over second, the second having priority on third and so on)</small></th> 
<td><fieldset><legend class="screen-reader-text"><span>Image Pick Sequence <small>(The first one is having priority over second, the second having priority on third and so on)</small> </span></legend> 
<input name="smooth_slider_options[img_pick][0]" type="checkbox" value="1" <?php checked('1', $smooth_slider['img_pick'][0]); ?>  /> Use Custom Field/Key &nbsp; &nbsp; 
<input type="text" name="smooth_slider_options[img_pick][1]" class="text" value="<?php echo $smooth_slider['img_pick'][1]; ?>" /> Name of the Custom Field/Key
<br />
<input name="smooth_slider_options[img_pick][2]" type="checkbox" value="1" <?php checked('1', $smooth_slider['img_pick'][2]); ?>  /> Use Featured Post/Thumbnail (Wordpress 3.0 +  feature)&nbsp; <br />
<input name="smooth_slider_options[img_pick][3]" type="checkbox" value="1" <?php checked('1', $smooth_slider['img_pick'][3]); ?>  /> Consider Images attached to the post &nbsp; &nbsp; 
<input type="text" name="smooth_slider_options[img_pick][4]" class="small-text" value="<?php echo $smooth_slider['img_pick'][4]; ?>" /> Order of the Image attachment to pick &nbsp; <br />
<input name="smooth_slider_options[img_pick][5]" type="checkbox" value="1" <?php checked('1', $smooth_slider['img_pick'][5]); ?>  /> Scan images from the post, in case there is no attached image to the post&nbsp; 
</fieldset></td> 
</tr> 

<tr valign="top">
<th scope="row">Align to</th>
<td><select name="smooth_slider_options[img_align]" id="smooth_slider_img_align" >
<option value="left" <?php if ($smooth_slider['img_align'] == "left"){ echo "selected";}?> >Left</option>
<option value="right" <?php if ($smooth_slider['img_align'] == "right"){ echo "selected";}?> >Right</option>
<option value="none" <?php if ($smooth_slider['img_align'] == "none"){ echo "selected";}?> >Center</option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row">Wordpress Image Extract Size</th>
<td><select name="smooth_slider_options[crop]" id="smooth_slider_img_crop" >
<option value="0" <?php if ($smooth_slider['crop'] == "0"){ echo "selected";}?> >Full</option>
<option value="1" <?php if ($smooth_slider['crop'] == "1"){ echo "selected";}?> >Large</option>
<option value="2" <?php if ($smooth_slider['crop'] == "2"){ echo "selected";}?> >Medium</option>
<option value="3" <?php if ($smooth_slider['crop'] == "3"){ echo "selected";}?> >Thumbnail</option>
</select>
<small>This is for fast page load, in case you choose 'Custom Size' setting from below, you would not like to extract 'full' size image from the media library. In this case you can use, 'medium' or 'thumbnail' image. This is because, for every image upload to the media gallery WordPress creates four sizes of the same image. So you can choose which to load in the slider and then specify the actual size.</small>
</td>
</tr>


<tr valign="top"> 
<th scope="row">Image Size</th> 
<td><fieldset><legend class="screen-reader-text"><span>Image Size</span></legend> 
<input name="smooth_slider_options[img_size]" type="radio" value="0" <?php checked('0', $smooth_slider['img_size']); ?>  /> Original Size <small>(In this case, the size would be equal to the extracted image (full/large/medium/thumbnail) from the above settings</small><br />
<input name="smooth_slider_options[img_size]" type="radio" value="1" <?php checked('1', $smooth_slider['img_size']); ?>  /> Custom Size:&nbsp; 
<label for="smooth_slider_options[img_width]">Width</label>
<input type="text" name="smooth_slider_options[img_width]" class="small-text" value="<?php echo $smooth_slider['img_width']; ?>" />&nbsp;px &nbsp;&nbsp; 
<!--<label for="smooth_slider_options[img_height]">Maximum Height of the Image</label>
<input type="text" name="smooth_slider_options[img_height]" class="small-text" value="<?php echo $smooth_slider['img_height']; ?>" />&nbsp;px &nbsp;&nbsp; <br />-->
<!--<input name="smooth_slider_options[crop]" type="checkbox" value="1" <?php checked('1', $smooth_slider['crop']); ?>  /> Crop Images if Custom size is selected <small>(this uses timthumb and requires that the images should be in the same folder as of wordpress installation)</small>&nbsp;--> 
</fieldset></td> 
</tr> 

<tr valign="top">
<th scope="row">Maximum Height of the Image</th>
<td><input type="text" name="smooth_slider_options[img_height]" class="small-text" value="<?php echo $smooth_slider['img_height']; ?>" />&nbsp;px &nbsp;&nbsp; (This is necessary in order to keep the maximum image height in control)</td>
</tr>


<tr valign="top">
<th scope="row">Border Thickness</th>
<td><input type="text" name="smooth_slider_options[img_border]" id="smooth_slider_img_border" class="small-text" value="<?php echo $smooth_slider['img_border']; ?>" />&nbsp;px &nbsp;(put 0 if no border is required)</td>
</tr>

<tr valign="top">
<th scope="row">Border Color</th>
<td><input type="text" name="smooth_slider_options[img_brcolor]" id="color_value_4" value="<?php echo $smooth_slider['img_brcolor']; ?>" />&nbsp; <img id="color_picker_4" src="<?php echo smooth_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="Pick the color of your choice" /><div class="color-picker-wrap" id="colorbox_4"></div></td>
</tr>

<tr valign="top">
<th scope="row">Make pure Image Slider</th>
<td><input name="smooth_slider_options[image_only]" type="checkbox" value="1" <?php checked('1', $smooth_slider['image_only']); ?>  />&nbsp;(check this to convert Smooth Slider to Image Slider with no content)</td>
</tr>
</table>

<h2>Slider Content</h2> 
<p>Customize the looks of the content of each of the sliding post here</p> 
<table class="form-table">
<tr valign="top">
<th scope="row">Font</th>
<td><select name="smooth_slider_options[content_font]" id="smooth_slider_content_font" >
<option value="Arial" <?php if ($smooth_slider['content_font'] == "Arial"){ echo "selected";}?> >Arial</option>
<option value="Book Antiqua" <?php if ($smooth_slider['content_font'] == "Book Antiqua"){ echo "selected";}?> >Book Antiqua</option>
<option value="Bookman Old Style" <?php if ($smooth_slider['content_font'] == "Bookman Old Style"){ echo "selected";}?> >Bookman Old Style</option>
<option value="Calibri" <?php if ($smooth_slider['content_font'] == "Calibri"){ echo "selected";}?> >Calibri</option>
<option value="Century Schoolbook" <?php if ($smooth_slider['content_font'] == "Century Schoolbook"){ echo "selected";}?> >Century Schoolbook</option>
<option value="Courier New" <?php if ($smooth_slider['content_font'] == "Courier New"){ echo "selected";}?> >Courier New</option>
<option value="Geneva" <?php if ($smooth_slider['content_font'] == "Geneva"){ echo "selected";}?> >Geneva</option>
<option value="Georgia" <?php if ($smooth_slider['content_font'] == "Georgia"){ echo "selected";} ?> >Georgia</option>
<option value="Helvetica" <?php if ($smooth_slider['content_font'] == "Helvetica"){ echo "selected";}?> >Helvetica</option>
<option value="Monotype Corsiva" <?php if ($smooth_slider['content_font'] == "Monotype Corsiva"){ echo "selected";}?> >Monotype Corsiva</option>
<option value="Times New Roman" <?php if ($smooth_slider['content_font'] == "Times New Roman"){ echo "selected";}?> >Times New Roman</option>
<option value="Trebuchet MS" <?php if ($smooth_slider['content_font'] == "Trebuchet MS"){ echo "selected";}?> >Trebuchet MS</option>
<option value="Verdana" <?php if ($smooth_slider['content_font'] == "Verdana"){ echo "selected";}?> >Verdana</option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row">Font Color</th>
<td><input type="text" name="smooth_slider_options[content_fcolor]" id="color_value_5" value="<?php echo $smooth_slider['content_fcolor']; ?>" />&nbsp; <img id="color_picker_5" src="<?php echo smooth_slider_plugin_url( 'images/color_picker.png' ); ?>" alt="Pick the color of your choice" /><div class="color-picker-wrap" id="colorbox_5"></div></td>
</tr>

<tr valign="top">
<th scope="row">Font Size</th>
<td><input type="text" name="smooth_slider_options[content_fsize]" id="smooth_slider_content_fsize" class="small-text" value="<?php echo $smooth_slider['content_fsize']; ?>" />&nbsp;px</td>
</tr>

<tr valign="top">
<th scope="row">Font Style</th>
<td><select name="smooth_slider_options[content_fstyle]" id="smooth_slider_content_fstyle" >
<option value="bold" <?php if ($smooth_slider['content_fstyle'] == "bold"){ echo "selected";}?> >Bold</option>
<option value="bold italic" <?php if ($smooth_slider['content_fstyle'] == "bold italic"){ echo "selected";}?> >Bold Italic</option>
<option value="italic" <?php if ($smooth_slider['content_fstyle'] == "italic"){ echo "selected";}?> >Italic</option>
<option value="normal" <?php if ($smooth_slider['content_fstyle'] == "normal"){ echo "selected";}?> >Normal</option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row">Pick content From</th>
<td><select name="smooth_slider_options[content_from]" id="smooth_slider_content_from" >
<option value="slider_content" <?php if ($smooth_slider['content_from'] == "slider_content"){ echo "selected";}?> >Slider Content Custom field</option>
<option value="excerpt" <?php if ($smooth_slider['content_from'] == "excerpt"){ echo "selected";}?> >Post Excerpt</option>
<option value="content" <?php if ($smooth_slider['content_from'] == "content"){ echo "selected";}?> >From Content</option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row">Maximum content size (in characters)</th>
<td><input type="text" name="smooth_slider_options[content_chars]" id="smooth_slider_content_chars" class="small-text" value="<?php echo $smooth_slider['content_chars']; ?>" />&nbsp;characters &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
</tr>
<tr valign="top">
<th scope="row">Maximum content size (in words)</th>
<td><input type="text" name="smooth_slider_options[content_limit]" id="smooth_slider_content_limit" class="small-text" value="<?php echo $smooth_slider['content_limit']; ?>" />&nbsp;words (if specified will override the &quot;Maximum Content Size in Chracters&quot; setting above)</td>
</tr>

</table>

<h2>Miscellaneous</h2> 

<table class="form-table">
<tr valign="top">
<th scope="row">Retain these html tags</th>
<td><input type="text" name="smooth_slider_options[allowable_tags]" class="regular-text code" value="<?php echo $smooth_slider['allowable_tags']; ?>" />&nbsp;(read <a href="http://www.clickonf5.org/smooth-slider" title="how to retain html like line breaks and links in the Smooth Slider" target="_blank">Usage section of the plugin page</a> to know more)</td>
</tr>
<tr valign="top">
<th scope="row">Continue Reading Text</th>
<td><input type="text" name="smooth_slider_options[more]" class="regular-text code" value="<?php echo $smooth_slider['more']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Minimum User Level to add Post to the Slider</th>
<td><select name="smooth_slider_options[user_level]" >
<option value="manage_options" <?php if ($smooth_slider['user_level'] == "manage_options"){ echo "selected";}?> >Administrator</option>
<option value="edit_others_posts" <?php if ($smooth_slider['user_level'] == "edit_others_posts"){ echo "selected";}?> >Editor and Admininstrator</option>
<option value="publish_posts" <?php if ($smooth_slider['user_level'] == "publish_posts"){ echo "selected";}?> >Author, Editor and Admininstrator</option>
<option value="edit_posts" <?php if ($smooth_slider['user_level'] == "edit_posts"){ echo "selected";}?> >Contributor, Author, Editor and Admininstrator</option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row">Add Shortcode Support</th>
<td><input name="smooth_slider_options[shortcode]" type="checkbox" value="1" <?php checked('1', $smooth_slider['shortcode']); ?>  />&nbsp;check this if you want to use Smooth Slider Shortcode i.e [smoothslider]</td>
</tr>

<tr valign="top">
<th scope="row">Smooth Slider Styles to Use on Other than Post/Pages <small>(i.e. for index.php,category.php,archive.php etc)</small></th>
<td><select name="smooth_slider_options[stylesheet]" >
<?php 
$directory = SMOOTH_SLIDER_CSS_DIR;
if ($handle = opendir($directory)) {
    while (false !== ($file = readdir($handle))) { 
     if($file != '.' and $file != '..') { ?>
      <option value="<?php echo $file;?>" <?php if ($smooth_slider['stylesheet'] == $file){ echo "selected";}?> ><?php echo $file;?></option>
 <?php  } }
    closedir($handle);
}
?>
</select><small>(The css styles set thru the above settings do represent default.css, do not change this file. You can select and then change the sample.css in wp-content -> plugins -> smooth-slider -> css-> styles folder of your WP installation. You can also create a new css file there and select it from here. This will help you get custom smooth slider styles on other than posts and pages. On post and pages, you do get a special option to select the slider stylesheet thru a setting.)</small>
</td>
</tr>

<tr valign="top">
<th scope="row">Multiple Slider Feature</th>
<td><label for="smooth_slider_multiple"> 
<input name="smooth_slider_options[multiple_sliders]" type="checkbox" id="smooth_slider_multiple" value="1" <?php checked("1", $smooth_slider['multiple_sliders']); ?> /> 
 Enable Multiple Slider Function on Edit Post/Page</label></td>
</tr>

</table>

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div> <!--end of float left -->
</form>

</div> <!--end of float wrap -->


<?php	
}
function register_mysettings() { // whitelist options
  register_setting( 'smooth-slider-group', 'smooth_slider_options' );
}
?>