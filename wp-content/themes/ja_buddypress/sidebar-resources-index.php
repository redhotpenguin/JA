	<!--Most Popular Resources-->
	<div class="item-list-tabs">Most Popular</div>
	<?php 
	$args = array(
		'post_status' => 'publish',
		'meta_key' => 'post_views_count',
	    'orderby' => 'meta_value_num',
		'order' => 'ASC',
		'category_name' => 'resources',
		'posts_per_page' => 7
	);
	
	$the_query = new WP_Query($args);
	?>
	<ul id="popular-list">
	<?php //the loop
	while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
		<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
	<?php endwhile; ?>
	</ul>
	
	
	<!--Most Commented Resources-->
	<div class="item-list-tabs">Most Commented</div>
	<?php 
	$args = array(
		'post_status' => 'publish',
		'orderby' => 'comment_count',
		'order' => 'ASC',
		'category_name' => 'resources',
		'posts_per_page' => 7
	);
	
	$the_query = new WP_Query($args);
	?>
	<ul id="commented-list">
	<?php //the loop
	while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
		<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
	<?php endwhile; ?>
	</ul>
	

<!-- Hiding until categories resolved.

	Resources Posts by Tag
	<div class="item-list-tabs">View by tags</div>
<div id="tag-cloud-wrapper">
	<?php if ( function_exists('wp_tag_cloud') ) : ?>
		<?php wp_tag_cloud('smallest=7&largest=24&include=25,35,34,33,26,324,36,16,32,31,30,445'); ?>
	<?php endif; ?>
	</div>
 -->