<?php
/*
Plugin Name: Pull This
Plugin URI: http://wordpress.org/extend/plugins/pull-this/
Description: Select and place pull quotes inside your posts using shortcodes ([pullthis][/pullthis] and [pullshow]). Pull quotes are inserted with javascript.
Author: Jon Smajda
Version: 1.1
*/

/* Create shortcodes */

// shortcode to mark a pull quote: [pullthis id="foo"]Make me a pull quote[/pullthis]
function pull_this_mark($atts, $content = null) {
  extract(shortcode_atts(array( 'id' => '1', 'display' => 'both'), $atts));
  $id = pull_this_build_id($id, 'mark');
  if ($display == 'outside' )
    return '<span class="pull-this-mark" id="'.$id.'" style="display:none;">'.$content.'</span>';
  else 
    return '<span class="pull-this-mark" id="'.$id.'">'.$content.'</span>';
}
add_shortcode('pullthis', 'pull_this_mark');

// shortcode to display a pullquote: [pullshow id="foo"]
function pull_this_show($atts) {
  extract(shortcode_atts(array( 'id' => '1'), $atts));
  $id = pull_this_build_id($id, 'show');
  return '<div class="pull-this-show" id="'.$id.'" style="display:none;"></div>';
}
add_shortcode('pullshow', 'pull_this_show');

// prepend pull-this-mark or -show and post_id to id
function pull_this_build_id($id, $s_or_m = "mark") {
  $post_id = get_the_ID();
  $id = "pull-this-".$s_or_m."-".$post_id."-".$id;
  return $id;
}

/* Load JS and CSS */
function pull_this_scripts() {
  wp_enqueue_style(
    'pull-this', 
    WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/pull-this.css', 
    false, false, 'all'
  );
  wp_enqueue_script(
    'pull-this',
    WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/pull-this.js',
    array('jquery')
  );
}
add_action('wp_enqueue_scripts', 'pull_this_scripts');

?>
