		
		</div> <!-- #container -->

		<?php do_action( 'bp_after_container' ) ?>
		<?php do_action( 'bp_before_footer' ) ?>
		<div class="column-footer">
			<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer') ) ?>
		
			<div class="widget widget_text"><h2>Connect</h2>	
				<div class="textwidget">
					<ul>
<?php if(!is_user_logged_in()){?><li><img style=' background-color:#fff;' src='http://www.journalismaccelerator.com/wp-content/uploads/2011/09/ja_14.png'/> <a href="#" onclick="showRPX('rpxlogin');">Login</a></li><?php } ?>
						<li><a href="https://twitter.com/journaccel" class="twitter-follow-button" data-show-count="true" data-lang="en">Follow @journaccel</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></li>
						<li class="facebook-icon"><a href="http://www.facebook.com/journalismaccelerator" title="JA on Facebook" target="_blank">Facebook</a></li>
						<li class="linkedin-icon"><a href="http://www.linkedin.com/company/journalism-accelerator/" title="JA on LinkedIn" target="_blank">LinkedIn</a></li>
						<li class="rss-icon"><a href="/feed/">RSS Feed</a></li>
						<li class="ja-link"><a href="/link-to-us/">Link to Us</a></li>
					</ul>
				</div>
			</div>
	
		<div class="clear"></div>
		</div>
		<div id="footer">
	    	<p><?php printf( __( '%s is proudly powered by <a href="http://wordpress.org">WordPress</a> and <a href="http://buddypress.org">BuddyPress</a>', 'buddypress' ), get_bloginfo( 'name' ) ); ?></p>

			<?php do_action( 'bp_footer' ) ?>
		</div><!-- #footer -->

		<?php do_action( 'bp_after_footer' ) ?>

		<?php wp_footer(); ?>
		<a class="feedback" href="/contact-us/">Contact Us</a>

	</body>
</html>
