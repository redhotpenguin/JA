<div class="wrap">
	<?php    echo "<h2>" . __( 'Meta Keywords & Description', 'meta_settings' ) . "</h2>"; ?><br />
	<?php 
	if (!defined('ABSPATH')) {
		return ;
	}
	
	$plugin = dirname(__FILE__);
	$meta_lang = 'en';
	$meta_lang = get_option('meta-lang');
	require_once("$plugin/langs/$meta_lang.php");
	$lang = $GLOBALS['lang'];
	
	if($_POST['meta_hidden'] == 'Y') {  
		$dbpwd = htmlspecialchars($_POST['meta_lang']);  
		update_option('meta-lang', $dbpwd);
		
		$dbpwd = htmlspecialchars($_POST['meta_keywords']);  
		update_option('meta-keywords', $dbpwd); 
		
		$dbpwd = $_POST['meta_keywords_usage'];  
		update_option('meta-keywords-usage', $dbpwd);
		
		$dbpwd = htmlspecialchars($_POST['meta_description']);  
		update_option('meta-description', $dbpwd);
		
		print "<div class=\"updated\"><p><strong>". $lang['updated'] ."</strong></p></div>";
	}
	?>
	
	<form name="ats_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="meta_hidden" value="Y">
		<div id="poststuff" class="metabox-holder"> 
			<div class="stuffbox"> 
			<h3><?php echo $lang['language']; ?></h3>
			<div class="inside"> 
				<input type="radio" name="meta_lang" value="bg" <?php if (get_option('meta-lang') == "bg") { echo 'checked="yes"'; } ?>/> Български<br />
				<input type="radio" name="meta_lang" value="en" <?php if (get_option('meta-lang') == "en") { echo 'checked="yes"'; } ?>/> English<br />
				<input type="radio" name="meta_lang" value="de" <?php if (get_option('meta-lang') == "de") { echo 'checked="yes"'; } ?>/> Deutsch<br />
				<input type="radio" name="meta_lang" value="fr" <?php if (get_option('meta-lang') == "fr") { echo 'checked="yes"'; } ?>/> French
			</div> 
			</div> 
		</div> 
		<div id="poststuff" class="metabox-holder"> 
			<div class="stuffbox"> 
			<h3><?php echo $lang['keywords']; ?></h3>
			<div class="inside"> 
				<textarea name="meta_keywords" style="width: 100%; height: 70px;"><?php echo get_option('meta-keywords'); ?></textarea><br /><br />
			</div> 
			</div> 
		</div> 
		<div id="poststuff" class="metabox-holder"> 
			<div class="stuffbox"> 
			<h3><?php echo $lang['keywords-use']; ?></h3>
			<div class="inside"> 
				<input type="radio" name="meta_keywords_usage" value="0" <?php if (get_option('meta-keywords-usage') == "0") { echo 'checked="yes"'; } ?>/> <?php echo $lang['keywords-0']; ?><br />
			<input type="radio" name="meta_keywords_usage" value="1" <?php if (get_option('meta-keywords-usage') == "1") { echo 'checked="yes"'; } ?>/> <?php echo $lang['keywords-1']; ?><br />
			<input type="radio" name="meta_keywords_usage" value="2" <?php if (get_option('meta-keywords-usage') == "2") { echo 'checked="yes"'; } ?>/> <?php echo $lang['keywords-2']; ?>
			</div> 
			</div> 
		</div> 
		<div id="poststuff" class="metabox-holder"> 
			<div class="stuffbox"> 
			<h3><?php echo $lang['description']; ?></h3>
			<div class="inside"> 
				<textarea name="meta_description" style="width: 100%; height: 70px;"><?php echo get_option('meta-description'); ?></textarea>
			</div> 
			</div> 
		</div> 	
		<p class="submit">
			<input type="submit" name="Submit" value="<?php echo $lang['save']; ?>" />
		</p>
	</form>
	<div id="poststuff" class="metabox-holder"> 
		<div class="stuffbox"> 
			<h3>Copyright</h3>
			<div class="inside"> 
			<ul>
				<li><b><?php echo $lang['author']; ?>:</b> <a href="http://www.svil4ok.com/" target="_blank"><?php echo $lang['name']; ?></a></li>
				<li><b><?php echo $lang['url']; ?>:</b> <a href="http://wordpress.org/extend/plugins/wp-meta-keywords-meta-description/" target="_blank">http://wordpress.org/extend/plugins/wp-meta-keywords-meta-description/</a></li>
				<li><b><?php echo $lang['version']; ?>:</b> 0.8</li>
				<li><b><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=svil4ok%40networx-bg%2ecom&item_name=Meta+Keywords+Description&item_number=Support+Open+Source&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP+DonationsBF&charset=UTF%2d8"><?php echo $lang['donate']; ?></a></b></li>
			</ul>
			</div> 
		</div> 
	</div>
</div>