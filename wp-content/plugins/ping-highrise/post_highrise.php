
<?php
ignore_user_abort(true);


define('WP_USE_THEMES', false);


require($_SERVER['DOCUMENT_ROOT'].'/'.'wp-blog-header.php');
$_POST = stripslashes_deep($_POST);

if( !isset($_REQUEST['action']) ) 
	exit;

switch( $_REQUEST['action'] ){
	case 'process_comments': 
		$_key = $_REQUEST['k'];
		if( $_key === get_option('highrise_cron_key') ){
			ph_log('CRON JOB Starting');
			process_comments(); 
		}
		else{
                    echo 'Wrong Key: '.$_key.'. This incident was <b>reported</b>.';
                    error_log('Ping Highrise CRON: Wrong Key '.$_key);
		}
		break;
	
	case 'new_user':
		notify_new_user();
		break;
	
	default: exit;
}
 


/****************************************************************************************/
//							CRON																		//
/****************************************************************************************/
function process_comments(){
	global $wpdb;
	global $hr_url;
	global $hr_token;
	
	
	 $hr_url = get_option('highrise_url');
	 $hr_token = get_option('highrise_token');


	$comment_ids = get_comment_ids_for_processing();

	if( empty( $comment_ids ) ){ 
		ph_log('Nothing to Process.');
		return false;
	}
	
	ph_log('Start Processing Comments');

	foreach ( $comment_ids as $comment_id  ){
		 $comment_id =  $comment_id[0];
		
		if(  notify_new_comment( $comment_id ) ){
			ph_log("Comment $comment_id Pushed to Highrise ");
			update_phighrise( $comment_id, 'comment', 'processed', 2 );
		}
		else{
			ph_log("Could'not Push Comment $comment_id to Highrise");
			update_phighrise( $comment_id, 'comment', 'failed', 1 );
		}
	}

	ph_log('Done Processing.');
	return true;
	
}

function update_phighrise( $uc_id, $type, $status, $code ){

	global $wpdb;

	return $wpdb->update('wp_phighrise', 
			array(
				'type'=> $type,
				'status' => $status,
				'code' => $code,
				'time' => current_time('mysql')	
			),
			
			array( 	'uc_id' => $uc_id )
		);
}


/****************************************************************************************/
//											WP INTEGRATION 								//
/****************************************************************************************/

function notify_new_comment( $comment_id ){

	ph_log('Post Highrise: new comment id: '.$comment_id);
	
	
	$comment_obj = get_comment($comment_id);
	if( empty($comment_obj->comment_content) )
		return false;
		
	$post_obj = get_post($comment_obj->comment_post_ID);
	
	$user_id = $comment_obj->user_id;
	$hr_user_id = get_user_meta($user_id, 'hr_user_id', true);
	
	$categories = get_the_category($comment_obj->comment_post_ID);

	$category_list = array();
	foreach($categories as $cat){
		array_push($category_list, $cat->category_nicename );
	}
	
	$comment = array();
	$comment['post_title'] = $post_obj->post_title;
	$comment['post_link'] = get_permalink($comment_obj->comment_post_ID);
	$comment['post_category'] = $category_list;
	
	$anchor_pattern = '/<a[^>]*?href=[\'"](.*?)[\'"][^>]*?>(.*?)<\/a>/si' ;	
	
	$comment['content'] = preg_replace($anchor_pattern, '${1}',  $comment_obj->comment_content);

	$comment['comment_link'] = get_comment_link($comment_obj);
	$comment['comment_date'] = $comment_obj->comment_date;

	$push_result = push_comment($hr_user_id, 'Wrote a Comment on "'.$comment['post_title'].'":', $comment);
	if($push_result) return true;
	else return false;
}

function notify_new_user(){
	global $hr_url;
	global $hr_token;
	global $user_obj;
	
	$hr_url =  $_POST['hr_url'];
	$hr_token =  $_POST['hr_token'];
	$user_id = $_POST[user_id];
	$assignee =  get_option('tasks_user_id');
	$tag_user_as = get_option('tasks_user_tag');

	$user = populate_user($user_id); //prepare $user[]
	
	$user_hr_id = get_hr_id_by_email($user['email']);
	$ja_profile = get_link_to_public_profile($user_id); 
	if( empty($user_hr_id) ) { // contact is not stored in Highrise
		if( $user_hr_id = push_contact($user) ){
			$task_category = get_option('highrise_task_category');
			$note = "New Highrise Profile Created: $hr_url/people/$user_hr_id";
		}
	}
	else{ // contact is already stored in Highrise
		update_contact($user_hr_id, $user);
		
		$note =  "Highrise Profile Linked to JA Profile: $ja_profile";
		$task_category = get_option('highrise_task_category_updated_contact');
	}
	
	$tag_list = explode(',', $tag_user_as);
	foreach($tag_list as $tag){
		push_tag($user_hr_id, $tag);
	}
	update_user_meta($user_id, 'hr_user_id', (string) $user_hr_id);
	push_note($user_hr_id, $note , '');
	push_task($assignee, $user_hr_id, $task_category);
	
	
	if( function_exists('rpx_send_admin_notification') ){
	$res = rpx_send_admin_notification($user_obj);
	}
}

function populate_user($user_id){
	global $user_obj;
	$user = array();
	$user_obj =  get_userdata($user_id);
	if( empty($user_obj) )
		return false;
		
	$twitter = xprofile_get_field_data('Twitter' ,$user_id);
	
	if( empty($user_obj->first_name) )
		$user['first-name'] = $user_obj->display_name;  // use display_name when empty
	
	else 
		$user['first-name'] = $user_obj->first_name;
	
	$user['last-name'] = $user_obj->last_name;
	
	$user['email'] = $user_obj->user_email;
	$user['avatar'] = get_user_meta($user_id, 'rpx_photo', true);
	$user['background'] = xprofile_get_field_data('One-Line Bio' ,$user_id);
	$user['user_registered'] = current_time('mysql');
	$user['user_id'] = $user_id;

	$websites = array( // google and facebook 
			get_user_meta($user_id, 'rpx_url', true)
	);
			
	if( $user_obj->rpx_provider != 'Twitter' ){ //avoid Twitter showing up twice
		array_push($websites, $twitter);
	}
	
	$tmp_website = xprofile_get_field_data('Website' ,$user_id);
	if( get_user_meta($user_id, 'rpx_url', true) != $tmp_website ){
		array_push($websites, $tmp_website ); //avoid FB showing up twice
	}
	
	array_push($websites, get_link_to_public_profile($user_id) );
	array_push($websites, get_site_url().get_edit_link($user_id) );
		
	$user['websites'] = $websites;	
	$websites = array_unique($websites);

	
	if($user_obj->rpx_provider == 'LinkedIn') { 
		$user['linkedin'] = get_user_meta($user_id, 'linkedin', true);
	}
	
	if($twitter) 
		$user['twitter'] = get_twitter_handle( $twitter );
	
	return $user;
}


/****************************************************************************************/
//											API CALLS 									//
/****************************************************************************************/

function push_contact($user){
	if(empty($user) ) return false;
	$xml_query = '<person>
			<first-name>'.$user['first-name'].'</first-name>
			<last-name>'.$user['last-name'].'</last-name>
			<background>'.$user['background'].'
			Registered on: '.$user['user_registered'].'
			JA User ID: #'.$user['user_id'].'</background>
			<contact-data>
					<email-addresses>
						<email-address>
							<address>'.$user['email'].'</address>
							<location>Work</location>
						</email-address>
					</email-addresses>
				   <web-addresses>';
		
			foreach($user['websites'] as $website){
			if(empty($website)) continue;
				$xml_query.= '	<web-address>
									<url>'.$website.'</url>
									<location>Other</location>
								</web-address>';
			}						
			$xml_query.= '</web-addresses>';
	
			if( !empty($user['twitter']) ){
			 $xml_query.='<twitter-accounts>
				<twitter-account>
				  <location>Personal</location>
				  <username>'.$user['twitter'].'</username>
				  <url>'.$user['twitter'].'</url>
				</twitter-account>
			  </twitter-accounts>';
			 }
			
		$xml_query.= '</contact-data>
		</person>';

	$hr_user = do_push('people.xml', $xml_query );
	$hr_user_id = $hr_user->id;
	
	if($hr_user_id) return $hr_user_id;
	else error_log('Could not push a user to Highrise');
}

function update_contact($hr_user_id, $user){
	$update_query='<person>

  <contact-data>
  <background>'.$user['background'].'
Registered on: '.$user['user_registered'].'
JA User ID: #'.$user['user_id'].'
   </background>

	<web-addresses>';
		
			foreach($user['websites'] as $website){
			if(empty($website)) continue;
				$update_query.= '<web-address>
									<url>'.$website.'</url>
									<location>Other</location>
								</web-address>';
			}						
			$update_query.= '</web-addresses>';
			
	if( !empty($user['twitter']) ){
			 $update_query.='<twitter-accounts>
				<twitter-account>
				  <location>Personal</location>
				  <username>'.get_twitter_handle($user['twitter']).'</username>
				  <url>'.$user['twitter'].'</url>
				</twitter-account>
			  </twitter-accounts>';
			 }		
			
  $update_query.= '</contact-data> </person>';

	$ret = do_push("people/$hr_user_id.xml?reload=true", $update_query, "PUT");

}

function push_task($assignee_user_id, $subject_id, $task_category){
	if( empty($assignee_user_id) || empty($subject_id) || empty($task_category) ) return false;
	
	$task_query = '<task>
		<subject-type>Party</subject-type>
		<subject-id type="integer">'.$subject_id.'</subject-id>
		<category-id type="integer">'.$task_category.'</category-id>
		<body> Review Profile </body>
		<frame>today</frame>
		<owner-id type="integer">'.$assignee_user_id.'</owner-id>
		<public type="boolean">true</public>
		<notify type="boolean"> true </notify>
	</task>';
	
	$xml_result = do_push('tasks.xml', $task_query );
}
 
function push_tag($hr_user_id, $tag_name){
	if( empty($hr_user_id) || empty($tag_name) ) return false;
		$tags_xml = '<name>'.$tag_name.'</name>';

		$tag_result = do_push('people/'.$hr_user_id.'/tags.xml' , $tags_xml);
		$tag_id = $tag_result->id;
		if($tag_id) return $tag_id;
		else return false;
}

function push_comment($hr_user_id, $subject, $content){
	if(empty($hr_user_id) || empty($subject) || empty($content)) return false;

	ph_log('Post Highrise: push_comment');
		
	$xml_query = '<note>
		<subject-id type="integer">'.$hr_user_id.'</subject-id>
		<subject-type>Party</subject-type>
		<body>'.$subject.'
		
		'.strip_tags ( $content['content'] ) .' 
		
		On '.$content['comment_date'].' 
		
		Link to the comment: '.$content['comment_link'].' 
		
		Link to the Post: '.$content['post_link'].'
		
		Categories: /'; 
		$categories = $content['post_category'];
		foreach( $categories as $category){
			$xml_query.= "$category/";
		}
		
		$xml_query.='</body>
		<visible-to>Everyone</visible-to>
	</note>';
	


	$comment_result = do_push('notes.xml', $xml_query);
	

	
	$comment_result_subject_id = (string) $comment_result->{'subject-id'};

	if($hr_user_id == $comment_result_subject_id) return true;
	else return false;
}

function get_hr_id_by_email($email){
	$people = do_push('people/search.xml?criteria[email]='.$email);
	$id = '-1';
	foreach ($people->person as $person ) {
		if($person != null) {
			$id = $person->id;
		}	
	}

	if($id == -1)
		return false;
	else
		return (string)$id;
}	

function push_note($hr_user_id, $subject, $content){
	$xml_query = '<note>
		<subject-id type="integer">'.$hr_user_id.'</subject-id>
		<subject-type>Party</subject-type>
		<body>'.$subject.' 
			'.$content.' 
		</body>
		<visible-to>Everyone</visible-to>
	</note>';
	$note_result = do_push('notes.xml', $xml_query);
}

function do_push($target, $xml_query = '', $method = "POST"){
	global $hr_url;
	global $hr_token;
	
	if( empty($hr_url) || empty($hr_token) || empty($target)  ) return false;
	$curl = @curl_init($hr_url.'/'.$target);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERPWD, $hr_token.':x');
		
	if(!empty($xml_query)){
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/xml"));
		curl_setopt($curl, CURLOPT_POSTFIELDS, $xml_query);
	}
		
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	
	
	if($method != "POST")
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
	elseif($method == $POST && !empty($xml_query))
		curl_setopt($curl, CURLOPT_POST, true);
		
	
	if($xml_result = @curl_exec($curl)){
		curl_close($curl);
		return $xml_result = @simplexml_load_string($xml_result);
	 }
	 else {
		error_log('do_push: Impossible to Execute Curl Request');
		error_log( curl_error($curl) );
		curl_close($curl);
		return false;
	 }

}


/****************************************************************************************/
//											DEBUG
/****************************************************************************************/

function debugme2($msg, $option = 'a', $filename = 'user.htm' ){
	 $date = date("Y-m-d H:i:s");
	$msg = '<b> '.$date.'</b><pre> '.htmlentities(print_r($msg,true)).'</pre><br/>';



	if (is_writable($filename)) {

		if (!$handle = fopen($filename, $option)) { exit;}
		if (fwrite($handle, $msg) === FALSE) { exit;}
		fclose($handle);
	} 
}


?>
