<h4>Related Blog Posts</h4>
	<?php $mb->the_field('posts'); ?>
	<p><label>Post IDs:</label> <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></p>
	<p><em>Must be a comma-separated list of post IDs.</em></p>