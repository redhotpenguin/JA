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

	 public function __toString(){
		$ret .= "<div class='model_tweet'><img style='float: left; margin-right: 5px;' src='$this->tweet_avatar'/>";
		$ret .= "<b><a href='https://twitter.com/#!/$this->tweet_username'> $this->tweet_username: </a></b>";
		$ret .= "$this->tweet_text <br/>";
		$ret .= "<a href='https://twitter.com/#!/$this->tweet_username/status/$this->tweet_id'>". date("l M j Y \- g:ia",strtotime($this->tweet_date))  ."</a> <br/></div>";

		return $ret;
    }
}


?>