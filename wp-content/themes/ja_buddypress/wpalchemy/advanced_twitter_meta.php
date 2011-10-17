<?php global $wpalchemy_media_access; ?>

<?php $metabox->the_field('twitter_query_type'); ?>
<p><label>Feed type:</label>

<?php $selected = ' selected="selected"'; ?>

<select name="<?php $mb->the_name(); ?>">
	<option value="search"<?php if ($metabox->get_the_value() == 'search') echo $selected; ?>>   Search</option>
	<option value="profile"<?php if ($metabox->get_the_value() == 'profile') echo $selected; ?>> Profile</option>
</select>

<?php $metabox->the_field('twitter_query'); ?>
<p><label>Query:</label> <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></p>
<p>Example of queries: @winning_mark, #socialmedia, journaccel </p>
	
	
<?php $metabox->the_field('twitter_count'); ?>
<p style="margin-bottom: 2em;"><label>Number of Tweets</label> <input type="text" name="<?php $mb->the_name(); ?>" value="<?php if($mb->get_the_value()){$mb->the_value();} else echo '0'; ?>" /><br />


