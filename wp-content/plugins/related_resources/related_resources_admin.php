<?php  
    if($_POST['featured_resources_hidden'] == 'Y') {  
        //Form data sent
        $resources_home = $_POST['resources_home'];
        update_option('resources_home', $resources_home);
        ?><div class="updated"><p><strong><?php _e('Options saved.'); ?></strong></p></div><?php
    } else {  
        //Normal page display
        $resources_home = get_option('resources_home');
    }  
?>  

<div class="wrap">
	<?php echo "<h2>" . __( 'Featured Resources Settings', 'featured_resources_options' ) . "</h2>"; ?>
	<form name="featured_resources_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="featured_resources_hidden" value="Y" />
		<?php echo "<h4>" . __( 'Resources to display on home page', 'featured_resources_options') . "</h4>"; ?>
		<textarea name="resources_home" rows="10" cols="50"><?php echo $resources_home; ?></textarea>
		<p class="submit">
			<input type="submit" value="<?php _e( 'Update', 'featured_resources_options' ) ?>" />
		</p>
	</form>
</div>