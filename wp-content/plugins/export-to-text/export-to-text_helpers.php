<?php 
function get_categories_checkboxes( $taxonomy = 'category', $selected_cats = null ) {
	$args = array (
		'taxonomy' => $taxonomy
	);
	$all_categories = get_categories($args);
	
	$o = '<div class="checkbox_box checkbox_with_all"><ul><li class="e2t_all"><label><input class="e2t_all_input" type="checkbox" name="taxonomy['.$taxonomy.'][]" value="e2t_all" checked="yes" /> All</label></li>';
	foreach($all_categories as $key => $cat) {
		if($cat->parent == "0") $o .= __show_category($cat, $taxonomy, $selected_cats);
	}
	return $o . '</ul></div>';
}
function __show_category($cat_object, $taxonomy = 'category', $selected_cats = null) {
	$checked = "";
	if(!is_null($selected_cats) && is_array($selected_cats)) {
		$checked = (in_array($cat_object->cat_ID, $selected_cats)) ? 'checked="checked"' : "";
	}
	$ou = '<li><label><input class="e2t_input" ' . $checked .' type="checkbox" name="taxonomy['.$taxonomy.'][]" value="'. $cat_object->cat_ID .'" /> ' . $cat_object->cat_name . '</label>';
	$childs = get_categories('parent=' . $cat_object->cat_ID);
	foreach($childs as $key => $cat) {
		$ou .= '<ul class="children">' . __show_category($cat, $taxonomy, $selected_cats) . '</ul>';
	}
	$ou .= '</li>';
	return $ou;
}
// get taxonomies terms links
function custom_taxonomies_terms_links() {
	global $post, $post_id;
	// get post by post id
	$post = &get_post($post->ID);
	// get post type by post
	$post_type = $post->post_type;
	// get post type taxonomies
	$taxonomies = get_object_taxonomies($post_type);
	$return = '';
	foreach ($taxonomies as $taxonomy) {
		if( $taxonomy !=  'category' && $taxonomy != 'post_tag' ) {
			// get the terms related to post
			$terms = get_the_terms( $post->ID, $taxonomy );
			if ( !empty( $terms ) ) {
				$return .= $taxonomy.' => ';
				$first = 1;
				foreach ( $terms as $term )
					if($first = 1) {
						$return .= $term->slug;
						$first = 0;
					}
					else {
						$return .= ','.$term->slug;
					}
				$return .= '. ';
			}
		}
	}
	return $return;
}