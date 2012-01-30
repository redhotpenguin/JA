<?php
if (!function_exists ('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}


class Tweet_Manager_Admin{
	private $jptc_model;
	private $tweet_catcher_status;
	
	public function __construct($jptc_model){
		$this->jptc_model = $jptc_model;
		add_action('admin_menu', array(&$this, 'add_option_menu') );
		add_action( 'admin_init', array(&$this,'register_settings') );
		add_action('admin_notices', array(&$this, 'jptc_warning') );
		
	    $this->tweet_catcher_status = get_option('jptc_tweet_catching');
	}
	
	
	public function add_option_menu(){
			add_options_page('Tweet Catcher', 'Tweet Catcher', 'manage_options', 'tweet-catcher',  array(&$this,'admin_page') );
	}
	
	public function register_settings(){
		register_setting( 'jptc_settings_group', 'jptc_tweet_catching' );
		register_setting( 'jptc_settings_group', 'jptc_follow_btn' );
		register_setting( 'jptc_settings_group', 'jptc_exluded_users' );
		register_setting( 'jptc_settings_group', 'jptc_email', array(&$this, 'sanitize_email_address')  );
		register_setting( 'jptc_settings_group', 'jptc_email_notification');
	}
	
	public function jptc_warning(){
		$btn = get_option('jptc_follow_btn');
		if( empty( $btn)  ){ 
		echo "<div id='tweet-catcher-warning' class='updated fade'><p><strong>"
					   . "Tweet Catcher says:</strong> please <b><a href='options-general.php?page=tweet-catcher'>configure me</a></b> if you want the job to get done!" 
					   . "</p></div>";
		}
	}
	
	public function admin_page(){
		echo '<div class="wrap">';
		echo '<h2>Tweet Catcher Settings</h2>';
		$this->form_begin();
		$this->set_follow_button();
		$this->activate_catching();
		$this->email_settings();
		$this->users_to_exlude();
		$this->form_end();
		echo '<hr/><h2>Tweet Catching</h2> Will only search for posts/pages where Tweet Catcher is used. The Permalink is used as a search query';
		if( $this->tweet_catcher_status == 'on' ) 
			$this->display_next_scheduled_job();
		$found_tweets = $this->find_tweets();
		echo '<hr/>';
		$this->last_tweets_added($found_tweets);
		echo '</div>';
	}
	
	private function form_begin(){ ?>
		<form method="post" action="options.php">
		<?php settings_fields( 'jptc_settings_group' );?>
		<table class="widefat" id="jptc_settings">
		<thead>
			<th>Setting</th>
			<th>Value</th>
			<th>Description</th>
		</thead>
	
	 <?php }
	
	private function form_end(){
		echo '<tr class="jptc_submit_row"> <td colspan="3"> <input type="submit" value="Done!" /> </td> </tr>';
		echo '</table> </form>';
	}
	
	
	
	private function set_follow_button(){ ?>
			<tr>
				<td>Follow Button <b>(required)</b> </td>
				<td><input  size="70" type="text" name="jptc_follow_btn" value="<?php echo get_option('jptc_follow_btn'); ?>" /></td>
				<td>Ex: journaccel </td>
			</tr>	
	<?php
	}
	
	private function users_to_exlude(){ 
	?>
			<tr>
				<td>Twitter Users Exluded from Catching </td>
				<td><input  size="70" type="text" name="jptc_exluded_users" <?php echo 'value="'.get_option('jptc_exluded_users').'"'; ?> /></td>
				<td>Ex: evil666, stupidtweet (comma separated values allowed)</td>
			</tr>	
	<?php
	}
	


	
	 function activate_catching(){
		if($this->tweet_catcher_status  == 'on')
			$checked = 'checked = "checked" ';
		else 
			$checked = '';		
	 ?>

	
			<tr>
				<td>Enable Tweet Catching</td>
				<td><input type="checkbox" name="jptc_tweet_catching" <?php echo $checked ?> /></td>
				<td>Look for new Tweets every hour </td>
			</tr>
	<?php
	}
	
	private function email_settings(){
		$email_address = get_option('jptc_email');
		$jptc_email_notification = get_option('jptc_email_notification');

		if($jptc_email_notification)
			$checked = 'checked = "checked" ';
		else 
			$checked = '';
		?>
			<tr>
				<td>Enable Email Notification</td>
				<td><input type="checkbox" name="jptc_email_notification" <?php echo $checked; echo $disabled; ?> /></td>
				<td>Send an Email when new Tweets are found.</td>
			</tr>
		
			<tr>
				<td>Send an Email to</td>
				<td><input size="70" type="text" name="jptc_email"  <?php  echo " value = '$email_address' "; ?>/></td>
				<td>Will send an email to &lt;<?php echo get_option('admin_email')?>&gt;  if empty </td>
			</tr>

	<?php	
	}
	private function display_next_scheduled_job(){
		echo '<h3>Next Search Scheduled in: </h3>';
		echo human_time_diff( time() , wp_next_scheduled('jptc_hook') );
	}
	public function find_tweets(){
	?>
	<h3>Can't wait? Force Tweet Catching</h3>
	<form method="post" action="#">
		<table>
			<tr>
				<td> <input type="submit" value="Find New Tweets" name="find_tweets" /> </td>
			</tr>
		</table>
	</form>
	<?php
	if( isset($_POST['find_tweets']) && !empty($_POST['find_tweets']) ){
		 $results= jptc_catch_tweets();
		  
		if($results  > 0) {
			$tweet_s = apply_filters('jptc_tweet_s' , $results );
			echo '<div id="message" class="updated"><p><b>'.$results.' new '.$tweet_s.' added.</b></p></div>';
			return $results;
		}
		else echo '<div id="message" class="updated"><p>No Tweets Found :( </p></div>';
		}
	}
	
	
	
	public function last_tweets_added($found_tweets = 0 ){
		@session_start();
		$_SESSION['jptc_current_tweets_displayed'] = JPTC_ADMIN_DISPLAY_N_TWEETS;
		$total_tweets_catched =  $this->jptc_model->get_total_saved_tweets();
		echo "<h2>Tweets Saved - <span id='jptc_tweet_counter' >$total_tweets_catched</span> so far! </h2> List of the last caught and saved Tweets (most recent first)";
		
		echo '<div id="last_tweets">';
		$this->jptc_model->the_last_saved_tweets( JPTC_ADMIN_DISPLAY_N_TWEETS + $found_tweets, $found_tweets, 0);
		echo '</div>';
		echo '<div id="jptc_more" ><span id="jptc_more_tweets_btn">More</span></div>';
	}
	
//  ***************************************************************************************************//
// 												SANITIZE IDIOTS	:@									   //
//  ***************************************************************************************************//
	public function sanitize_email_address($p_address){
		if(  sanitize_email($p_address) )
			return $p_address;
		else
			return get_option('admin_email');
	}
	
}

?>