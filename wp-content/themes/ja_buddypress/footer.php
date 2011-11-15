		
		</div> <!-- #container -->

		<?php do_action( 'bp_after_container' ) ?>
		<?php do_action( 'bp_before_footer' ) ?>
		<div class="column-footer">
			<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer') ) ?>
		
			<div class="widget widget_text"><h2>Connect</h2>	
				<div class="textwidget">
					<ul>
<?php if(!is_user_logged_in()){?><li><img style=' background-color:#fff;' src='http://www.journalismaccelerator.com/wp-content/uploads/2011/09/ja_14.png'/> <a href="#" onclick="showRPX('rpxlogin');">Login</a></li><?php } ?>
						<li><a href="https://twitter.com/journaccel" class="twitter-follow-button" data-show-count="false">Follow @journaccel</a>
<script src="//platform.twitter.com/widgets.js" type="text/javascript"></script></li>
						<li><a href="/Feed/"><img src="/feed.png" alt="Feed Icon">  </a><a href="/feed/">RSS Feed</a></li>
						
					</ul>
				</div>
			</div>
	
		<div class="clear"></div>
		</div>
		<div id="footer">
	    	<p>Journalism Accelerator | PO Box 80191 | 7805 SW 40th Avenue | Portland, OR 97280<br />
	    	<?php printf( __( '%s is proudly powered by <a href="http://wordpress.org">WordPress</a> and <a href="http://buddypress.org">BuddyPress</a>', 'buddypress' ), get_bloginfo( 'name' ) ); ?></p>

			<?php do_action( 'bp_footer' ) ?>
		</div><!-- #footer -->

		<?php do_action( 'bp_after_footer' ) ?>

		<?php wp_footer(); ?>
		<a class="feedback" href="/contact-us/">Contact Us</a>

	</body>
</html>
