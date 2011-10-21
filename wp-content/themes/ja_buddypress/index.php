<?php get_header() ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_blog_home' ) ?>

		<div class="page" id="blog-latest">
			
			<div id="slider-container">
			  <div id="featured-slider">
							<?php ja_slider(); ?>
							
						</div>
						<div id="slider-nav">
								<a href="#" id="prev">&lt;</a><a href="#" id="next">&gt;</a>
						</div>
			</div>
			<div id="home-tweet" class="clearfix"><img src="http://www.journalismaccelerator.com/ja-bird.png" class="ja-bird" /><div id="home-tweet-container"></div>
			<div class="twitter-follow"><a href="https://twitter.com/journaccel" class="twitter-follow-button" data-show-count="false">Follow @journaccel</a>
<script src="//platform.twitter.com/widgets.js" type="text/javascript"></script></div></div>
			
			<?php ja_home(); ?>
			
			
		</div>

		<?php do_action( 'bp_after_blog_home' ) ?>
		
		

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>
