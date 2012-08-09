	<!--Most Popular Posts-->
	<div class="item-list-tabs">Most Popular</div>
	<?php 
	$args = array(
		'post_status' => 'publish',
		'meta_key' => 'post_views_count',
	    'orderby' => 'meta_value_num',
		'order' => 'desc',
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
	<div class="item-list-tabs">Most Comments</div>
	<?php 
	$args = array(
		'post_status' => 'publish',
		'orderby' => 'comment_count',
		'order' => 'desc',
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
	<?php if ( function_exists('st_tag_cloud') ) : ?>

	<div id="tag-cloud-wrapper">
		<?php 
		$tag_args = array(
			'title' => __('<h4 class="item-list-tabs">View Blog Posts by Tags</h4>', 'simpletags'),
			'category' => 0	);
		
		st_tag_cloud( $tag_args ); ?>
	</div>

	<?php endif; ?>
