<?php

class Ping_Highrise_Business{
	private $hr_core;
	private $user_tag;
	private $assigned_task_to;
	private $task_category;
	
	public function __construct($hr_core, $params){
		$this->hr_core = $hr_core;
	 
		$this->user_tag = $params['user_tag'];
		$this->assigned_task_to = $params['assigned_task_to'];
		$this->task_category = $params['task_category'];
	}
	
	
	public function new_comment_hook($comment_id, $approved = true){ // executed when a new comment is posted
		$post_highrise_url = get_option('post_highrise_url');
		$hr_url = get_option('highrise_url');
		$hr_token= get_option('highrise_token');
		
		ph_log('business: new_comment_hook  comment_ID is :'.$comment_id);
		ph_log('business: new_comment_hook  post_highrise_url is :'.$post_highrise_url);
		ph_log('business: new_comment_hook  hr_url is :'.$hr_url);
		ph_log('business: new_comment_hook  hr_token is :'.$hr_token);
	
		$post_body = array(
			'action' => 'new_comment',
			'hr_url' =>  $hr_url,
			'hr_token' => $hr_token,
			'comment_id' => $comment_id,
		);

		$this->hr_core->make_request($post_highrise_url, $post_body);
	}

	public function new_user_hook($user_id){ // executed when a user registers
		$post_highrise_url = get_option('post_highrise_url');
		$hr_url = get_option('highrise_url');
		$hr_token= get_option('highrise_token');
		
		$post_body = array(
			'action' => 'new_user',
			'hr_url' =>  $hr_url,
			'hr_token' => $hr_token,
			'user_id' => $user_id,
			'user_tag' => $this->user_tag,
			'assign_tasks_to' => $this->assigned_task_to,
			'task_category' => $this->task_category
		);
		$this->hr_core->make_request($post_highrise_url, $post_body);
	}

}

?>