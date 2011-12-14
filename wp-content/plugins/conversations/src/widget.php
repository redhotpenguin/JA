<?php

class JPCONV_Widget extends WP_Widget {
	public function JPCONV_Widget() {
		parent::WP_Widget( 'jpconv_widget', 'Conversation Widget', array( 'description' => 'Sidebar Display Comments' ) );
	}
	
	public function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
		}
		else {
			$title = __( 'Conversations on This Post', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<?php 
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	public function widget($args, $instance){
		global $wp_query; 
		global $post;
		
		$post_id =  url_to_postid( $wp_query->query_vars['participants'] );
	
		if( empty($post_id) )
			$post_id = $post->ID;

		$permalink = get_permalink($post_id);
	
		$display_n = get_option('jpconv_display_comment_number');
		
		$comments = get_comments(array(
			'post_id' => $post_id,
			'number' => $display_n,
			'order' => 'DESC',
			'type' => 'comment'
		));
		
		
		
		if( count ($comments) <= 3 )
			return false;


		if(!is_single()  && ! $wp_query->query_vars['participants'] ){
			return false;
		}	
			
		$more = '<span class="jpconv_more"> <a href="'.$permalink.'#respond">Weigh In</a> </span>';
			
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $more. $after_title; 
	
		echo '<div id="jpconv">';
		foreach($comments as $comment){
			$user_id = $comment->user_id;
			$avatar = get_avatar($user_id, 50);
			$user_name = $comment->comment_author;
			$profile_url = get_link_to_public_profile($user_id);
			$comment_content = $comment->comment_content;
			$comment_content = strip_tags(substr($comment_content, 0, 100));
			?>
			<div class="jpconv clearfix">
				<div class="jpconv_avatar">
					<?php echo $avatar; ?>
				</div>
				
				<div class="jpconv_content">
				<?php 
					echo "<a href='$profile_url'>$user_name</a> ";
					echo "<span class='jpconv_said'><a href='#comment-$comment->comment_ID'>said</a>:</span> ";
					echo "$comment_content ";
					?>
				</div>
			
			</div>
			<?php
		}
		if(! $wp_query->query_vars['participants'] ) 
			echo '<span class="jpconv_involved"><a href="'.$permalink.'participants">Who else is talking?</a></span>';
		
		echo '</div>';
		
		echo $after_widget;
	}
	
}

?>