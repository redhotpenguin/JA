<?php
if (!function_exists ('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

class Ping_Highrise_Setup{
	function __construct(){
		add_action('admin_menu', array(&$this, 'create_menu'));
	}

	function create_menu(){
		$ico = get_site_url(). '/wp-content/plugins/ping-highrise/highrise_ico.png';
		add_options_page('Ping Highrise', 'Ping Highrise', 'manage_options', 'ping_highrise',  array(&$this,'settings_page') );
		add_action( 'admin_init', array(&$this,'register_settings') );
	}
	
	function register_settings(){
		register_setting( 'ph-settings-group', 'post_highrise_url' );
		register_setting( 'ph-settings-group', 'highrise_url' );
		register_setting( 'ph-settings-group', 'highrise_token' );
		register_setting( 'ph-settings-group', 'tasks_user_id');
		register_setting( 'ph-settings-group', 'tasks_user_tag' );
		register_setting( 'ph-settings-group', 'highrise_task_category' );
		register_setting( 'ph-settings-group', 'highrise_task_category_updated_contact' );
		register_setting( 'ph-settings-group', 'highrise_new_user_hook' );
		register_setting( 'ph-settings-group', 'highrise_new_comment_hook' );
		register_setting( 'ph-settings-group', 'highrise_new_comment_hook' );
		register_setting( 'ph-settings-group', 'highrise_cron_key' );
	}
	
	
	
	function settings_page(){
	$post_highrise_url = get_option('post_highrise_url');
	$new_user_hook = get_option('highrise_new_user_hook');
	$new_comment_hook = get_option('highrise_new_comment_hook');

		?>
		<div class="wrap">
		<h2>Ping Highrise Settings</h2>
		<form method="post" action="options.php">
		<?php
			settings_fields( 'ph-settings-group' );
			do_settings_sections( 'ping_highrise' );

		?>
	
			<table class="widefat">
		
			<tr valign="top">
			<th scope="row">URL to post_highrise.php</th>
			<td><input  size="70" type="text" name="post_highrise_url" value="<?php echo $post_highrise_url; ?>" /></td>
			<td>Ex: http://www.mywebsite/wp-content/plugins/ping-highrise/post_highrise.php</td>
			</tr>
			
			
			<tr valign="top">
			<th scope="row">Highrise URL</th>
			<td><input size="70" type="text" name="highrise_url" value="<?php echo get_option('highrise_url'); ?>" /></td>
			<td>Ex: https://example.highrisehq.com</td>
			</tr>
			 
			<tr valign="top">
			<th scope="row">Highrise Token</th>
			<td><input size="70" type="text" name="highrise_token" value="<?php echo get_option('highrise_token'); ?>" /></td>
			<td>Ex: 4ba194ayb2e40c43d858cfaa49da13 (found in My Info)</td>
			</tr>
			
			<tr valign="top">
			<th scope="row">Tasks are assigned to (user ID)</th>
			<td><input size="70" type="text" name="tasks_user_id" value="<?php echo get_option('tasks_user_id'); ?>" /></td>
			<td>Ex: 605140 See mysite.highrisehq.com/users.xml &lt;id type="integer"&gt;605140&lt;/id&gt;</td>
			</tr>
			
			<tr valign="top">
			<th scope="row">Task Category (for new contacts)</th>
			<td><input size="70" type="text" name="highrise_task_category" value="<?php echo get_option('highrise_task_category'); ?>" /></td>
			<td>Ex: 3239887.  See mysite.highrisehq.com/task_categories.xml &lt;id type="integer"&gt;3239887&lt;/id&gt;</td>
			</tr>
			
			<tr valign="top">
			<th scope="row">Task Category (for existing contacts)</th>
			<td><input size="70" type="text" name="highrise_task_category_updated_contact" value="<?php echo get_option('highrise_task_category_updated_contact'); ?>" /></td>
			<td>Ex: 3239887.  See mysite.highrisehq.com/task_categories.xml &lt;id type="integer"&gt;3239887&lt;/id&gt;</td>
			</tr>
			
			<tr valign="top">
			<th scope="row">New Users are tagged under</th>
			<td><input size="70" type="text" name="tasks_user_tag" value="<?php echo get_option('tasks_user_tag'); ?>" /></td>
			<td>Ex: JA Commenter. Multiple tags allowed: Tag1, Tag2, Tag3</td>
			</tr>
			
			
			
			<tr valign="top">
			<th scope="row">New User Hook</th>
			<td><input size="70" type="text" name="highrise_new_user_hook" value="<?php echo $new_user_hook ?>" /></td>
			<td>Default: <a href="http://codex.wordpress.org/Plugin_API/Action_Reference/user_register">user_register</a></td>
			</tr>
			
			<tr valign="top">
			<th scope="row">New Comment Hook</th>
			<td><input size="70" type="text" name="highrise_new_comment_hook" value="<?php echo $new_comment_hook ?>" /></td>
			<td>Default: comment_post</td>
			</tr>
			
			<tr valign="top">
			<th scope="row">Cron Key</th>
			<td><input size="70" type="text" name="highrise_cron_key" value="<?php echo  get_option('highrise_cron_key'); ?>" /></td>
			<td>Secret key to execute the cron script</td>
			</tr>
			
		</table>
		
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
		</form>
                    
            <h2>Queue:</h2>
            <table class="widefat">
                <tr style="font-weight: bold;">
                    <td>User/Comment ID</td>
                    <td>Type</td>
                    <td>Status</td>
                    <td>Code</td>
                    <td>Date</td>
                </tr>
               
                <?php
                  $comments_status = ph_get_comment_status();
                  if( empty( $comments_status)  )
                      echo '<p>Nothing to Process.</p>';
                  else
                    foreach($comments_status as $comment_status){
                        echo '<tr>';
                        $comment_status;
                            echo '<td> '.$comment_status['uc_id'].' </td>';
                            echo '<td> '.$comment_status['type'].' </td>';
                            echo '<td> '.$comment_status['status'].' </td>';
                            echo '<td> '.$comment_status['code'].' </td>';
                            echo '<td> '.$comment_status['time'].' </td>';
                        echo '</tr>';
                    }
                  
                  
                  ?>
                
            </table>
            </div>
             <?php
	}
	
}

?>