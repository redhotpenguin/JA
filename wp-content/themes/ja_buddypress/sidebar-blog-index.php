	<!--Most Popular Posts-->
	<div class="item-list-tabs">Most Popular</div>
	<?php 
	$args = array(
		'post_status' => 'publish',
		'meta_key' => 'post_views_count',
	    'orderby' => 'meta_value_num',
		'order' => 'ASC',
		'category_name' => 'blog',
		'posts_per_page' => 7
	);
	
	$the_query = new WP_Query($args);
	?>
	<ul>
	<?php //the loop
	while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
		<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
	<?php endwhile; ?>
	</ul>
	
	
	<!--Most Commented Posts-->
	<div class="item-list-tabs">Most Commented</div>
	<?php 
	$args = array(
		'post_status' => 'publish',
		'orderby' => 'comment_count',
		'order' => 'ASC',
		'category_name' => 'blog',
		'posts_per_page' => 7
	);
	
	$the_query = new WP_Query($args);
	?>
	<ul>
	<?php //the loop
	while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
		<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
	<?php endwhile; ?>
	</ul>
	

	<!--Blog Posts by Tag-->
	<div class="item-list-tabs">View Blog post by tags</div>
<div id="tag-cloud-wrapper">
	<?php if ( function_exists('wp_tag_cloud') ) : ?>
		<?php wp_tag_cloud('smallest=8&largest=22'); ?>
	<?php endif; ?>
</div>