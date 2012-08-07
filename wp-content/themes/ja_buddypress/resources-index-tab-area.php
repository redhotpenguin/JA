<script>
	jQuery(document).ready(function($) {
		$("#example-two").organicTabs({
		"speed": 200
		});
	});
</script> 

<div id="example-two">
			
	<ul class="nav">
		<li class="nav-one"><a class="current" href="#topic">By Topic</a></li>
		<li class="nav-two"><a href="#alpha">By Alpha</a></li>
<!-- 
		<li class="nav-three"><a href="#date">By Date</a></li>
 -->
	</ul>
	
	<div class="list-wrap">
	
		<div id="topic">
			<?php 
			$categories = get_categories('child_of=25');
			//print_r($categories);exit;
			foreach($categories as $category){
			//echo $category->term_id;
			$args = array(
				'post_status' => 'publish',
				'cat' => $category->term_id,
				'posts_per_page' => 7
			);

			$category_link = get_category_link( $category->term_id );			
			
			$the_query = new WP_Query($args);
			?>
			<div class="boxed-resource">
			<h4><?php echo $category->name; ?> <a href="<?php echo esc_url( $category_link ); ?>">&raquo;</a></h4>
			<ul>
<?php //the loop
			while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
				<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
			<?php endwhile; ?>
			</ul>
			<p id="morelink"><a href="<?php echo esc_url( $category_link ); ?>">More &raquo;</a></p>
			</div>
			<?php } ?>
		</div>
		
		 
		<div class="hide" id="alpha" style="position: relative; top: 0px; left: 0px; display: none;">
			<?php 
			$args = array(
				'cat' => 25,
				'post_status' => 'publish',
				'order' => 'ASC',
				'orderby' => 'title',
				'showposts' => -1
			);
			
			$the_query = new WP_Query($args);
	
	echo '<a name="letter-index"></a><br />';
	echo '<p class="letter-index"><strong>Jump to: </strong>';
	$curr_letter = '';
	foreach ( $the_query->posts as $post ) {
		$this_letter = strtoupper(substr($post->post_title,0,1));
		if( $this_letter != $curr_letter ) {
			echo "<a class='letter-link' href='#letter-$this_letter' >$this_letter</a>&nbsp;|&nbsp;";
		 $curr_letter = $this_letter;
      }
   }
	echo '</p>';
	wp_reset_query();

	$args = array(
		'cat' => 25,
		'post_status' => 'publish',
		'order' => 'ASC',
		'orderby' => 'title',
		'showposts' => -1
	);
	
	$the_query = new WP_Query($args);
$curr_letter = '';
$letter_div = '<div class="letter-wrap">';
echo $letter_div;
while ($the_query->have_posts()) {
   $the_query->the_post();				
   $this_letter = strtoupper(substr($post->post_title,0,1));
   if ($this_letter != $curr_letter) {
      if ($curr_letter !== '') echo "</ul><p class='letter-back'><a href='#letter-index' >Back to Index</a></p></div>$letter_div";
      echo "<a name='letter-$this_letter'></a><br />";
      echo "<h3 class='alpha-title'>$this_letter</h2><ul>";
      $curr_letter = $this_letter;
   }
   ?>
	<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
			<?php } ?>
			</div>
		</div>
		 
		 
		 
<!-- Hiding tab for future development
		<div class="hide" id="date" style="position: relative; top: 0px; left: 0px; display: none;">
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
			<ul>
			<?php //the loop
			while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
				<li><?php the_time('F j, Y'); ?><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
			<?php endwhile; ?>
			</ul>
		</div>
 -->
		 
	 </div> <!-- END List Wrap -->
	
</div>