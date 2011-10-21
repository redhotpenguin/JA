<?php global $wpalchemy_media_access; ?>
<h4>Resource Intro Tout</h4>
<?php $mb->the_field('tout'); ?>
<p><textarea name="<?php $mb->the_name(); ?>"><?php $mb->the_value(); ?></textarea></p>
<h4>Logo Upload</h4>
<?php $mb->the_field('logo'); ?>
<?php $wpalchemy_media_access->setGroupName('resource_logo')->setInsertButtonLabel('Upload')->setTab('type'); ?>
<?php echo $wpalchemy_media_access->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
<?php echo $wpalchemy_media_access->getButton(); ?>
<?php $mb->the_field('force-left'); ?>
<p><label>Force float left?</label> <input type="checkbox" name="<?php $mb->the_name(); ?>" value="force-left"<?php echo $mb->is_value('force-left')?' checked="checked"':''; ?> /></p>
<h4>Title for Box</h4>
<?php $mb->the_field('box_title'); ?>
<p><label>Box Title</label> <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></p>
<h4>Related Links</h4>
<?php while ($mb->have_fields_and_multi('links')) : ?>
<?php $mb->the_group_open(); ?>
	<?php $mb->the_field('title'); ?>
	<p><label>Link Title:</label> <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></p>
	<?php $mb->the_field('url'); ?>
	<p style="margin-bottom: 2em;"><label>Link URL:</label> <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /><br />
	<small><a href="#" class="dodelete">(remove link)</a></small></p>
<?php $mb->the_group_close(); ?>
<?php endwhile; ?>
<p><a href="#" class="docopy-links button">Add Link</a></p>
<h4>People</h4>
<?php while ($mb->have_fields_and_multi('people')) : ?>
<?php $mb->the_group_open(); ?>
	<?php $mb->the_field('name'); ?>
	<p><label>Name:</label> <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></p>
	<?php $mb->the_field('url'); ?>
	<p style="margin-bottom: 2em;"><label>URL:</label> <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /><br />
	<small><a href="#" class="dodelete">(remove link)</a></small></p>
<?php $mb->the_group_close(); ?>
<?php endwhile; ?>
<p><a href="#" class="docopy-people button">Add Person</a></p>