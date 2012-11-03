<?php
function get_blank_avatar() { return false; }

/**
 * Copied template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @param mixed $comment Comment record from database
 * @param array $args Arguments from wp_list_comments() call
 * @param int $depth Comment nesting level
 * @see wp_list_comments()
 * @since 1.2
 */
add_theme_support( 'post-thumbnails' );


/*for custom post type*/

add_action( 'init', 'create_post_type' );

function create_post_type() {
	register_post_type( 'featured_banner',
		array(
			'labels' => array(
				'name' => __( 'Homepage Banner' ),
				'singular_name' => __( 'Homepage Banner' ),
				'add_new_item' => 'Add New Homepage Banner'
			),
		'public' => true,
		'has_archive' => true,
		'menu_position' => 20,
		'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
		)
	);
}
/*End of custom post type*/

//	Custom Post Type for Projects

add_action( 'init', 'register_cpt_projects' );

function register_cpt_projects() {

    $labels = array( 
        'name' => _x( 'Projects', 'projects' ),
        'singular_name' => _x( 'Project', 'projects' ),
        'add_new' => _x( 'Add New', 'projects' ),
        'all_items' => _x( 'Projects', 'projects' ),
        'add_new_item' => _x( 'Add New Project', 'projects' ),
        'edit_item' => _x( 'Edit Project', 'projects' ),
        'new_item' => _x( 'New Project', 'projects' ),
        'view_item' => _x( 'View Project', 'projects' ),
        'search_items' => _x( 'Search Projects', 'projects' ),
        'not_found' => _x( 'No projects found', 'projects' ),
        'not_found_in_trash' => _x( 'No projects found in Trash', 'projects' ),
        'parent_item_colon' => _x( 'Parent Project:', 'projects' ),
        'menu_name' => _x( 'Projects', 'projects' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'description' => 'Projects are a compilation of all assets around any major initiative the JA produces themselves or in partnership with other organizations.',
        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'custom-fields', 'comments', 'revisions' ),
        'taxonomies' => array( 'category', 'post_tag' ),
        'menu_position' => 20
    );

    register_post_type( 'projects', $args );
}

//	Custom Post Type for Questions

// add_action( 'init', 'register_cpt_questions' );
// 
// function register_cpt_questions() {
// 
//     $labels = array( 
//         'name' => _x( 'Questions', 'questions' ),
//         'singular_name' => _x( 'Question', 'question' ),
//         'add_new' => _x( 'Add New', 'questions' ),
//         'all_items' => _x( 'Questions', 'questions' ),
//         'add_new_item' => _x( 'Add New Question', 'questions' ),
//         'edit_item' => _x( 'Edit Question', 'questions' ),
//         'new_item' => _x( 'New Question', 'questions' ),
//         'view_item' => _x( 'View Question', 'questions' ),
//         'search_items' => _x( 'Search Questions', 'questions' ),
//         'not_found' => _x( 'No questions found', 'questions' ),
//         'not_found_in_trash' => _x( 'No questions found in Trash', 'questions' ),
//         'parent_item_colon' => _x( 'Parent Question:', 'questions' ),
//         'menu_name' => _x( 'Questions', 'questions' ),
//     );
// 
//     $args = array( 
//         'labels' => $labels,
//         'hierarchical' => true,
//         'public' => true,
//         'show_ui' => true,
//         'show_in_menu' => true,
//         'description' => 'Questions are something that need a description.',
//         'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'custom-fields', 'comments', 'revisions' ),
//         'taxonomies' => array( 'category', 'post_tag' ),
//         'menu_position' => 25
//     );
// 
//     register_post_type( 'questions', $args );
// }




function is_child( $parent = '' ) {
	global $post;
 
	$parent_obj = get_page( $post->post_parent, ARRAY_A );
	$parent = (string) $parent;
	$parent_array = (array) $parent;
 
	if ( in_array( (string) $parent_obj['ID'], $parent_array ) ) {
		return true;
	} elseif ( in_array( (string) $parent_obj['post_title'], $parent_array ) ) {
		return true;	
	} elseif ( in_array( (string) $parent_obj['post_name'], $parent_array ) ) {
		return true;
	} else {
		return false;
	}
}


function bp_dtheme_blog_comments( $comment, $args = '', $depth = '') {
	$GLOBALS['comment'] = $comment; 
	?>
	
	<?php if ( 'pingback' == $comment->comment_type ) return false; ?>
	<li name="comment-<?php comment_ID(); ?>" id="comment-<?php comment_ID(); ?>" class="comment author-id-<?php echo $comment->user_id; ?>">

	
	<div class="comment-content clearfix">
	<?php  $color_header =  get_user_meta($comment->user_id, 'comment_color', true); ?>
			<div class="<?php if($color_header != 'default' ) echo "comment_header_$color_header";?> comment-meta clearfix ">
	
		<div class="comment-avatar-box">
			<div class="avb">
				<a href="<?php echo get_link_to_public_profile($comment->user_id) ?>" rel="nofollow">
					<?php if ( $comment->user_id ) : ?>
						<?php 
						$email =  get_comment_author_email();
						$id =  get_user_id_from_string($email); // retrieve id from an email, defined in /plugins/rpx_override/rpx-override.php
						echo bp_core_fetch_avatar( array( 'item_id' => $id, 'width' => 50, 'height' => 50, 'email' => $comment->comment_author_email ) ); 
						?>
					<?php else : ?>
						<?php 
						
						
						$comment_tweet_avatar   = get_comment_meta($comment->comment_ID, 'tmac_image',true);
						if($comment_tweet_avatar) echo "<img src='$comment_tweet_avatar' />";
						else echo get_avatar( $comment, 50 );

						?>
					<?php endif; ?>
				</a>
			</div>
		</div>

		<div class="comment_header_text">
			<?php if (get_comment_author_url()) { ?>
	<a id="author_link_comment-<?php echo $comment->comment_ID;?>" href="<?php echo get_link_to_public_profile($comment->user_id) ?>" rel="nofollow">
	<?php } ?>
<?php echo get_comment_author(); ?>

			<?php if (get_comment_author_url()) { ?>
</a>
	<?php } ?>
				<em class="comment_date"><a href="<?php echo get_comment_link(); ?>"><?php comment_date() ?> at <?php comment_time(); ?></a></em>
				<?php
					// retrieve the correct user ID based on the email address
					$comment_author_email = $comment->comment_author_email;
					$comment_author_id = get_user_id_from_string($comment_author_email);
				?>
				<p class="bio"><?php echo xprofile_get_field_data('One-Line Bio', $comment_author_id); ?>
				<?php
					global $current_user;
					get_currentuserinfo(); // logged in user, populate $current_user
					 
					$current_user_id = $current_user->ID;
					if(($current_user_id ==  $comment_author_id) && $current_user_id!=0 ){
						echo '<a class="comment_edit_profile" href="'.get_edit_link($current_user_id).'">Edit Profile</a>';
					}
				?>
			</div>
			</div>

			<?php if ( $comment->comment_approved == '0' ) : ?>
			 	<em class="moderate"><?php _e('Your comment is awaiting moderation.'); ?></em><br />
			<?php endif; ?>

			<div class="comment-text">
			<?php comment_text() ?>
			</div>

			<div class="comment-options">
			<?php //rpx_get_comment_reply_link - defined in rpx-override.php
		
			echo rpx_get_comment_reply_link( array('depth' => $depth, 'max_depth' => get_option('thread_comments_depth') , 'login_text'  => 'Log in to Reply') ) ?>

			
				<?php edit_comment_link( __( 'Edit' ),'','' ); ?>
			</div>

		</div>
<?php
}
// Journalism Accelerator Functions

// Limit BuddyPress Activity on Profile

function limit_buddypress_activity( $query_string ) {
	if (plain_bp_title()) {
		$query_string .= '&per_page=5';
	}
	return $query_string;
}
add_filter( 'bp_dtheme_ajax_querystring', 'limit_buddypress_activity' );

bp_core_remove_subnav_item('settings', 'notifications');


function posthasimages() {
	$content = get_the_content();
	$searchimages = '~<img [^>]* />~';
	
	/*Run preg_match_all to grab all the images and save the results in $pics*/
	
	preg_match_all( $searchimages, $content, $pics );
	
	// Check to see if we have at least 1 image
	$iNumberOfPics = count($pics[0]);
	
	if ( $iNumberOfPics > 0 ) {
		 return true;
	}
	//return true;
}

function plain_bp_title() {
	global $bp;
	return $bp->displayed_user->fullname;
}

function display_title() {
	$url_arr = explode('/', $_SERVER['REQUEST_URI']);
	
	if (is_home()) {
		echo '<title>Journalism Accelerator: A forum about innovation in journalism, beyond the usual suspects</title>';
	}

	elseif( ($url_arr[1] == 'members' && empty($url_arr[2])) || ($url_arr[1] == 'members' && strrpos($url_arr[2], 'search_type')) ){
		if(wp_title(null, false)) echo '<title>'.wp_title(null, false).' | Members Directory </title>';
		else echo '<title>Members Directory | Journalism Accelerator </title>';
	}
	
	elseif (is_archive()) {
		if (is_category(28)) {
			echo '<title>Questions and Answers about Journalism: Innovations Discussed on the Journalism Accelerator</title>';
		}
		elseif (post_is_in_descendant_category(28)) {
			echo '<title>Questions and Answers about ' .wp_title(null, false) . ' and Journalism: Innovations Discussed on the Journalism Accelerator</title>';
		}
		elseif (is_category(25)) {
			echo '<title>Resources Related to Journalism: Innovations Discussed on the Journalism Accelerator</title>';
		}
		elseif (post_is_in_descendant_category(25)) {
			echo '<title>Resources Related to ' .wp_title(null, false) . ' and Journalism: Innovations Discussed on the Journalism Accelerator</title>';
		}
		elseif (post_is_in_descendant_category(39) || is_category(39)) {
			echo '<title>Journalism Accelerator Blog</title>';
		}
		else { echo '<title>' . wp_title(null, false) . ' | Journalism Accelerator</title>'; }
	}
	elseif (plain_bp_title()) {
		echo '<title>' . plain_bp_title() . ' | Journalism Accelerator</title>';
	}
	elseif (in_category('about')) {
		echo '<title>About | Journalism Accelerator</title>';
	}
	elseif (is_page()) {
		echo '<title>' . wp_title(null, false) . '</title>';
	}
	else {
		echo '<title>' . wp_title(null, false) . '</title>';
	}
}

function makeURL($URL) {
$URL = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:\+.~#?&//=]+)','<a href=\\1>\\1</a>', $URL);
$URL = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:\+.~#?&//=]+)','<a href=\\1>\\1</a>', $URL);
$URL = eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})','<a href=\\1>\\1</a>', $URL);
return $URL;
}

function startsWith($Haystack, $Needle){
    return strpos($Haystack, $Needle) === 0;
}

function newMakeURL($URL) {
	if (startsWith($URL, 'http://') || startsWith($URL, 'https://')) {
		$href = "<a href=\"$URL\" target=\"_blank\">$URL</a>";
		return $href;
	}
	else {
		$href = "<a href=\"http://$URL\" target=\"_blank\">$URL</a>";
		return $href;
	}
}

/* function has_twitter($has_twitter) {

	$pieces = explode('/', $has_twitter);

	if (sizeof($pieces) == 5) {
	
		# https://twitter.com/#!/redhotpenguin
		$has_twitter = $pieces[4];
		return $has_twitter;

	} else if (sizeof($pieces) == 4) { 

		// http://twitter.com/redhotpenguin
		$has_twitter=$pieces[3];
		return $has_twitter;

	} else if (sizeof($pieces) == 2) {

		// twitter.com/redhotpenguin
		// no http:// on twitter link.
		$has_twitter=$pieces[1];
		return $has_twitter;

	} else {


		$pieces = explode('@', $has_twitter);
		if (sizeof($pieces) == 2) {
			$has_twitter = $pieces[1];
			return $has_twitter;

		} else {
			// use what they entered
			return $has_twitter;
		}
	}
	if ($has_twitter) {
		$pieces = explode('"', $has_twitter);
		$has_twitter=$pieces[0];
		return $has_twitter;
	}
	
} */

function new_excerpt_more($more) {
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

//remove_action( 'bp_adminbar_menus', 'bp_adminbar_thisblog_menu',      6   );
// remove_action( 'bp_adminbar_menus', 'bp_adminbar_notifications_menu', 8   );
remove_action( 'bp_adminbar_menus', 'bp_adminbar_random_menu',        100 ); 
global $bp;
bp_core_remove_nav_item($bp->activity->id);
function my_function_admin_bar(){
    return false;
}
add_filter( 'show_admin_bar' , 'my_function_admin_bar');



function featured_question() {
		rewind_posts();
		query_posts($query_string . '&cat=4&showposts=1');
		if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<h1>Q: <?php the_title() ?></h1><?php
		endwhile;
		endif;
}

function latest_listings_all( $category ) {
	$latest_listings = new WP_Query();
	
	if( isset( $category ) && $category == 'blog'){
		$latest_listings->query('category_name=blog&showposts=4');
	}
	
	else	
		$latest_listings->query('&showposts=4');
	
	while ( $latest_listings->have_posts() ) {
		$latest_listings->the_post();
		global $post;
		$postdate = $post->post_date;
		$formatdate = date("l, F j", strtotime($postdate));
		
		?>
		<div class="resource question clearfix">
			<p class="post-date"><?php echo $formatdate; ?></p>
			<p class="title"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></p>
			<div class="excerpt-text"><?php 
				if($tout = get_post_meta($post->ID, 'tout', true))
					echo $tout;
				else
					the_excerpt(); 
			?></div>
			<?php do_action('thumbs_up_post', $post->ID ); ?>
			</p>
			
		</div>
		<?php
	}
}

function ja_question_home() { ?>
	<div class="quadrants" style="margin-bottom: 18px;">		
	
		<div class="home_box_left">
		  <div class="popular_questions">
					<div class="box_content">
						<h2>Latest Questions</h2>
						<?php latest_questions(); ?>
					</div>
				</div>
		</div>
		
		<div class="home_box_right">
		  <div class="popular_answers">
					<div class="box_content">
						<h2>Latest Answers</h2>
						<?php dp_recent_question_comments(); ?>
					</div>
				</div>
		</div>
		
		<div class="clear"></div>
	</div><?php
	$wp_query->is_category = true;
	$wp_query->is_archive = true;
	$wp_query->is_home = false;
}

function latest_questions() {
	$latest_listings = new WP_Query();
	$latest_listings->query('&cat=28&showposts=4');
	while ( $latest_listings->have_posts() ) {
		$latest_listings->the_post();
		global $post;
		$postdate = $post->post_date;
		$formatdate = date("l, F j", strtotime($postdate));
		?>
		<div class="resource question clearfix">
			<p class="post-date"><?php echo $formatdate; ?></p>
			<p class="title"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></p>
			<div class="excerpt-text"><?php 
			
			if($tout = get_post_meta($post->ID, 'tout', true))
				echo $tout;
			else
				the_excerpt(); 
			
			?></div>
		</div>
		<?php
	}
}

function dp_recent_question_comments() {
	global $wpdb;
	$request = "SELECT * FROM $wpdb->comments";
	$request .= " JOIN $wpdb->posts ON ID = comment_post_ID  JOIN $wpdb->term_relationships ON (ID = $wpdb->term_relationships.object_id) JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)";
	$request .= " WHERE comment_approved = '1' AND post_status = 'publish' AND post_password ='' AND $wpdb->term_taxonomy.taxonomy = 'category' AND $wpdb->term_taxonomy.term_id IN(28)";
	$request .= " ORDER BY comment_date DESC LIMIT 4";

	$comments = $wpdb->get_results($request);
	if ($comments) {
		foreach ($comments as $comment) { 
		
		$commentdate = $comment->comment_date;
		$formatdate = date("l, F j", strtotime($commentdate));
		$email = $comment->comment_author_email;

		?>
		
			<div class="resource answer clearfix">
				<p class="comment-date"><?php echo $formatdate; ?></p>
				<a href="<?php echo get_link_to_public_profile($email); ?>" rel="nofollow">
					
				<?php if ( $comment->user_id ) : ?>
						<?php 
						
						//$email =  get_comment_author_email();
						$email = $comment->comment_author_email;
						$id =  get_user_id_from_string($email); // retrieve id from an email, defined in /plugins/rpx_override/rpx-override.php
						echo bp_core_fetch_avatar( array( 'item_id' => $id, 'width' => 50, 'height' => 50, 'email' => $comment->comment_author_email ) ); 
						?>
					<?php else : ?>
						<?php 
						$comment_tweet_avatar   = get_comment_meta($comment->comment_ID, 'tmac_image',true);
						if($comment_tweet_avatar) : 
						echo "<img class='avatar avatar-48 photo' src='$comment_tweet_avatar' />";
						else : 
						 $real_user_id = get_user_id_from_string($comment->comment_author_email);
						 echo get_avatar($real_user_id,48);
						 endif;
						?>
					<?php endif; ?>
								

				</a>
	<p class="title" style="font-size: 16px; padding-left: 60px;"><a href="<?php echo get_link_to_public_profile($email); ?>"><?php echo $comment->comment_author; ?></a> on <a href="<?php echo get_permalink($comment->comment_post_ID); ?>#comment-<?php echo $comment->comment_ID; ?>"><?php echo $comment->post_title; ?></a></p>
	<div class="comment-text clearfix">
<?php

	 ?><p style="margin-left: 60px;"><?php
				// trim the string
				if (strlen($comment->comment_content) > 250) {
					echo substr( strip_tags( $comment->comment_content ), 0, 250  )."...";
				}
				else {
					echo strip_tags($comment->comment_content);
				}
	
?></p>
</div>
			</div><?php
		}
	}
}

function ja_home() {
 	query_posts($query_string . '&posts_per_page=0');?>
	<div class="quadrants">		
	
		<div class="home_box_left">
		  <div class="popular_questions">
					<div class="box_content">
						<h2>Recent Blog Posts</h2>
						<?php latest_listings_all('blog'); ?>
					</div>
				</div>
		</div>
		
		<div class="home_box_right">
		  <div class="popular_answers">
					<div class="box_content">
						<h2>Recent Comments</h2>
						<?php dp_recent_all_comments(); ?>
					</div>
				</div>
		</div>
		
		<div class="clear"></div>
	</div><?php
	$wp_query->is_category = false;
	$wp_query->is_archive = false;
	$wp_query->is_home = true;
}

function ja_slider () {
	rewind_posts();
	query_posts($query_string . '&showposts=6&tag=featured');
	if ( have_posts() ) : while ( have_posts() ) : the_post();
	$thisID = get_the_ID(); 
	?>
	<div class="slide">
		<div class="left">
			<div class="left-content">
				<p class="content"><a href="<?php the_permalink(); ?>"><?php if (get_post_meta($thisID, 'short_question_title')) { echo get_post_meta($thisID, 'short_question_title', 1); } else { the_title(); } ?></a></p>
				<?php if (get_post_meta($thisID, 'tout')) { ?><p class="tout"><?php echo get_post_meta($thisID, 'tout', 1); ?></p><?php } ?>
			</div>
		</div>
		<div class="right">
			<a href="<?php the_permalink(); ?>"><img src="<?php echo get_post_meta($thisID, 'featured_image', 1); ?>" alt="<?php the_title(); ?>" /></a>
		</div>
	</div>
	<?php
	endwhile;
	endif;
}


/* function useful_answers() {

	global $wpdb;
	$request = "SELECT wp_comments.* FROM $wpdb->comments";
	$request .= " LEFT JOIN wp_gdsr_data_comment on wp_comments.comment_id =  wp_gdsr_data_comment.comment_id";
	$request .= " WHERE comment_approved = '1' AND wp_gdsr_data_comment.user_votes >= 3";
	$request .= " ORDER BY rand() LIMIT 4";

	$comments = $wpdb->get_results($request);
	if ($comments) {
		foreach ($comments as $comment) { ?>
		
			<div class="answer">
				<p class="category"><?php the_category(', ', null, $comment->comment_post_ID); ?></p>
	<p class="title"><a href="<?php echo get_permalink($comment->comment_post_ID); ?>#comment-<?php echo $comment->comment_ID; ?>">
<?php

				// trim the string
				if (strlen($comment->comment_content) > 100) {
					echo strip_tags(substr(apply_filters('get_comment_text', $comment->comment_content), 0, 99)) . "...";
				}
				else {
					echo strip_tags($comment->comment_content);
				}
	
?>
</a></p>

				<div class="rating"><?php wp_gdsr_render_comment($comment, $read_only=true ); ?></div>
				<div class="clear"></div>
			</div><?php
		}
	}
} */


/* function useful_questions() {

	global $wpdb;
	$request = "SELECT wp_posts.* FROM $wpdb->posts";
	$request .= " LEFT JOIN wp_gdsr_data_article on wp_posts.ID =  wp_gdsr_data_article.post_id";
	$request .= " WHERE post_status = 'publish' AND wp_gdsr_data_article.user_votes >= 3";
	$request .= " AND wp_posts.ID IN ( SELECT DISTINCT ID FROM wp_posts, wp_term_relationships,wp_term_taxonomy WHERE wp_term_relationships.object_id=wp_posts.ID AND wp_term_relationships.term_taxonomy_id IN (select term_id from wp_term_taxonomy where parent=28)) ";

	$request .= " ORDER BY rand() LIMIT 4";

//	echo "req: $request";
?>
<?php
	$posts = $wpdb->get_results($request);
	if ($posts) {
		foreach ($posts as $post) { ?>
	
		    <div class="question">
		    <p class="category"><?php the_category(', ', null, $post->ID); ?></p>

		<p class="title"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title ?></a></p>

		<div class="rating"><?php wp_gdsr_render_article(null, $read_only=true); ?></div>
				<div class="clear"></div>
			</div><?php
		}
	}
} */ 

function ja_header() {
		?>
		<div class="ja_header clearfix">
			<div class="logo">
			<p><a href="/"><img src="http://www.journalismaccelerator.com/wp-content/uploads/2011/01/ja_logo_1.png"  alt="Journalism Accelerator Logo" width="450" height="46" /></a></p>
			<p><a href="/">A forum about innovation in journalism, beyond the usual suspects</a></p>
			</div>
			<form method="get" class="search_form" id="search_form" action="/"> 
		
	<p> 
		<input class="text_input" type="text" name="s" id="s" value="<?php echo $_GET['s'];?>" /> 
		<input type="submit" id="searchsubmit" value="Search" /> 
		<a class="find_people" href="/members/">Find People</a> 
	</p> 	

			</form>
	</div>
		<?php
}

function dp_recent_comments($no_comments = 10, $comment_len = 100) {
	global $wpdb;
	$request = "SELECT * FROM $wpdb->comments";
	$request .= " JOIN $wpdb->posts ON ID = comment_post_ID";
	$request .= " WHERE comment_approved = '1' AND post_status = 'publish' AND post_password =''";
	$request .= " ORDER BY comment_date DESC LIMIT 4";
	$comments = $wpdb->get_results($request);
	if ($comments) {
		foreach ($comments as $comment) {
			ob_start();
			?><div class="answer">
				<p class="category"></p>
				<h2>A:</h2>
				<p><a href="<?php comment_author_url($comment->comment_ID); ?>"><?php 
				echo comment_author($comment->comment_ID); ?></a><?php
				echo ' on ' ;
				$category = get_the_category($comment->comment_post_ID);
				?>
				<a href="<?php echo get_permalink($comment->comment_post_ID); ?>"><?php echo $category[0]->cat_name; ?></a>: <?php

		// trim the string
				if (strlen($comment->comment_content) > 100) {
					echo substr( strip_tags( $comment->comment_content ), 0, 100  )."...";
				}
				else {
					echo strip_tags($comment->comment_content);
				}
				?>
				</p></div>
				<div class="clear"></div>
			<?php
			ob_end_flush();
		}
	} else {
		echo '<p>Comments not found.</p>';
	}
}

function ja_resource_home() { ?>
	<div class="quadrants" style="margin-bottom: 18px;">		
	
		<div class="home_box_left">
		  <div class="popular_questions">
					<div class="box_content">
						<h2>Latest Resource Listings</h2>
						<?php latest_listings(); ?>
					</div>
				</div>
		</div>
		
		<div class="home_box_right">
		  <div class="popular_answers">
					<div class="box_content">
						<h2>Latest Resource Comments</h2>
						<?php dp_recent_resource_comments(); ?>
					</div>
				</div>
		</div>
		
		<div class="clear"></div>
	</div><?php
	$wp_query->is_category = true;
	$wp_query->is_archive = true;
	$wp_query->is_home = false;
}


function dp_recent_resource_comments() {
	global $wpdb;
	$request = "SELECT * FROM $wpdb->comments";
	$request .= " JOIN $wpdb->posts ON ID = comment_post_ID  JOIN $wpdb->term_relationships ON (ID = $wpdb->term_relationships.object_id) JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)";
	$request .= " WHERE comment_approved = '1' AND post_status = 'publish' AND post_password ='' AND $wpdb->term_taxonomy.taxonomy = 'category' AND $wpdb->term_taxonomy.term_id IN(25)";
	$request .= " ORDER BY comment_date DESC LIMIT 4";

	$comments = $wpdb->get_results($request);
	if ($comments) {
		foreach ($comments as $comment) { 
		
		$commentdate = $comment->comment_date;
		$formatdate = date("l, F j", strtotime($commentdate));
		?>
		
			<div class="resource answer clearfix">
				<p class="comment-date"><?php echo $formatdate; ?></p>
				
					
				<?php 
					if ( $comment->user_id ) :
						$email = $comment->comment_author_email;

				?>
				
				<a href="<?php echo get_link_to_public_profile($email); ?>" rel="nofollow">
						<?php 
						$id =  get_user_id_from_string($email); // retrieve id from an email, defined in /plugins/rpx_override/rpx-override.php
						echo bp_core_fetch_avatar( array( 'item_id' => $id, 'width' => 50, 'height' => 50, 'email' => $comment->comment_author_email ) ); 
						?>
					<?php else : ?>
						<?php 
						$comment_tweet_avatar   = get_comment_meta($comment->comment_ID, 'tmac_image',true);
						if($comment_tweet_avatar) : 
						echo "<img class='avatar avatar-48 photo' src='$comment_tweet_avatar' />";
						else : 
						 $real_user_id = get_user_id_from_string($comment->comment_author_email);
						 echo get_avatar($real_user_id,48);
						 endif;
						?>
					<?php endif; ?>
								

				</a>
	<p class="title" style="font-size: 16px; padding-left: 60px;"><a href="<?php echo  get_link_to_public_profile($email); ?>"><?php echo $comment->comment_author; ?></a> on <a href="<?php echo get_permalink($comment->comment_post_ID); ?>#comment-<?php echo $comment->comment_ID; ?>"><?php echo $comment->post_title; ?></a></p>
	<div class="comment-text clearfix">
<?php

	 ?><p style="margin-left: 60px;"><?php
				// trim the string
				if (strlen($comment->comment_content) > 250) {
					echo substr( strip_tags( $comment->comment_content ), 0, 250  )."...";
				}
				else {
					echo strip_tags($comment->comment_content);
				}
	
?></p>
</div>
			</div><?php
		}
	}
}

function dp_recent_all_comments() {
	global $wpdb;
	$request = "SELECT * FROM $wpdb->comments";
	$request .= " JOIN $wpdb->posts ON ID = comment_post_ID";
	$request .= " WHERE comment_approved = '1' AND post_status = 'publish' AND post_password =''";
	$request .= " ORDER BY comment_date DESC LIMIT 4";

	$comments = $wpdb->get_results($request);
	if ($comments) {
		foreach ($comments as $comment) { 
		
		$commentdate = $comment->comment_date;
		$formatdate = date("l, F j", strtotime($commentdate));
		?>
		
			<div class="resource answer clearfix">
				<p class="comment-date"><?php echo $formatdate; ?></p>
				<?php 
					if ( $comment->user_id ) :
						$email = $comment->comment_author_email;
				?>
				<a href="<?php echo get_link_to_public_profile($email); ?>" rel="nofollow">
						<?php 
						
						//$email =  get_comment_author_email();
						
						$id =  get_user_id_from_string($email); // retrieve id from an email, defined in /plugins/rpx_override/rpx-override.php
						echo bp_core_fetch_avatar( array( 'item_id' => $id, 'width' => 50, 'height' => 50, 'email' => $comment->comment_author_email ) ); 
						?>
					<?php else : ?>
						<?php 
						$comment_tweet_avatar   = get_comment_meta($comment->comment_ID, 'tmac_image',true);
						if($comment_tweet_avatar) : 
						echo "<img class='avatar avatar-48 photo' src='$comment_tweet_avatar' />";
						else : 
						 $real_user_id = get_user_id_from_string($comment->comment_author_email);
						 echo get_avatar($real_user_id,48);
						 endif;
						?>
					<?php endif; ?>
								

				</a>
	<p class="title" style="font-size: 16px; padding-left: 60px;"><a href="<?php echo get_link_to_public_profile($email); ?>"><?php echo $comment->comment_author; ?></a> on <a href="<?php echo get_permalink($comment->comment_post_ID); ?>#comment-<?php echo $comment->comment_ID; ?>"><?php echo $comment->post_title; ?></a></p>
	<div class="comment-text clearfix">
<?php

	 ?><p style="margin-left: 60px;"><?php
				// trim the string
				if (strlen($comment->comment_content) > 250) {
					echo substr( strip_tags( $comment->comment_content ), 0, 250  )."...";
				}
				else {
					echo strip_tags($comment->comment_content);
				}
	
?></p>
</div>
			</div><?php
		}
	}
}

function latest_listings() {
	$latest_listings = new WP_Query();
	$latest_listings->query('&cat=25,-445,-16&showposts=4');
	while ( $latest_listings->have_posts() ) {
		$latest_listings->the_post();
		global $post;
		$postdate = $post->post_date;
		$formatdate = date("l, F j", strtotime($postdate));
		?>
		<div class="resource question clearfix">
			<p class="post-date"><?php echo $formatdate; ?></p>
			<p class="title"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></p>
			<div class="excerpt-text"><?php 
				if($tout = get_post_meta($post->ID, 'tout', true))
					echo $tout;
				else
					the_excerpt(); 
			?></div>
		</div>
		<?php
	}
}

/* function all_comments($no_comments = 10, $comment_len = 100) {
    global $wpdb;
    $request = "SELECT * FROM wp_comments JOIN wp_posts ON ID = comment_post_ID WHERE comment_approved = '1' AND post_status = 'publish' AND post_password ='' ORDER BY comment_date DESC LIMIT 1000";

    $comments = $wpdb->get_results($request);
    if ($comments):
        foreach ($comments as $comment) {
            ob_start();
            ?><div class="post"><h2 class="posttitle"><a href="<?php echo get_permalink($comment->comment_post_ID); ?>"><?php

            // trim the string
            if (strlen($comment->comment_content) > 100):
                echo strip_tags(substr(apply_filters('get_comment_text', $comment->comment_content), 0, 99)) . "...";
            else:
                echo strip_tags($comment->comment_content);
            endif;

            ?></a></h2><div class="archive-info"><p>Posted by <?php comment_author_profile($comment); ?> on <?php comment_date('F j, Y', $comment); ?> in <?php strip_tags(the_category(', ', null, $comment->comment_post_ID)) ?></p><?php wp_gdsr_render_comment($comment); ?></div></div><?php
            ob_end_flush();
        }
     else:
        echo '<p>Comments not found.</p>';
     endif;

} */

function user_page() {
	global $bp;
	$username = $bp->loggedin_user->fullname;
	$userpage = $bp->loggedin_user->domain;
	echo "<a href=\"" . $userpage . "\">" . $username . "</a>";
}

if ( function_exists('register_sidebar') ) {
	register_sidebar(array(
	'name' => 'Footer',
	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	'after_widget' => '</div>',
	'before_title' => '<h2>',
	'after_title' => '</h2>',
));
}

function active_tab($category) {
	if (is_category($category)) {
		echo 'class="activetab"';
	}
}

function add_ja_login_form() {
	if (!is_user_logged_in()) {
	?>
	<div class="login-left">
		<h2>Login:</h2>

                <?php do_action( 'bp_after_sidebar_login_form' ) ?>
		<form name="login-form" id="page-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
			<label><?php _e( 'Username', 'buddypress' ) ?><br />
			<input type="text" name="log" id="page-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" /></label>

			<label><?php _e( 'Password', 'buddypress' ) ?><br />
			<input type="password" name="pwd" id="page-user-pass" class="input" value="" tabindex="98" /></label>

			<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="page-rememberme" value="forever" tabindex="99" /> <?php _e( 'Remember Me', 'buddypress' ) ?></label></p>

			<?php do_action( 'bp_sidebar_login_form' ) ?>
			<input type="submit" name="wp-submit" id="page-wp-submit" value="<?php _e('Log In'); ?>" tabindex="100" />
			<input type="hidden" name="testcookie" value="1" />
		</form>
	</div>
	<div class="login-right">
		<h2>Or create an account:</h2>
		<p>You can connect using your social media account on the left or <a href="#">click here to create an account</a>.</p>
	</div>
	<div class="clear"></div>
		<?php
		}
		else {
			echo "Already logged in!";
		}
}

add_shortcode('add_login_form', 'add_ja_login_form');

function remove_menus () {
	if (!current_user_can('install_themes')) {
			global $menu;
			$restricted = array(__('Pages'));
			end ($menu);
			while (prev($menu)){
				$value = explode(' ',$menu[key($menu)][0]);
				if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
			}
	}
}
add_action('admin_menu', 'remove_menus');

function post_is_in_descendant_category( $cats, $_post = null )
{
	foreach ( (array) $cats as $cat ) {
		// get_term_children() accepts integer ID only
		$descendants = get_term_children( (int) $cat, 'category');
		if ( $descendants && in_category( $descendants, $_post ) )
			return true;
	}
	return false;
}

function vp_get_thumb_url($text)
{
  global $post;
 
  $imageurl="";        
 
  // extract the thumbnail from attached imaged
  $allimages =&get_children('post_type=attachment&post_mime_type=image&post_parent=' . $post->ID );        
 
  foreach ($allimages as $img){                
     $img_src = wp_get_attachment_image_src($img->ID);
     break;                       
  }
 
  $imageurl=$img_src[0];
 
 
  // try to get any image
  if (!$imageurl)
  {
    preg_match('/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'>]*)/i' ,  $text, $matches);
    $imageurl=$matches[1];
  }
 
  // try to get youtube video thumbnail
  if (!$imageurl)
  {
    preg_match("/([a-zA-Z0-9\-\_]+\.|)youtube\.com\/watch(\?v\=|\/v\/)([a-zA-Z0-9\-\_]{11})([^<\s]*)/", $text, $matches2);
 
    $youtubeurl = $matches2[0];
    if ($youtubeurl)
     $imageurl = "http://i.ytimg.com/vi/{$matches2[3]}/1.jpg"; 
   else preg_match("/([a-zA-Z0-9\-\_]+\.|)youtube\.com\/(v\/)([a-zA-Z0-9\-\_]{11})([^<\s]*)/", $text, $matches2);
 
   $youtubeurl = $matches2[0];
   if ($youtubeurl)
     $imageurl = "http://i.ytimg.com/vi/{$matches2[3]}/1.jpg"; 
  }
 
 
return $imageurl;
}

add_action("login_head", "my_login_head");
function my_login_head() {
	echo "
	<style>
	body.login #login h1 a {
		background: url('http://www.journalismaccelerator.com/ja-login-banner.png') no-repeat scroll center top transparent;
		height: 33px;
		width: 320px;
	}
	</style>
	";
}

add_filter('login_headerurl', create_function(false,"return '/';"));

/**
* Add meta panel for resources
*/

include_once 'wpalchemy/MetaBox.php';
include_once 'wpalchemy/MediaAccess.php';
if (is_admin()) { wp_enqueue_style('custom_meta_css', get_bloginfo('stylesheet_directory') . '/meta.css'); }
$wpalchemy_media_access = new WPAlchemy_MediaAccess();
$resource_metabox = new WPAlchemy_MetaBox(array
(
	'id' => '_resource_meta',
	'title' => 'Resource Fields',
	'template' => STYLESHEETPATH . '/resource_meta.php',
	'priority' => 'high',
	'context' => 'side',
	'autosave' => FALSE
));

function custom_resource_tout() {
	global $resource_metabox;
	if ($resource_metabox->get_the_value('tout')) {
		echo "<div class=\"resource-tout\">" . $resource_metabox->get_the_value('tout') . "</div>"; 
	}
}

function custom_resource_fields() {
	global $resource_metabox;
	if ($resource_metabox->the_meta()) { ?>
	<?php
	$resource_metabox->the_meta();
	if ($resource_metabox->get_the_value('logo') || $resource_metabox->have_fields('links')) {
	$resource_metabox->the_field('logo');
	$resource_image_size = getimagesize($resource_metabox->get_the_value());
	if (($resource_image_size[0]/$resource_image_size[1]) > 1 && !$resource_metabox->get_the_value('force-left')) $aspect = 'wide';
	elseif (($resource_image_size[0]/$resource_image_size[1]) <= 1 || $resource_metabox->get_the_value('force-left')) $aspect = 'tall';
	?>
	<div class="resource-box <?php echo $aspect; ?>">
	<img src="<?php $resource_metabox->the_value(); ?>" class="logo" <?php if ($aspect == 'tall' && $resource_image_size[0] > 75) echo "width=\"75\""; elseif ($aspect == 'wide' && $resource_image_size[0] > 250) echo "width=\"250\""; ?> />
	<?php
	if($resource_metabox->get_the_value('box_title')) { 
		$box_title = $resource_metabox->get_the_value('box_title');
		echo "<h3>$box_title</h3>";
	} ?> 
	<ul>
	<li><span class="field-header"><strong>Links:</strong></span> <span class="field-data"><?php
	$resource_links = Array();
	while ($resource_metabox->have_fields('links')) {
		$link_title = $resource_metabox->get_the_value('title');
		$link_url = $resource_metabox->get_the_value('url');
		array_push($resource_links, "<a href=\"$link_url\" target=\"_blank\">$link_title</a>");
	}
	echo implode(' | ', $resource_links);
	?></span></li>
	<?php if($resource_metabox->get_the_value('people')) { ?>
	<li><span class="field-header"><strong>People:</strong></span> <span class="field-data"><?php
	$people_links = Array();
	while ($resource_metabox->have_fields('people')) {
		$person_title = $resource_metabox->get_the_value('name');
		$person_url = $resource_metabox->get_the_value('url');
		array_push($people_links, "<a href=\"$person_url\">$person_title</a>");
	}
	echo implode(' | ', $people_links);
	?></span></li>
	<?php } ?>
	<li><span class="field-header"><strong>Tags:</strong></span> <span class="field-data"><?php the_tags('', ', '); ?></span></li>
	</div><?php } }
}

function comment_count( $count ) {
	if ( ! is_admin() ) {
		global $id;
		$comments_by_type = &separate_comments(get_comments('status=approve&post_id=' . $id));
		return count($comments_by_type['comment']);
	} else {
		return $count;
	}
}
add_filter('get_comments_number', 'comment_count', 0);

/**
* convert long integer into American English words.
* e.g. -12345 -> "minus twelve thousand forty-five"
* Handles negative and positive integers
* on range -Long.MAX_VALUE .. Long.MAX_VALUE;
* It cannot handle Long.MIN_VALUE;
*/

function num2words( $num ){
    $ZERO = "zero";
    $MINUS = "minus";
    $lowName = array(
          /* zero is shown as "" since it is never used in combined forms */
          /* 0 .. 19 */
          "", "one", "two", "three", "four", "five",
          "six", "seven", "eight", "nine", "ten",
          "eleven", "twelve", "thirteen", "fourteen", "fifteen",
          "sixteen", "seventeen", "eighteen", "nineteen");

    $tys = array(
          /* 0, 10, 20, 30 ... 90 */
          "", "", "twenty", "thirty", "forty", "fifty",
          "sixty", "seventy", "eighty", "ninety");

    $groupName = array(
          /* We only need up to a quintillion, since a long is about 9 * 10 ^ 18 */
          /* American: unit, hundred, thousand, million, billion, trillion, quadrillion, quintillion */
          "", "hundred", "thousand", "million", "billion",
          "trillion", "quadrillion", "quintillion");

    $divisor = array(
          /* How many of this group is needed to form one of the succeeding group. */
          /* American: unit, hundred, thousand, million, billion, trillion, quadrillion, quintillion */
          100, 10, 1000, 1000, 1000, 1000, 1000, 1000) ;

    $num = str_replace(",","",$num);
    $num = number_format($num,2,'.','');
    $cents = substr($num,strlen($num)-2,strlen($num)-1);
    $num = (int)$num;

    $s = "";

    if ( $num == 0 ) $s = $ZERO;
    $negative = ($num < 0 );
    if ( $negative ) $num = -$num;

    // Work least significant digit to most, right to left.
    // until high order part is all 0s.
    for ( $i=0; $num>0; $i++ ) {
        $remdr = (int)($num % $divisor[$i]);
        $num = $num / $divisor[$i];
        // check for 1100 .. 1999, 2100..2999, ... 5200..5999
        // but not 1000..1099,  2000..2099, ...
        // Special case written as fifty-nine hundred.
        // e.g. thousands digit is 1..5 and hundreds digit is 1..9
        // Only when no further higher order.
        if ( $i == 1 /* doing hundreds */ && 1 <= $num && $num <= 5 ){
            if ( $remdr > 0 ){
                $remdr += $num * 10;
                $num = 0;
            } // end if
        } // end if
        if ( $remdr == 0 ){
            continue;
        }
        $t = "";
        if ( $remdr < 20 ){
            $t = $lowName[$remdr];
        }
        else if ( $remdr < 100 ){
            $units = (int)$remdr % 10;
            $tens = (int)$remdr / 10;
            $t = $tys [$tens];
            if ( $units != 0 ){
               $t .= "-" . $lowName[$units];
            }
        }else {
            $t = $inWords($remdr);
        }
        $s = $t . " " . $groupName[$i] . " "  . $s;
        $num = (int)$num;
    } // end for
    $s = trim($s);
    if ( $negative ){
        $s = $MINUS . " " . $s;
    }

    // $s .= " and $cents/100";

    return $s;
} // end inWords


// Change Search sql request so that it looks for wp_user.display_name instead 
add_filter('bp_core_get_paged_users_sql', 'update_search_request');
function update_search_request($param){
	$param = str_replace('pd.value', 'u.display_name', $param);
	return $param;	
}

/* COPIED FROM bp-core-templatetags.php (function name sligtly changed to avoid name conflicts), 
called from /members/index.php */

function ja_bp_directory_members_search_form() {
	global $bp;

	$search_value = __( 'Search people', 'buddypress' );
	if ( !empty( $_GET['s'] ) )
	 	$search_value = $_GET['s'];

	?>
	<form action="" method="get" id="search-members-form">
		<label><input type="text" name="s" id="members_search" value="<?php echo esc_attr( $search_value ) ?>"  onfocus="if (this.value == '<?php _e( 'Search people', 'buddypress' ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Search people', 'buddypress' ) ?>';}" /></label>
		<input type="submit" id="members_search_submit" name="members_search_submit" value="<?php _e( 'Search', 'buddypress' ) ?>" />
	</form>
<?php
}

function parent_category_is($cat) {
	$categories = get_the_category();
	foreach ($categories as $category) {
		if ( $category->category_parent == $cat ) return true;
	}
}

function in_slug($par){
	$tmp = explode('/', $_SERVER['REQUEST_URI']);
	if(array_search($par, $tmp)) return true;
	else return false;
}

/* MENUS */

register_nav_menu( 'questions', 'Question Categories' );
register_nav_menu( 'resources', 'Resource Categories' );
register_nav_menu( 'blog', 'Blog Categories' );


function get_post_bitly( $post_id ){
	$bitly = get_post_meta( $post_id, 'bitly_url', true);
	if( empty($bitly) ){
		$bitly = make_bitly_url( get_permalink( $post_id ) , 'thejabitly', 'R_dad370cd7d098deaf876974d13b49cf4'  );
		update_post_meta( $post_id, 'bitly_url', $bitly);
	}
	return $bitly;
}



function make_bitly_url($url,$login, $appkey, $format = 'xml', $version = '2.0.1') {
  $bitly = 'http://api.bit.ly/shorten?version='.$version.'&longUrl='.urlencode($url).'&login='.$login.'&apiKey='.$appkey.'&format='.$format;
  $response = @file_get_contents($bitly);
  if(strtolower($format) == 'json') {
    $json = @json_decode($response,true);
    return $json['results'][$url]['shortUrl'];
  }
  else {
    $xml = simplexml_load_string($response);
    return 'http://bit.ly/'.$xml->results->nodeKeyVal->hash;
  }
}	


//.............................................................................
// 									Ajax Frontend Controller 			     //
//.............................................................................
add_action('wp_ajax_frontend', 'ajax_frontend');
add_action('wp_ajax_nopriv_frontend', 'ajax_frontend');
function ajax_frontend(){
	include_once('ajax/front_end.php');
	exit;
}


//.............................................................................
// 									ADD IF IE CLASS 					     //
//.............................................................................

function ie_open() {
	?>
	<!--[if lte IE 8]><div class="ie"><![endif]-->
	<!--[if IE 9]><div class="ie9"><![endif]-->
	<?php
}

function ie_close() {
	?>
	<!--[if lte IE 8]></div><![endif]-->
	<!--[if IE 9]></div><![endif]-->
	<?php
}

add_action('bp_before_header','ie_open', 1);
add_action('bp_after_footer','ie_close', 1);

//.............................................................................
//	ADD THE ABILITY TO USE SHORTCODES IN THE WORDPRESS TEXT WIDGETS
//.............................................................................

add_filter( 'widget_text', 'shortcode_unautop');
add_filter( 'widget_text', 'do_shortcode');


function getPostViews($postID){
$count_key = 'post_views_count';
$count = get_post_meta($postID, $count_key, true);
if($count==''){
    delete_post_meta($postID, $count_key);
    add_post_meta($postID, $count_key, '0');
    return "0 View";
}
return $count.' Views';
}
function setPostViews($postID) {
$count_key = 'post_views_count';
$count = get_post_meta($postID, $count_key, true);
if($count==''){
    $count = 0;
    delete_post_meta($postID, $count_key);
    add_post_meta($postID, $count_key, '0');
}else{
    $count++;
    update_post_meta($postID, $count_key, $count);
}
}

function bp_author_link($author_ID) {

	// Get author's data BuddyPress style
	$author = new BP_Core_User( $author_ID );

	echo '<a href="' . $author->user_url . '">';
	echo $author->avatar_mini;
	echo ' ' . $author->fullname;
	echo '</a><p>';
	echo $author->profile_data['One-Line Bio']['field_data'];
	echo '</p><hr>';
}