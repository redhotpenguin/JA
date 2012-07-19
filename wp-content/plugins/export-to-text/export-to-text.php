<?php
/*
Plugin Name: Export to Text
Plugin URI: http://www.hypedtext.com
Description: A simple plugin to export WordPress post data into a tab-separated text file format (TSV)
Version: 2.1
Author: Sky Rocket Inc.
Author URI: http://www.hypedtext.com
*/
?>
<?php
register_activation_hook( __FILE__, 'sre2t_install' ); // executes funcition upon plugin activation

function sre2t_install() { // function executed at plugin activation - checks if WP version is compatible with the plugin - NOT WORKING
	if ( version_compare( get_bloginfo( 'version' ), '3.3', '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
	}
}

add_action( 'admin_menu', 'sre2t_admin_page' );

function sre2t_admin_page() { // adds menu to "Tools" section linked to sre2t_manage function
	$plugin_page = add_management_page('Export to Text', 'Export to Text', 9, basename(__FILE__), 'sre2t_manage'); //Sets and saves plugin page in WP admin
	add_action( 'load-'.$plugin_page, 'sre2t_add_js' ); //triggers sre2t_add_js function just on plugin page
}

function sre2t_add_js() { //properly adds JS file to page
	wp_enqueue_style( 'export_to_text_css', plugins_url( 'export-to-text.css' , __FILE__ ) ); 
	
	wp_enqueue_script( 'export_to_text_js', plugins_url( 'export-to-text.js' , __FILE__ ) );
	
	$protocol = isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://'; //This is used to set correct adress if secure protocol is used so ajax calls are working
	$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php', $protocol )
	);
	wp_localize_script( 'export_to_text_js', 'export_to_text_js', $params );
}

require_once( 'export-to-text_helpers.php' ); //loads file with fuctions for help

function sre2t_manage() { // Sre2t_manage function used to display Export To Text page

		global $wpdb, $wp_locale;
		
		// Code used to get start and end dates with posts
		$dateoptions = $edateoptions = '';
		$types = "'" . implode("', '", get_post_types( array( 'public' => true, 'can_export' => true ), 'names' )) . "'";
		if ( function_exists( get_post_stati ) ) {
			$stati = "'" . implode("', '", get_post_stati( array( 'internal' => false ), 'names' )) . "'";
		}
		else {
			$stati = "'" . implode("', '", get_post_statuses( array( 'internal' => false ), 'names' )) . "'";
		}
		if ( $monthyears = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, YEAR(DATE_ADD(post_date, INTERVAL 1 MONTH)) AS `eyear`, MONTH(DATE_ADD(post_date, INTERVAL 1 MONTH)) AS `emonth` FROM $wpdb->posts WHERE post_type IN ($types) AND post_status IN ($stati) ORDER BY post_date ASC ") ) {
			foreach ( $monthyears as $k => $monthyear )
				$monthyears[$k]->lmonth = $wp_locale->get_month( $monthyear->month, 2 );
			for( $s = 0, $e = count( $monthyears ) - 1; $e >= 0; $s++, $e-- ) {
				$dateoptions .= "\t<option value=\"" . $monthyears[$s]->year . '-' . zeroise( $monthyears[$s]->month, 2 ) . '">' . $monthyears[$s]->lmonth . ' ' . $monthyears[$s]->year . "</option>\n";
				$edateoptions .= "\t<option value=\"" . $monthyears[$e]->eyear . '-' . zeroise( $monthyears[$e]->emonth, 2 ) . '">' . $monthyears[$e]->lmonth . ' ' . $monthyears[$e]->year . "</option>\n";
			}
		}
		// Displays Export To Text Menu
		?>
		<div class="export-to-text">
        <div class="wrap">
        
            <?php screen_icon(); //function used to get correct icon ?>
            <h2>Export to Text</h2>
            
            <div id="main">
                
	            <p>A simple plugin to export WordPress post data into a tab-separated text file format (TSV). When you click the button below Export to Text will render a text box from which you can copy and paste your data into a text editor or Excel. If you need to re-import posts from a text file, please consider using CSV Importer plugin.</p>
	            
	            <form id="export-to-text-form" action="<?php echo plugins_url( 'export-to-text_dl_txt.php' , __FILE__ ); ?>" method="post"><!--Form posts to "export-to-text_dl_txt.php" responsible for file download-->
	                <h3>Filters</h3>
	                <div id="options_holder">
	                	<div class="option_box option_box_short">
	                		<label id="sdate" class="short_label" for="sdate">Start Date</label>
	                        <select name="sdate" id="sdate">
	                        	<option value="all">All Dates</option>
	                        	<?php 
								echo $dateoptions;
	                        	?>
	                        </select>                 		
	                	</div>
	                	<div class="option_box option_box_short">
	                		<label for="edate" class="short_label">End Date</label>
	                        <select name="edate" id="edate">
	                             <option value="all">All Dates</option>
								 <?php
	                             echo $edateoptions;
	                             ?>
	                        </select>
	                	</div>
	                	<div class="option_box option_box_submit submit">
			                <input type="hidden" name="download" value="<?php echo get_home_path(); ?>" /> <!--hidden input used to sent home path to "export-to-text_dl_txt.php" so it always find and load correct Core WP file-->
			                   
			                	<a href="#" class="button-secondary">Generete for quick copying</a> <!--link connected to js responsible for AJAX call-->
			                    <input type="submit" value="Download as TXT file" name="submit"> <!--Posts data to "export-to-text_dl_txt.php" file-->
	                	</div>               	
	                	<div class="clearboth"></div>
	                	<div class="option_box">
	                		<label for="author" class="full_label">Authors:</label>
	                    	<div class="checkbox_box">
	                            <ul>
	                                <li><label><input type="checkbox" name="author[]" value="e2t_all" checked="yes" /> All</label></li>
		                            <?php
		                            $authors = $wpdb->get_results( "SELECT DISTINCT u.id, u.display_name FROM $wpdb->users u INNER JOIN $wpdb->posts p WHERE u.id = p.post_author ORDER BY u.display_name" );
		                            foreach ( (array) $authors as $author ) {
		                            ?>
		                                <li><label><input type="checkbox" name="author[]" value="<?php echo $author->id; ?>" /> <?php echo $author->display_name; ?></label></li>
		                            <?php
		                            }
		                            ?>
	                            </ul>
	                    	</div>                                  		
	                	</div>
	                	<div class="option_box">
	                		<label for="ptype" class="full_label">Post Types:</label>
	                    	<div class="checkbox_box">
	                            <ul>
	                                <li><label><input type="checkbox" name="ptype[]" value="post" checked="yes" /> Posts</label></li>
	                                <li><label><input type="checkbox" name="ptype[]" value="page" /> Pages</label></li>
	                                <?php 
	                                foreach ( get_post_types( array( 'public' => true,'_builtin' => false ), 'objects' ) as $post_type_obj ) { ?>
	                                    <li><label><input type="checkbox" name="ptype[]" value="<?php echo $post_type_obj->name; ?>" /> <?php echo $post_type_obj->labels->name; ?></label></li>
	                                <?php 
	                                } 
	                                ?>
	                        	</ul>
	                        </div>
	                	</div>                	
	                	<div class="option_box">
	                		<label for="ptype" class="full_label">Statuses:</label>
	                    	<div class="checkbox_box">
	                            <ul>
	                            	<li><label><input type="checkbox" name="post_status[]" value="publish" checked="yes"/> Publish</label></li>        
	                            	<li><label><input type="checkbox" name="post_status[]" value="pending" /> Pending</label></li>        
	                            	<li><label><input type="checkbox" name="post_status[]" value="draft" /> Draft</label></li>        
	                            	<li><label><input type="checkbox" name="post_status[]" value="future" /> Future</label></li>        
	                            	<li><label><input type="checkbox" name="post_status[]" value="private" /> Private</label></li>        
	                            	<li><label><input type="checkbox" name="post_status[]" value="trash" /> Trash</label></li>        
								</ul>
							</div>
	                	</div>
	                	
						<?php
						$taxonomies = array_merge(array('category', 'post_tag'), get_taxonomies(array('_builtin' => false),'names'));
						foreach ($taxonomies as $taxonomy ) { ?>
							<div class="option_box">
								<label for="ptype" class="full_label"><?php echo str_replace('_', ' ', $taxonomy); ?>: </label>
								<?php echo get_categories_checkboxes($taxonomy);?>
							</div>
						<?php
						}
						?>
						
						<div class="option_box">
	                        <label for="cf" class="full_label">Custom field:</label></br>
	                    	<label class="short_label">Name: </label><input type="text" name="cfname"></br>
	                   		<label class="short_label">Value: </label><input type="text" name="cfvalue" >
						</div>
						
	                	<div class="option_box">
	                		<label for="ptype" class="full_label">Select data to generate:</label>
	                    	<div class="checkbox_box">
	                            <ul>
	                            	<li><label><input type="checkbox" name="data_filter[]" value="ID" checked="yes"/> ID</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Title" checked="yes"/> Title</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Date" checked="yes"/> Date</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Post Type" checked="yes"/> Post Type</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Categories" checked="yes"/> Categories</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Tags" checked="yes"/> Tags</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Custom Taxonomies" checked="yes"/> Custom Taxonomies</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Permlink" checked="yes"/> Permlink</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Content" checked="yes"/> Content</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Author" checked="yes"/> Author</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Author Email" checked="yes"/> Author Email</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Custom Fields" checked="yes"/> Custom Fields</label></li>        
	                            	<li><label><input type="checkbox" name="data_filter[]" value="Comments" checked="yes"/> Comments</label></li>
								</ul>
							</div>
	                	</div>					                	
	                	
	                </div>
	                	
	            </form>
	            <div class="clearboth"></div>
	            
	            <div id="pre_holder">
	            	<a href="#" id="pre_close">Close</a>
	            	<pre id="pre" onclick="containerSelect(this)"><strong>Just click on "Generete for quick copying" and then click on this box to select and copy the text.<br/>Then paste it (Paste Special works best) into a new Excel document.</strong></pre>
	            </div>
	                
				<div class="info-box" id="donate_box">
					<div class="sidebar-name">
						<h3>Donate!</h3>
					</div>
					<div class="sidebar-description">
	                    <p>Please make a donation to aid the development of plugins and support open source software.</p>
	                    <form id="donate_form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
	                        <input type="hidden" name="cmd" value="_s-xclick">
	                        <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
	                        <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	                        <img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
	                    </form>					
                    </div>
				</div>

				<div class="info-box">
					<div class="sidebar-name">
						<h3>Need support?</h3>
					</div>
					<div class="sidebar-description">
	                    <p>Please visit the <a href="http://wordpress.org/tags/export-to-text">WordPress.org forums</a> if you are having any issues with this plugin.</p>				
                    </div>
				</div>

				<div class="info-box">
					<div class="sidebar-name">
						<h3>Rate me!</h3>
					</div>
					<div class="sidebar-description">
	                    <p><a href="http://wordpress.org/extend/plugins/export-to-text/">Click here</a> and rate this plugin on WordPress.org</p>					
                    </div>
				</div>
                
                <div class="clearboth"></div>
                            
            </div>
        </div>
        </div>
<?	
}

add_action( 'wp_ajax_sre2t_ajax', 'sre2t_ajax' ); //adds function to WP ajax
function sre2t_ajax() { //Function used for generating results for display in PRE tag and saving as TXT

	// sets correct values for start and end date + adds WP "post_where" filter
	if ( ($_POST['sdate'] != 'all' || $_POST['edate'] != 'all') && !empty($_POST['sdate']) ) {
		add_filter('posts_where', 'filter_where');
		function filter_where($where = '') {
			if ( $_POST['sdate'] != 'all' && $_POST['edate'] == 'all') {
				$sdate =  "AND post_date >= '".$_POST['sdate']."'";
			}
			elseif ( $_POST['edate'] != 'all' && $_POST['sdate'] == 'all') {
				$edate = "AND post_date < '".$_POST['edate']."'";
			}
			else {
				$sdate =  " AND post_date >= '".$_POST['sdate']."'";
				$edate = " AND post_date < '".$_POST['edate']."'";
			}
			$where .= $sdate;
			$where .= $edate;
			
			return $where;
		}
	}
	
	$args = array( // arguments used for WP_Query
		'posts_per_page' => -1,
		'post_type' => $_POST['post_type'],
		'post_status' => $_POST['post_status'],
		'order' => ASC,
		'meta_key' => $_POST['cfname'],
		'meta_value' => $_POST['cfvalue']
	);
	//creates arrays for taxonomies
	$is_tax_query = 0;
	foreach ($_POST['taxonomy'] as $key => $value) {
		if( !in_array('e2t_all', $value) ) {
			if($is_tax_query == 0) {
				$args['tax_query'] = array('relation' => 'OR');
				$is_tax_query = 1;
			}
			$temp = array( 'taxonomy' => $key, 'field' => 'id', 'terms' => $value);
			array_push($args['tax_query'], $temp);
		}
	}
	//adds argument for authors
	if( !in_array('e2t_all', $_POST['author']) ) {
		$args['author'] = implode(',', $_POST['author']);
	}
	$export_to_text = new WP_Query( $args ); // new custom loop to get desired results
		
	if ( $export_to_text->have_posts() ) :
		 
		if( !is_array($_POST['data_filter']) ) {
			$_POST['data_filter'] = array();
		}
		$labels = implode("\t", $_POST['data_filter']);
		echo ( $_POST['download'] == '0' ) ? '<strong>'.$labels.'</strong><br />' : $labels."\r\n";// echoes labels differently for Pre and TXT file
	
	while ( $export_to_text->have_posts() ) : $export_to_text->the_post();
	
		$ett_post = '';
	
		if(in_array('ID', $_POST['data_filter'])) $ett_post .= get_the_ID()."\t";
		if(in_array('Title', $_POST['data_filter'])) $ett_post .= get_the_title()."\t";
		if(in_array('Date', $_POST['data_filter'])) $ett_post .= get_the_date()."\t";
		if(in_array('Post Type', $_POST['data_filter'])) $ett_post .= get_post_type()."\t";
		
		if(in_array('Categories', $_POST['data_filter'])) {
			foreach ( get_the_category () as $category) {
				$ett_post .= $category -> cat_name . ',';
			} 
			$ett_post .= "\t";
		}

		if(in_array('Tags', $_POST['data_filter'])) {		
			if (has_tag()){
				foreach ( get_the_tags () as $tag) {
					$ett_post .= $tag -> name . ','; 
				}
			}
			else {
				$ett_post .= '';
			}
			$ett_post .= "\t";
		}
		
		if(in_array('Custom Taxonomies', $_POST['data_filter'])) $ett_post .= custom_taxonomies_terms_links()."\t";
		
		if(in_array('Permlink', $_POST['data_filter'])) $ett_post .= get_permalink()."\t";
		
		if(in_array('Content', $_POST['data_filter'])) {
			global $more;
			$more = 1;
			$thepostcontent = htmlentities(get_the_content(),ENT_QUOTES | ENT_IGNORE,"UTF-8");
			$thepostcontent = preg_replace('/[\t\r\n]*/', '', $thepostcontent);
			$ett_post .= $thepostcontent."\t";
		}
	
		if(in_array('Author', $_POST['data_filter'])) $ett_post .= get_the_author()."\t";
		if(in_array('Author Email', $_POST['data_filter'])) $ett_post .= get_the_author_email()."\t";
		
		if(in_array('Custom Fields', $_POST['data_filter'])) {
			$custom_field_keys = get_post_custom_keys();
			if (!empty($custom_field_keys)) {
				foreach ( $custom_field_keys as $key => $value ) {
					$valuet = trim($value);
					if ( '_' != $valuet{0} ) {
						$mykey_values = get_post_custom_values($value);
						foreach ( $mykey_values as $key2 => $value2 ) {
							$ett_post .= htmlentities(preg_replace('/[\t\r\n]*/', '', "$value => $value2. "),ENT_QUOTES | ENT_IGNORE,"UTF-8");
						}
					}
				}
			}
			else {
				$ett_post .= '';
			}
			$ett_post .= "\t";
		}

		if(in_array('Comments', $_POST['data_filter'])) {		
			$args = array(
				'status' => 'approve',
				'post_id' => get_the_ID()
			);
			$comments = get_comments($args);
			$first = 1;
			foreach($comments as $comment) {
				$comment_content = htmlentities($comment->comment_content,ENT_QUOTES | ENT_IGNORE,"UTF-8");
				$comment_content = preg_replace('/[\t\r\n]*/', '', $comment_content);
				
				$ett_post .= $comment->comment_author.' => '.$comment_content.'. ';
			}
			$ett_post .= "\t";
		}
		
		echo ( $_POST['download'] == '0' ) ? $ett_post.'<br/>' : html_entity_decode($ett_post)."\r\n";// echoes single result differently for Pre and TXT file
		
	endwhile; else:
		echo 'No results';
	endif;
					
	if ( ($_POST['sdate'] != 'all' || $_POST['edate'] != 'all') && !empty($_POST['sdate']) ) { remove_filter('posts_where', 'filter_where'); }
	
	die(); //Functions echoing for AJAX must die
}
?>
<?php
/*  Copyright 2010  Sky Rocket Inc.  (email : jonathan.clarke@skyrocketonlinemarketing.com)

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