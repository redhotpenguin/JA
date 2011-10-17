<?php 
/*
Template Name: Answers
*/
get_header(); ?>

	<div id="content">
		<div class="padder">


		<div class="page" id="all-archives">
			<h1>Archive of All Answers</h1>
			<?php 
			all_comments();
         	?>
		</div>


		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer(); ?>
