<?php
/*
Plugin Name: Simple Google Sitemap XML
Version:     1.4.1
Plugin URI:  http://itx-technologies.com/blog/simple-google-sitemap-xml-for-wordpress
Description: Generates a valid Google XML sitemap with a very simple admin interface
Author:      iTx Technologies
Author URI:  http://itx-technologies.com/
*/

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

//Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
  define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );


/*
Genarates the actual XML sitemap file on the server
*/
function gsxml_generate_xmlsitemap() {
     
$filename = "sitemap.xml";
     
if (get_option('gsxml_store') == "1") { $file_handler = fopen(WP_PLUGIN_DIR.'/simple-google-sitemap-xml/'.$filename, "w+");  } 

elseif (get_option('gsxml_store') == "2") { $file_handler = fopen(ABSPATH.$filename, "w+"); }
else {	$file_handler = fopen(WP_PLUGIN_DIR.'/simple-google-sitemap-xml/'.$filename, "w+");   }
     
     
     if (!$file_handler) {
	  die;
     }
     
     else {
     $content = gsxml_get_content();
     fwrite($file_handler, $content);
     fclose($file_handler);
     }
}

/*
Gets the content of the database and formats it to form valid XML
*/
function gsxml_get_content() {
    
    global $wpdb;
    
    /* Setting default values for the settings */
    if (get_option('gsxml_hp')) { $home_p = get_option('gsxml_hp'); } else { $home_p = 0.5;}
    if (get_option('gsxml_hf')) { $home_f = get_option('gsxml_hf'); } else { $home_f = 'weekly';}
    if (get_option('gsxml_gp')) { $other_p = get_option('gsxml_gp'); } else { $other_p = 0.5;}
    if (get_option('gsxml_gf')) { $other_f = get_option('gsxml_gf'); } else { $other_f = 'weekly';}
    if (get_option('gsxml_pri_freq')) { $gsxml_pri_freq = get_option('gsxml_pri_freq');} else { $gsxml_pri_freq = "Disable"; }
    if (!get_option('gsxml_cat')) { $gsxml_cat = 'NotInclude';} else {	$gsxml_cat = get_option('gsxml_cat'); }
    if (!get_option('gsxml_tag')) { $gsxml_tag = 'NotInclude';} else {	$gsxml_tag = get_option('gsxml_tag'); }
    if (!get_option('gsxml_last_ch')) { $gsxml_last_ch = 'Disable';} else {	$gsxml_last_ch = get_option('gsxml_last_ch'); }
    
    
    $xmlcontent =  '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    $xmlcontent .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    $xmlcontent .= "
     <url>
	  <loc>".get_option( 'siteurl' )."/</loc>";
	  if ($gsxml_last_ch == 'Enable') {
	       $xmlcontent .= "<lastmod>".date('Y-m-d')."</lastmod>";
	  }     
	       
	  // verify that priority + change frequency have been enabled
	  if ($gsxml_pri_freq == 'Enable') {
	       $xmlcontent .= "<changefreq>".$home_f."</changefreq>
	       <priority>".$home_p."</priority>";
	  }
     $xmlcontent .= "</url>\n";
     
    // Query for the posts 
    $table_name = $wpdb->prefix . "posts";
    $query = "SELECT year(post_modified) AS y, month(post_modified) AS m, day(post_modified) AS d, ID,post_title, post_modified,post_name, post_type, post_parent FROM $table_name WHERE post_status = 'publish' AND (post_type = 'page' OR post_type = 'post') ORDER BY post_date DESC";
    $myrows = $wpdb->get_results($query); 
     
    foreach ($myrows as $myrow) {
    
    $permalink = utf8_encode($myrow->post_name);
    $type = $myrow->post_type;
    $date = $myrow->y."-";
    
    if ($myrow->m < 10) {
	  $date .= "0".$myrow->m."-";
    
    }
    
    else {
	  $date .= $myrow->m."-";
    
    }
    if ($myrow->d < 10) {
	  $date .= "0".$myrow->d;
    }
    
    else {
	  $date .= $myrow->d;
    }
    
	  $id = $myrow->ID;
	  //$url = get_option( 'siteurl' )."/?p=".$id;
	  $url = get_permalink($id);

    $xmlcontent .= "
     <url>
	  <loc>".$url."</loc>";
	  if ($gsxml_last_ch == 'Enable') {
	       $xmlcontent .= "<lastmod>".$date."</lastmod>";
	  }
	  
	  // verify that priority + change frequency have been enabled
	  if ($gsxml_pri_freq == 'Enable') {
	       $xmlcontent .= "<changefreq>".$other_f."</changefreq>
	       <priority>".$other_p."</priority>";
	  }
     $xmlcontent .= "</url>\n";
    
    }
    
    // If categories have been enabled, include them in the XML
    if ($gsxml_cat == 'Include') {
	  
	  // Prepare the SQL query
	  $table_terms = $wpdb->prefix . "terms";
	  $table_taxonomy = $wpdb->prefix . "term_taxonomy";
	  $query = "SELECT $table_terms.term_id, $table_taxonomy.taxonomy FROM $table_terms, $table_taxonomy WHERE ($table_terms.term_id = $table_taxonomy.term_id AND $table_taxonomy.taxonomy = 'category') ";
	  $mycats = $wpdb->get_results($query); 
	  
	  //  Output each category link with the date being when it 
	  $date = date('Y-m-d');
	  
	  foreach ($mycats as $mycat) {
	        
	        $xmlcontent .= "
				   <url>
					<loc>".get_category_link( $mycat->term_id )."</loc>
					<lastmod>".$date."</lastmod>";
					
					// verify that priority + change frequency have been enabled
					if ($gsxml_pri_freq == 'Enable') {
					     $xmlcontent .= "<changefreq>".$other_f."</changefreq>
					     <priority>".$other_p."</priority>";
					}
		    $xmlcontent .= "</url>\n";
	  
	  }
    
    }
    
    // If tags have been enabled, include them in the XML
    if ($gsxml_tag == 'Include') {
	  
	  // Prepare the SQL query
	  $table_terms = $wpdb->prefix . "terms";
	  $table_taxonomy = $wpdb->prefix . "term_taxonomy";
	  $query = "SELECT $table_terms.term_id, $table_taxonomy.taxonomy FROM $table_terms, $table_taxonomy WHERE ($table_terms.term_id = $table_taxonomy.term_id AND $table_taxonomy.taxonomy = 'post_tag') ";
	  $mytags = $wpdb->get_results($query); 
	  
	  //  Output each category link with the date being when it 
	  $date = date('Y-m-d');
	  
	  foreach ($mytags as $mytag) {
	        
	        $xmlcontent .= "
				   <url>
					<loc>".get_tag_link( $mytag->term_id )."</loc>
					<lastmod>".$date."</lastmod>";
					
					// verify that priority + change frequency have been enabled
					if ($gsxml_pri_freq == 'Enable') {
					     $xmlcontent .= "<changefreq>".$other_f."</changefreq>
					     <priority>".$other_p."</priority>";
					}
		    $xmlcontent .= "</url>\n";
	  
	  }
    
    }
    
    
     // end of the XML sitemap </urlset>
    $xmlcontent .= '</urlset>'."\n";
    return $xmlcontent;

}

/*
Creates an admin link
*/
function gsxml_menu_link() {
  if (function_exists('add_options_page')) {
    $gsxml_page = add_options_page('Google Sitemap XML', 'Google Sitemap XML', 'administrator', basename(__FILE__), 'gsxml_settings');
  }
}


/*
Admin setting page
*/
function gsxml_settings() { 

if (get_option('gsxml_store') == "1") { $path = WP_PLUGIN_URL.'/simple-google-sitemap-xml/sitemap.xml';  } 

elseif (get_option('gsxml_store') == "2") { $path = get_option( 'siteurl' ).'/sitemap.xml'; }
else {	$path = WP_PLUGIN_URL.'/simple-google-sitemap-xml/sitemap.xml';  }


?>
  <h2>Google Sitemap XML</h2>
     <h4>by <a style="color: #A30B06;" href="http://itx-technologies.com" target="_blank">iTx Technologies</a></h4>
     <div style="float:left;"><?php gsxml_paypal_donate();?></div>
     <div style="clear:both;"></div>
     <div style="margin: 20px 0 0 0;">
     <h3 style="color: #A30B06; margin-bottom: 0;">Your XML Sitemap</h3>
     <p style="margin:0 0 1em 0;">
     This is the absolute URL of your XML sitemap.  You can copy/paste it in <a href="https://www.google.com/webmasters/tools/" target="_blank">Google Webmaster Tools</a> which greatly increases the speed at which Google indexes your website.  
     <br /><br />
     <strong>The XML sitemap is automatically regenerated when you publish or delete a new post/page.</strong>
     </p>
     <form method="post" action="options.php">
     <?php wp_nonce_field('update-options'); ?>
     <table>
     <tr>
     <td>Where do you want to store your XML file ?</td>
     <td>
     <select name="gsxml_store" id="gsxml_store" type="text" value="<?php echo get_option('gsxml_store'); ?>" />
     <option value="1" <?php if (get_option('gsxml_store') == "1") { echo "selected"; } ?> >In the plugin's folder</option>
     <option value="2" <?php if (get_option('gsxml_store') == "2") { echo "selected"; } ?> >In my website's root folder</option>
     </select>
     </td>
     </tr>
     </table>
     
     <table>
     <tr>
     <td>Your XML absolute URL:</td><td style="background-color: white; padding: 5px;"><?php echo $path; ?></td>
     </tr>
     </table>
     
     
     <h3 style="color: #A30B06; margin-bottom: 0;">Parameters</h3>
     <p style="margin:0 0 1em 0;">
     You can slightly tweak your XML sitemap as described in the <a href="http://sitemaps.org/protocol.php" target="_blank">Sitemaps XML Protocol</a>.<br /><br />
     The following parameters will be applied to the global XML sitemap.  In other words, you cannot choose different parameters for each and every post/page, except the homepage.
     </p>
     <p style="font-weight:bold;">Last changed</p>
     <table width="50%">
	  <tr>
	       <td width="80%">Do you want to enable the <strong>last changed</strong> attribute ?  It is set to <strong>disabled</strong> by default.</td>
	       <td>
		    <?php
			 // if the value for the frequency + priority have neve been set, set to Yes
			 if (!get_option('gsxml_last_ch')) { $gsxml_last_ch = 'Disable';}
			 else {	$gsxml_last_ch = get_option('gsxml_last_ch'); }
		    ?>
		    <select name="gsxml_last_ch" id="gsxml_hf" type="text" value="<?php echo $gsxml_last_ch ?>" />
		    <option value="Disable" <?php if($gsxml_last_ch=="Disable") {echo 'selected';}?>>disable</option>
		    <option value="Enable" <?php if($gsxml_last_ch=="Enable") {echo 'selected';}?>>enable</option>
		    </select>
	       </td>
	  </tr>
	  <tr>
	       <td>
		    If the attributes are <strong>enabled</strong>, you can set them here :
	       </td>
	  </tr>
     </table>
      <p style="font-weight:bold;">Attributes</p>
     <table width="50%">
	  <tr>
	       <td width="80%">Do you want to enable the <strong>priority</strong> and the <strong>change frequency</strong> attributes ?  It is set to <strong>disabled</strong> by default.</td>
	       <td>
		    <?php
			 // if the value for the frequency + priority have neve been set, set to Yes
			 if (!get_option('gsxml_pri_freq')) { $gsxml_pri_freq = 'Disable';}
			 else {	$gsxml_pri_freq = get_option('gsxml_pri_freq'); }
		    ?>
		    <select name="gsxml_pri_freq" id="gsxml_hf" type="text" value="<?php echo $gsxml_pri_freq ?>" />
		    <option value="Disable" <?php if($gsxml_pri_freq=="Disable") {echo 'selected';}?>>disable</option>
		    <option value="Enable" <?php if($gsxml_pri_freq=="Enable") {echo 'selected';}?>>enable</option>
		    </select>
	       </td>
	  </tr>
	  <tr>
	       <td>
		    If the attributes are <strong>enabled</strong>, you can set them here :
	       </td>
	  </tr>
     </table>
     <div style="margin-left: 10%;">
	  <table width="50%">
	  <tr>
	       <p style="font-weight:bold;">Homepage parameters</p>
	       <th width="150">Priority</th>
	       <td width="100">
		    <select name="gsxml_hp" id="gsxml_hp" type="text" value="<?php echo get_option('gsxml_hp'); ?>" />
		    <?php for ($i=0; $i<1.05; $i+=0.1) {
			 echo "<option value='".$i."' ";
			 if (get_option('gsxml_hp')==$i) {
			      echo ' selected';
			 } // end if
			 
			 echo ">";
			 if($i==0) { echo "0.".$i;} 
			 elseif($i==1.0) { echo $i.'0';} 
			 else {echo $i;}
			 echo "</option>";
		    } // end for
		    ?>
		    </select>
	       </td>
	       <th width="150">Frequency</th>
	       <td width="100">
		    <select name="gsxml_hf" id="gsxml_hf" type="text" value="<?php echo get_option('gsxml_hf'); ?>" />
		    <option value="always" <?php if(get_option('gsxml_hf')=="always") {echo 'selected';}?>>always</option>
		    <option value="hourly" <?php if(get_option('gsxml_hf')=="hourly") {echo 'selected';}?>>hourly</option>
		    <option value="weekly" <?php if(get_option('gsxml_hf')=="weekly") {echo 'selected';}?>>weekly</option>
		    <option value="monthly" <?php if(get_option('gsxml_hf')=="monthly") {echo 'selected';}?>>monhtly</option>
		    <option value="yearly" <?php if(get_option('gsxml_hf')=="yearly") {echo 'selected';}?>>yearly</option>
		    <option value="never"  <?php if(get_option('gsxml_hf')=="never") {echo 'selected';}?>>never</option>
		    </select>
		    </td>
	  </tr>
	  </table>
	  <table width="50%">
	  <tr>
	  <p style="font-weight:bold;">General parameters</p>
	  
	       <th width="150">Priority</th>
	       <td width="100">
	       <select name="gsxml_gp" id="gsxml_gp" type="text" value="<?php echo get_option('gsxml_gp'); ?>" />
		    <?php for ($i=0; $i<1.05; $i+=0.1) {
			 echo "<option value='".$i."' ";
			 if (get_option('gsxml_gp')==$i) {
			      echo ' selected';
			 } // end if
			 
			 echo ">";
			 if($i==0) { echo "0.".$i;} 
			 elseif($i==1.0) { echo $i.'0';} 
			 else {echo $i;}
			 echo "</option>";
		    } // end for
		    ?>
		    </select>
	       </td>
	       
	       <th width="150">Frequency</th>
	       <td width="100">
		    <select name="gsxml_gf" id="gsxml_gf" type="text" value="<?php echo get_option('gsxml_gf'); ?>" />
		    <option value="always" <?php if(get_option('gsxml_gf')=='always') {echo 'selected';}?>>always</option>
		    <option value="hourly" <?php if(get_option('gsxml_gf')=='hourly') {echo 'selected';}?>>hourly</option>
		    <option value="weekly" <?php if(get_option('gsxml_gf')=='weekly') {echo 'selected';}?>>weekly</option>
		    <option value="monthly" <?php if(get_option('gsxml_gf')=='monthly') {echo 'selected';}?>>monthly</option>
		    <option value="yearly" <?php if(get_option('gsxml_gf')=='yearly') {echo 'selected';}?>>yearly</option>
		    <option value="never" <?php if(get_option('gsxml_gf')=='never') {echo 'selected';}?>>never</option>
		    </select>
	       </td>
	  </tr>
	  </table>
     </div>
     <h3 style="color: #A30B06; margin-bottom: 0;">Categories and Tags</h3>
     <p style="margin:0 0 1em 0;">
	  Simple Google Sitemap XML nows lets you include the categories and tags of your website into your generated sitemap.xml.<br />Simply choose which of them (or both) you want to include below :
     </p>
     <p style="font-weight:bold;">Categories: &nbsp;&nbsp;
	  <?php
	       // if the value for the categories have neve been set, set to Yes
	       if (!get_option('gsxml_cat')) { $gsxml_cat = 'NotInclude';}
	       else {	$gsxml_cat = get_option('gsxml_cat'); }
	  ?>
	  <select name="gsxml_cat" id="gsxml_cat" type="text" value="<?php echo $gsxml_tag ?>" />
	  <option value="NotInclude" <?php if($gsxml_cat=="NotInclude") {echo 'selected';}?>>do not include</option>
	  <option value="Include" <?php if($gsxml_cat=="Include") {echo 'selected';}?>>include</option>
	  </select>
     </p>
     <p style="font-weight:bold;">Tags: &nbsp;&nbsp;
	  <?php
	       // if the value for the tags have neve been set, set to Yes
	       if (!get_option('gsxml_tag')) { $gsxml_tag = 'NotInclude';}
	       else {	$gsxml_tag = get_option('gsxml_tag'); }
	  ?>
	  <select name="gsxml_tag" id="gsxml_tag" type="text" value="<?php echo $gsxml_tag ?>" />
	  <option value="NotInclude" <?php if($gsxml_tag=="NotInclude") {echo 'selected';}?>>do not include</option>
	  <option value="Include" <?php if($gsxml_tag=="Include") {echo 'selected';}?>>include</option>
	  </select>
     </p>
     <!-- Update the values -->
     <input type="hidden" name="action" value="update" />
     <input type="hidden" name="page_options" value="gsxml_hp,gsxml_gp,gsxml_hf,gsxml_gf,gsxml_store,gsxml_pri_freq, gsxml_cat, gsxml_tag,gsxml_last_ch" />

     <p style="margin-top: 20px;">
     <input type="submit" value="<?php _e('Save Changes'); ?>" />
     </p>
     </div>
<?php

/* Generate the sitemap once the plugin is saved */
gsxml_generate_xmlsitemap();
}



/*
Paypal donate button
*/
function gsxml_paypal_donate() {
echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="5MTLLW5LKCGEN">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
<img alt="" border="0" src="https://www.paypal.com/fr_CA/i/scr/pixel.gif" width="1" height="1">
</form>';

}


if ( is_admin() ){
     add_action('admin_menu', 'gsxml_menu_link');
}

function activate_gsxml () {

     gsxml_generate_xmlsitemap();
}

//register_activation_hook(WP_PLUGIN_DIR.'/simple-google-sitemap-xml/simple-google-sitemap-xml.php', 'generate_xmlsitemap');
register_activation_hook(WP_PLUGIN_DIR.'/simple-google-sitemap-xml/simple-google-sitemap-xml.php','activate_gsxml');
add_action ( 'activate_plugin', 'gsxml_generate_xmlsitemap' );
add_action ( 'publish_post', 'gsxml_generate_xmlsitemap' );
add_action ( 'publish_page', 'gsxml_generate_xmlsitemap' );
add_action ( 'trashed_post', 'gsxml_generate_xmlsitemap' );
?>