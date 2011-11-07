<?php

class Janitor_Admin{

	public function __construct(){
		add_action('admin_menu', array(&$this, 'create_admin_menu') );
	}
	
	public function create_admin_menu(){
		    $ico = get_site_url(). '/wp-content/plugins/janitor/janitor.png';
			add_utility_page( 'JAnitor', 'JAnitor', 'remove_users', 'janitor', array(&$this,'admin_user_page'), $ico);
			add_submenu_page( 'janitor', 'Janitor', 'Update User Avatar', 'remove_users', 'janitor',  array(&$this,'admin_user_page'));
			add_submenu_page( 'janitor', 'Comment', 'Reassign Comment', 'remove_users', 'janitor_comment',  array(&$this,'admin_comment_page'));
			add_submenu_page( 'janitor', 'Staff', 'Update Comment Color', 'remove_users', 'janitor_staff',  array(&$this,'admin_staff_page'));

	}
	
	public function admin_comment_page(){
		echo '<div class="wrap" >';
		$process_result =  $this->process_form();
		if($process_result) {echo '<div class="updated">'.$process_result.'</div>';}
		$this->change_comment_content();
		echo '</div>';
	}
	
	public function admin_user_page(){
		echo '<div class="wrap" >';
		$process_result =  $this->process_form();
		if($process_result) {echo '<div class="updated">'.$process_result.'</div>';}
		$this->change_user_avatar_page();
		echo '</div>';
	}
	
	public function admin_staff_page(){
			echo '<div class="wrap" >';
		$process_result =  $this->process_form();
		if($process_result) {echo '<div class="updated">'.$process_result.'</div>';}
		$this->change_staff_content();
		echo '</div>';
	}
	
	private function change_comment_content(){ // Content for Comment Page?>
		<h3> Reassign a Comment </h3>
		<form method="post" action="#">
			<table class="form">
				<tr>
					<td><label for="comment_id">Comment ID</label></td>
					<td><input id="comment_id" size="10" type="text" name="comment_id"/></td>
				</tr>
				
				<tr>	
					<td><label for="user_id">New User ID</label></td>
					<td><input id="user_id" size="10" type="text" name="user_id"/></td>
				</tr>
				
				<tr>
					<td><label>Update User Recent Activity</label></td>
					<td><input  type="checkbox" checked="checked" name="update_ra"/></td>
				</tr>	
				
				<tr>
					<td colspan="2"> 
						<input type="submit" name="change_comment" value="Update Comment"/>
					</td>
				</tr>
			</table>	
		</form>

		<hr/>
		<h3> Reassigned Comments: </h3>
		<div style="word-break: break-word;">
		<table class="widefat">
			<thead class="widefat">
				<tr>
					<th style='min-width:40px; display:block;' >ID</th>
					<th style='min-width:60px;'>User ID</th>
					<th style='min-width:60px;'>Author</th>
					<th style='min-width:170px;'>Email</th>
					<th style='min-width:60px;'>Date</th>
					<th style='min-width:60px;'>Avatar</th>
					<th style='min-width:60px;'>Content</th>
				</tr>
		</thead>
		<?php
			$comments = $this->get_reassigned_comments();
			foreach($comments as $comment){
					$content = strip_tags( substr($comment->comment_content, 0, 400) );
					$comment_link =  get_comment_link($comment);
					$user_profile = get_link_to_public_profile($comment->user_id);
				    $email = $comment->comment_author_email;
					$avatar = get_avatar($comment, 42);
					echo "<tr><th> <a href='$comment_link'> $comment->comment_ID </a> </th>";
					echo "<th><a href='$user_profile'>$comment->user_id </a> </th>";
					echo "<th><a href='$user_profile'>  $comment->comment_author</a> </th>";
					echo "<th> $email </th>";
					echo "<th> $comment->comment_date </th>";
					echo "<th> $avatar </th>";
					echo "<td> $content <a href='$comment_link' target='_blank'>More</a> </td></tr>";
			}
		?>
		</table>
		</div>
		
	<?php
	}
	
	
	private function change_user_avatar_page(){ // Content for Change User Page?>
		<h3>Change User Avatar</h3>
		<form method="post" action="#" enctype="multipart/form-data">
		<table>	
			<tr>
				<td><label for="upload_avatar"> Avatar</label> </td>
				<td>  
					<input id="upload_avatar" name="upload_avatar" type="file" /> 
				</td>
			</tr>
			<tr>
				<td><label for="user_id">User ID</label></td>
				<td><input id="user_id" size="10" type="text" name="user_id"/></td>
			</tr>

			<tr> 
				<td colspan="2"><input type="submit" name="change_avatar" value="Update Avatar"/></td>
			</tr>
		</table>
		</form>
	<?php
	}
	
	public function change_staff_content(){ ?>
		<h3>Update Comment Color</h3>
		<form method="post" action="#">
		<table>	
			<tr>
				<td><label for="user_id">User ID</label> </td>
				<td>  
					<input name="user_id" type="text" /> 
				</td>
			</tr>
		
			<tr>
				<td><label for="header_color">Comment Header Color</label> </td>
				<td>  
					<select name='comment_color'>
						<option value="green" >Green</option>
						<option value="default">Default</option>
					</select>
				</td>
			</tr>
	
			<tr> 
				<td colspan="2"><input type="submit" name="select_comment_color" value="Select Color"/></td>
			</tr>
		</table>
		</form>
		<hr/>
		<h3> User Colors: </h3>
	
		<table class="widefat" style="width:40%; ">
			<thead class="widefat">
				<tr>
					<th> User ID </th>
					<th> Name </th>
					<th> Color </th>
				</tr>
				<?php
					$user_colors = $this->get_users_color();
					foreach($user_colors as $user_color){
						$tmp_user = get_userdata($user_color->user_id);
						echo '<tr>';
							echo "<td>$user_color->user_id</td>";
							echo "<td>$tmp_user->display_name</td>";
							echo "<td>$user_color->meta_value</td>";
						echo '</tr>';
					}
				?>
			</thead>
		</table>
			
		
	<?php
	}
	

	
	private function process_form(){ // Process $_POST requests
	global $wpdb;
		if( isset($_POST['change_avatar']) && !empty($_POST['change_avatar'])){
			$user_id = $_POST['user_id'];
			$upload_avatar = $_FILES['upload_avatar'];
		
			if(empty($user_id) || empty($upload_avatar) ) { return 'All fields are required.'; }
			$user = get_userdata($user_id);
			if( empty($user) ) { return 'Invalid User ID'; }
			
			
			$count = $wpdb->get_var( $wpdb->prepare("
				SELECT COUNT(*) FROM $wpdb->usermeta
				WHERE user_id=%d
				AND meta_key = 'rpx_photo' ", $user_id ));
			$avatar_url = $this->handle_avatar_upload($upload_avatar);	
			if($avatar_url == false) return 'Only JPG, PNG and GIF files are allowed.';
			
			if($count == 0){
				$res = $wpdb->query( $wpdb->prepare("
				INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value)
				VALUES ( %d, %s, %s )", $user_id, 'rpx_photo', $avatar_url));
				wp_cache_flush();
				$avatar = get_avatar($user_id, 32);
				return 'Avatar uploaded: <br/>'.$user->display_name.' ' .$avatar;
			}

			elseif($count == 1){$res = $wpdb->query( $wpdb->prepare("
				UPDATE $wpdb->usermeta SET meta_value=%s
				WHERE user_id=%d
				AND meta_key='rpx_photo' ", $avatar_url, $user_id));
				wp_cache_flush();
				$avatar = get_avatar($user_id, 32);
				return 'Avatar updated: <br/>'.$user->display_name.' '.$avatar;
			}
			else return 'Something went wrong.';
		} // change avatar end
		elseif( isset($_POST['change_comment']) && !empty($_POST['change_comment']) ){ // change comment id 
			  $comment_id = $_POST['comment_id'];
			 
			  $user_id = $_POST['user_id'];
			  $update_ra = $_POST['update_ra'];
			 if(empty($user_id) || empty($comment_id)) { return 'All fields are required.'; }
			 $user = get_userdata($user_id);
			 if( empty($user) ) { return 'Invalid User ID'; }
			 $comment = get_comment($comment_id);
			 if( empty($comment) ) { return 'Invalid Comment ID'; }
			
				$user_email = $user->user_email;
				$user_profile_url = get_link_to_public_profile($user_email);

			 $res = $wpdb->query( $wpdb->prepare("
				UPDATE $wpdb->comments SET
				comment_author=%s,
				comment_author_email=%s,
				comment_author_url=%s, 
				user_ID=%d
				WHERE comment_ID=%d;",$user->display_name , $user_email, $user_profile_url,  $user_id, $comment_id));
			
			if($update_ra){
				$this->update_recent_activity($user_id, $comment_id);
			}
			
			add_comment_meta( $comment_id, 'janitor_reassigned', true, true);	
			$comment = get_comment($comment_id);
			if($res == 0 || $res == 1) return 'Comment Updated';
			else return 'Something went wrong.';
			
		} // change comment  end
		elseif( isset($_POST['select_comment_color'] )){ // change staff for comments header
			$user_id = $_POST['user_id'];
			$user = get_userdata($user_id);
			if( empty($user) ) { return 'Invalid User ID'; }
			
			$color =  $_POST['comment_color']; 
			if(empty($user_id)) return 'User ID required';
			if( update_user_meta($user_id, 'comment_color', $color)) return "Color $color selected for User #$user_id";
		} // end change staff for comments header
	}
	
	private function get_reassigned_comments(){ // Return an array of reassigned comments
		global $wpdb;
		wp_cache_flush();
		$comments_id = $wpdb->get_results( "SELECT comment_id FROM $wpdb->commentmeta WHERE meta_key = 'janitor_reassigned'" , OBJECT );
		$comments = array();
		foreach($comments_id as $comment_id){
			$comment_id = $comment_id->comment_id;
			$comment = get_comment($comment_id);
			array_push( &$comments, $comment );
		}
		
		return $comments;
	}
	
	private function get_users_color(){
		global $wpdb;
		wp_cache_flush();
		return $user_colors = $wpdb->get_results( " SELECT user_id, meta_value FROM $wpdb->usermeta WHERE meta_key = 'comment_color' AND meta_value != 'default' " , OBJECT );
	}
	
	private function update_recent_activity($user_id, $comment_id){ // Update User Recent Activity
		wp_cache_flush();
		$comment = get_comment($comment_id);
		$comment_author = $comment->comment_author;
		$comment_post_id = $comment->comment_post_ID;
		$comment_content = $comment->comment_content;
		$comment_author_email = $comment->comment_author_email;
		$comment_author_url = $comment->comment_author_url;
		
		
		$permalink = get_permalink($comment_post_id);

		$comment_date = get_comment_date('Y-m-d G:m:s', $comment_id);
		$comment_date = get_gmt_from_date($comment_date);
		$action = "<a href='$comment_author_url'>$comment_author</a> commented on $permalink";
		bp_activity_add( array(
			'user_id' => $user_id,
			'component' => 'blogs',
			'type' => 'new_blog_comment',
			'action' => $action,
			'primary_link'=> $permalink."#comment-$comment_id",
			'content' => $comment_content,
			'recorded_time' => $comment_date 
		) );
		return $comment_id;
	}
	
	private function handle_avatar_upload($file, $subdir = 'avatars'){
		 $file_name = $file['name'];
		 $file_type = $file['type'];
		 $file_size = $file['size'];
		 $tmp_name  = $file['tmp_name'];
		 
		 $upload_dir =  wp_upload_dir();
		 $target_path = $upload_dir['basedir'].'/'.$subdir;
		 
		 if( ! ($file_type =='image/jpeg' || $file_type =='image/gif' || $file_type =='image/x-png' || $file_type == 'image/png') ){
			return false;
		 }
		 
		 $upload_url = $upload_dir['baseurl'];
		 $new_file_name = md5(microtime());
		 $target =  $target_path.'/'.$new_file_name;
		 
		 $target_url = $upload_url.'/avatars/'.$new_file_name;
		 
		 if(@move_uploaded_file($tmp_name, $target)) {
			return $target_url;
		} 
		else{
			return false;
		}
	}

}


?>