<?php

class JPCONV_Widget extends WP_Widget {
	public function JPCONV_Widget() {

		parent::WP_Widget( 'jpconv_widget', 'Conversation Widget', array( 'description' => 'Sidebar Display Comments' ) );
	}
	
	public function form( $instance ) {
		if ( $instance ) {
			$normal_title = esc_attr( $instance[ 'normal_title' ] );
			$normal_participant_title = esc_attr( $instance[ 'normal_participant_title' ] );
			
			$question_title = esc_attr( $instance[ 'question_title' ] );
			$question_participant_title = esc_attr( $instance[ 'question_participant_title' ] );
		}
		else {
			$normal_title = __( 'Conversations on This Post', 'text_domain' );
			$normal_participant_title = __( 'Talking about this post', 'text_domain' );
			
			$question_title = __( 'Conversations on This Post', 'text_domain' );
			$question_participant_title = __( 'Talking about this post', 'text_domain' );
		}
		?>
		
		
		<h3>Normal Posts:</h3>
		<p>
			<label for="<?php echo $this->get_field_id('normal_title'); ?>"><?php _e('Title:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('normal_title'); ?>" name="<?php echo $this->get_field_name('normal_title'); ?>" type="text" value="<?php echo $normal_title; ?>" />
		</p>
		
		
		<p>
			<label for="<?php echo $this->get_field_id('normal_participant_title'); ?>"><?php _e('Participant Grid Title:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('normal_participant_title'); ?>" name="<?php echo $this->get_field_name('normal_participant_title'); ?>" type="text" value="<?php echo $normal_participant_title; ?>" />
		</p>
		
		<hr/>
		
		<h3>Forum Posts:</h3>
		
		<p>
			<label for="<?php echo $this->get_field_id('question_title'); ?>"><?php _e('Title:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('question_title'); ?>" name="<?php echo $this->get_field_name('question_title'); ?>" type="text" value="<?php echo $question_title; ?>" />
		</p>
		
		
		<p>
			<label for="<?php echo $this->get_field_id('normal_participant_title'); ?>"><?php _e('Participant Grid Title:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('question_participant_title'); ?>" name="<?php echo $this->get_field_name('question_participant_title'); ?>" type="text" value="<?php echo $question_participant_title; ?>" />
		</p>
		
		<hr/>
		
		<?php 
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['normal_title'] = strip_tags($new_instance['normal_title']);
		$instance['normal_participant_title'] = strip_tags($new_instance['normal_participant_title']);
		
		$instance['question_title'] = strip_tags($new_instance['question_title']);
		$instance['question_participant_title'] = strip_tags($new_instance['question_participant_title']);
		return $instance;
	}

	public function widget($args, $instance){
		global $wp_query; 
		global $post;
		$post_id =  url_to_postid( $wp_query->query_vars['participants'] );
		
		if( !empty($post_id) ) // if grid page is displayed
			$post = get_post($post_id);
		else
			$post_id = $post->ID;
		
		
		if(has_tag('Question', $post)) //if post is tagged with 'question'
			$is_question = true;
		else
			$is_question = false;

		$permalink = get_permalink($post_id);
	
		$display_n = get_option('jpconv_display_comment_number');
	
		$comments = get_comments(array(
			'post_id' => $post_id,
			'number' => $display_n,
			'order' => 'DESC',
			'type' => 'comment',
			'status' => 'approve'
		));

		if( count ($comments) <= 3 )
			return false;

		if(!is_single()  && ! $wp_query->query_vars['participants'] ){
			return false;
		}	
		
		$more= '<span class="jpconv_more"> <a href="'.$permalink.'#respond">Weigh In</a> </span>';	
			
		extract( $args );
		
		echo $before_widget;
		
		if( $wp_query->query_vars['participants'] ) { // if page rendered is the participant grid
	
			if( $is_question )
				$title = $instance['question_participant_title'];
			else
				$title = $instance['normal_participant_title'];
			
		}
		else{ // if not the participant grid
			if( $is_question )
				$title = apply_filters( 'widget_title', $instance['question_title'] );
			else
				$title = apply_filters( 'widget_title', $instance['normal_title'] );
		}
		
		if ( $title ){
			echo $before_title . $title . $more. $after_title; 
		}
		
		echo '<div id="jpconv">';
		foreach($comments as $comment){	
			$user_id = $comment->user_id;
			$avatar = get_avatar($user_id, 50);
			$user_name = $comment->comment_author;
			$profile_url = get_link_to_public_profile($user_id);
			$comment_content = $comment->comment_content;
			$comment_content = substr( strip_tags( $comment_content) , 0, 100 );
			?>
			<div class="jpconv clearfix">
				<div class="jpconv_avatar">
					<?php echo $avatar; ?>
				</div>
				
				<div class="jpconv_content">
				<?php 
					echo "<a class='jpconv_name' href='$profile_url'>$user_name</a> ";
					if( $comment->comment_parent ){
						 $parent_comment = get_comment( $comment->comment_parent );
						// print_r($parent_comment);
						 $parent_user_id = $parent_comment->user_id;
						 $parent_author =  $parent_comment->comment_author;
						 $parent_profile_url = get_link_to_public_profile( $parent_user_id );
						 
						 
						echo "<span class='jpconv_said'><a href='./?/#comment-$comment->comment_ID' class='jpconv_link_reply'>replied</a> to <a class='jpconv_name' href='$parent_profile_url'>$parent_author</a>:</span> ";

					}
					
					else{
						echo "<span class='jpconv_said'><a href='#comment-$comment->comment_ID' class='jpconv_link_said' >said</a>:</span> ";
					}
					
					echo "$comment_content ";
					?>
				</div>
			
			</div>
			<?php
		}
		if(! $wp_query->query_vars['participants'] ) {
	
			//echo '<div class="jpconv_footer"><span class="jpconv_more"> <a href="'.$permalink.'#respond">Weigh In</a> </span>';
		
		//echo '<div class="jpconv_footer">';
		
			if(!$is_question)
				echo '<span class="jpconv_involved"><a href="'.$permalink.'participants">Who else is talking?</a></span>';			
			else
				echo '<span class="jpconv_involved"><a href="'.$permalink.'participants">Who else is here?</a></span>';			
			
		}
		else
			echo '<span class="jpconv_involved"> &nbsp; </span>';

		
		echo '<span class="jpconv_rss"><a href="'.$permalink.'feed/">Comment Feed <img alt="Comment Feed" title="Comment Feed" src="/feed.png" /></a></span>';
		echo '</div> ';
		
		echo $after_widget;
	}
	
}

?>