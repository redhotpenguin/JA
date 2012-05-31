<?php

function get_post_participants($post_id, $limit = "", $unique = false, $random = false){
	global $wpdb;
	
	if(!empty($limit))
		$limit = "limit 0, $limit";
	
	if($random)
		$order = 'rand()';
	else
		$order = 'comment_date desc';
	
	if($unique)
		$unique = 'DISTINCT(user_id)';
	else $unique = 'user_id';
	
    $post_participants_query = "SELECT $unique
	FROM $wpdb->comments 
	WHERE comment_post_ID = $post_id
	AND comment_type = ''
	AND comment_approved = 1
	ORDER BY $order $limit";
	
	$participant_ids = $wpdb->get_results($post_participants_query, ARRAY_N);
	
	if(empty($participant_ids))
		return false;
	else return array_map('purple_p_extract_first_el', $participant_ids );
}

function purple_p_get_last_comment_by_user($user_id, $post_id){
	return get_comments(array(
		'number' => 1,
		'post_id' => $post_id,
		'user_id' => $user_id,
		'order' => 'DESC'
	));
}

function purple_p_extract_first_el($a){
		return $a[0] ;
}

function purple_p_word_trim($string, $count, $ellipsis = ' ...'){
  $words = explode(' ', $string);
  if (count($words) > $count){
    array_splice($words, $count);
    $string = implode(' ', $words);
    if (is_string($ellipsis)){
      $string .= $ellipsis;
    }
    elseif ($ellipsis){
      $string .= '&hellip;';
    }
  }
  return $string;
}

?>