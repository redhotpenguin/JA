	<!--Digest of Featured Resources-->
	<div class="item-list-tabs">Digest of Featured Resources</div>
	<?php 
	$args = array(
		'post_status' => 'publish',
		'order' => 'ASC',
		'category_name' => 'digest-resources',
		'posts_per_page' => 3
	);
	
	$the_query = new WP_Query($args);
	?>
	<ul id="popular-list">
<li><img class="index-card" alt="image of indexed book" src="/wp-content/themes/ja_buddypress/images/index-book.png"><p>Browse all of our archived featured links, organized weekly.</p></li>
	<?php //the loop
	while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
		<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
	<?php endwhile; ?>
	</ul>
	<div class="boxed-more-link">
	<a href="http://www.journalismaccelerator.com/topics/resources/digest-resources/page/2/">More Digest of Featured Resources &raquo;</a>
	</div>
	
	
	<!--Tweets for Keeps-->
	<div class="item-list-tabs">Tweets for Keeps</div>
	<?php 
	$args = array(
		'post_status' => 'publish',
		'order' => 'ASC',
		'category_name' => 'tweetsforkeeps',
		'posts_per_page' => 3
	);
	
	$the_query = new WP_Query($args);
	?>
	<ul id="commented-list">

<li><img class="tweet-bird" alt="Image of Twitter Bird" src="/wp-content/themes/ja_buddypress/images/index-twitter.png"><p>JA's top picks each week from our stream for you to find when it’s convenient.</p></li>

	<?php //the loop
	while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
		<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
	<?php endwhile; ?>
	</ul>
	<div class="boxed-more-link">
	<a href="http://www.journalismaccelerator.com/topics/resources/tweetsforkeeps/page/2/">More Tweets for Keeps &raquo;</a>
	</div>


	<?php if ( function_exists('st_tag_cloud') ) : ?>

	<div id="tag-cloud-wrapper">
		<?php 
		$tag_args = array(
			'title' => __('<h4 class="item-list-tabs">View by Tags</h4>', 'simpletags'),
			'category' => 30,31,32,445,16,36,26,33,324,34,35,25	);
		
		st_tag_cloud( $tag_args ); ?>
	</div>

	<?php endif; ?>