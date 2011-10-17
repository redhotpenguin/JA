<?php global $wpalchemy_media_access; ?>


<h4>Twitter feed parameter:</h4>

<?php 
	$metabox->the_field('twitter_parameters');

?>
		<p><label>Query:</label> 
		<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></p>
		<p>Example of queries: @winning_gmark, #socialmedia, journaccel </p>

		
