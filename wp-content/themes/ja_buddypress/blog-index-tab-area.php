<script>
	jQuery(document).ready(function($) {
		$("#example-two").organicTabs({
		"speed": 200
		});
	});
</script> 

<div id="example-two">
			
	<ul class="nav">
		<li class="nav-three"><a class="current" href="#date">By Date</a></li>
		<li class="nav-two"><a href="#author">By Author</a></li>
		<li class="nav-one"><a href="#topic">By Topic</a></li>
	</ul>
	
	<div class="list-wrap">

		<div class="hide" id="topic" style="position: relative; top: 0px; left: 0px; display: none;">
		<p id="helptext">These posts are listed by published date. Most recent posts are at the top.</p>	
			<?php 
			$categories = get_categories('child_of=39');
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
			
			// Get the URL of this category
			?>
			<div class="boxed-blog">
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
		
		 
		<div class="hide" id="author" style="position: relative; top: 0px; left: 0px; display: none;">
			<?php 
			$wp_user_search = new WP_User_Query( array( 
				'include' => array(747,736,700),
				'orderby' => 'ID'
				 ) );
			$authors = $wp_user_search->get_results();
			foreach($authors as $author){
			$args = array(
				'post_status' => 'publish',
				'author' => $author->ID,
				'order' => 'DSC',
				'posts_per_page' => 5
			);
			$the_query = new WP_Query($args);
			?>			
			<div class="boxed-author">
			<?php bp_author_link($author->ID);
?>
			<p>Latest posts by <?php echo $author->display_name; ?></p>
			<ul>
<?php //the loop
			while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
				<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
			<?php endwhile; ?>
			</ul>
			<?php if($the_query->found_posts >5){ ?>
			<p class="morelink">More posts by <?php the_author_posts_link(); ?></p>
			<?php } ?>
			</div>
			<?php } ?>

		<?php wp_reset_query(); ?>

<!-- Other Authors -->
<div class="boxed-author-other">			

	<img alt="Journalism Accelerator" src="http://www.journalismaccelerator.com/wp-content/uploads/2012/08/ja_icon-48x48.png"><p id="other-authors">Other Authors</p>

<?php
	$wp_user_search = new WP_User_Query( array( 
		'include' => array(725,713,66,2,729,697) ) );

	$authors_other = $wp_user_search->get_results();

	foreach($authors_other as $author){
		$args = array(
			'post_status' => 'publish',
			'author' => $author->ID,
			'order' => 'DSC',
			'posts_per_page' => 5
		);
		$the_query = new WP_Query($args);
		?>			

<p class="author-lead-in">Latest posts by <?php echo $author->display_name; ?></p>
<ul>
<?php //the loop
	while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
		<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
	<?php endwhile; ?>
</ul>
	<?php if($the_query->found_posts >5){ ?>
	<p class="morelink">More posts by <?php the_author_posts_link(); ?></p>

	<?php } ?>
	
	<?php } ?>
	</div>



 		</div> <!--END div#author -->
		 
		 
		 
		<div id="date">
			<?php 
			$args = array(
				'post_status' => 'publish',
//				'meta_key' => 'post_views_count',
				'orderby' => 'date',
				'order' => 'DSC',
				'category_name' => 'blog',
				'nopaging' => 'true',			);
			
			$the_query = new WP_Query($args);
			?>
<div class="boxed-date">
			<ul>
			<?php //the loop
			while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
				<li><span class="posted-date"><?php the_time('F j, Y'); ?>&mdash;</span><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
			<?php endwhile; ?>
			</ul>
</div>
		</div>
		 
	 </div> <!-- END List Wrap -->
	
</div>