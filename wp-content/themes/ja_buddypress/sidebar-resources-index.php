	<!--Digest of Featured Resources
	<div class="item-list-tabs"><a href="http://www.journalismaccelerator.com/topics/resources/digest-resources/">Digest of Featured Links</a></div>
	<?php 
	$args = array(
		'post_status' => 'publish',
		'order' => 'DSC',
		'category_name' => 'digest-resources',
		'posts_per_page' => 3
	);
	
	$the_query = new WP_Query($args);
	?>
	<ul id="popular-list">
<li><img class="index-card" alt="image of indexed book" src="<?php echo site_url(); ?>/wp-content/themes/ja_buddypress/images/index-book.png"><p>Browse all of our archived featured links, organized weekly.</p></li>
	<?php //the loop
	while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
		<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
	<?php endwhile; ?>
	</ul>
	<div class="boxed-more-link">
	<a href="<?php echo site_url(); ?>/topics/resources/digest-resources/">More Digest of Featured Links &raquo;</a>
	</div>
-->	
	
	<!--Tweets for Keeps-->
	<div class="item-list-tabs"><a href="<?php echo site_url(); ?>/topics/resources/tweetsforkeeps/">Tweets for Keeps</a></div>
	<?php 
	$args = array(
		'post_status' => 'publish',
		'order' => 'DSC',
		'category_name' => 'tweetsforkeeps',
		'posts_per_page' => 3
	);
	
	$the_query = new WP_Query($args);
	?>
	<ul id="commented-list">

<li><img class="tweet-bird" alt="Image of Twitter Bird" src="<?php echo site_url(); ?>/wp-content/themes/ja_buddypress/images/index-twitter.png"><p>JA's top picks each week from our stream for you to find when it’s convenient.</p></li>

	<?php //the loop
	while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
		<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
	<?php endwhile; ?>
	</ul>
	<div class="boxed-more-link">
	<a href="<?php echo site_url(); ?>/topics/resources/tweetsforkeeps/">More Tweets for Keeps &raquo;</a>
	</div>


<!--Most Popular Posts-->
	<div class="item-list-tabs">Most Popular Resources</div>
	<?php 
	$args = array(
		'post_status' => 'publish',
		'meta_key' => 'post_views_count',
	    'orderby' => 'meta_value_num',
		'order' => 'desc',
		'category_name' => 'resources',
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

<?php wp_reset_query(); ?>

	<div id="tag-cloud-wrapper">
	<h4 class="item-list-tabs">View Resources by Tags</h4>
<div id="tag-wrapper">
<?php 		
	  $tagargstwo = array(
		'smallest'                  => 10, 
		'largest'                   => 30,
		'unit'                      => 'px', 
		'number'                    => 45,  
		'format'                    => 'flat',
		'link'                      => 'view', 
		'taxonomy'                  => 'post_tag',
		'category_name' => 'resources',
);

		wp_tag_cloud( $tagargstwo ); ?>		
</div>

	</div>