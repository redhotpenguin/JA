<?php

class Tweet{
	private $tweet_id;
	private $tweet_username;
	private $tweet_avatar;
	private $tweet_text;
	private $tweet_date;


	
	public function __construct($id="", $username="", $text="", $avatar="",  $date="" ){
		$this->tweet_id = $id;
		$this->tweet_username = $username;
		$this->tweet_avatar = $avatar;
		$this->tweet_text = $text;
		$this->tweet_date = $date;

	}
	
	public function get_tweet_id() { return $this->tweet_id; } 
	public function get_tweet_username() { return $this->tweet_username; } 
	public function get_tweet_avatar() { return $this->tweet_avatar; } 
	public function get_tweet_text() { return $this->tweet_text; } 
	public function get_tweet_date() { return $this->tweet_date; }
	
	public function set_tweet_id($x) { $this->tweet_id = $x; } 
	public function set_tweet_username($x) { $this->tweet_username = $x; } 
	public function set_tweet_avatar($x) { $this->tweet_avatar = $x; } 
	public function set_tweet_text($x) { $this->tweet_text = $x; } 
	public function set_tweet_date($x) { $this->tweet_date = $x; } 

	 public function __toString(){ // The following code MUST be used ony for debugging purpose  and not for rendering.
		$ret .= "<img style='width:48px; height:48px; float:left; margin-right:3px;' src='$this->tweet_avatar'/>";
		$ret .= "<b>ID:</b> <span style='color:blue'; >$this->tweet_id </span><br/>";
		$ret .= "<b>Username:</b> $this->tweet_username <br/>";
		$ret .= "<b>Text:</b> $this->tweet_text <br/>";
		$ret .= "<b>Date:</b> $this->tweet_date <br/>";

		return $ret;
    }
}


?>