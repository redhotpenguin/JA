<?php

/*
Plugin Name: Ask a Question/Suggest a Resource
Plugin URI: http://ja.redhotpenguin.com/
Description: Widgetized Form for Journalism Accelerator (using FormStack)
Author: Jeremy
Version: 1
*/

function r_fill_email() {
	if (is_user_logged_in()) {
		global $current_user;
		get_currentuserinfo();
		if ($current_user->user_email) echo $current_user->user_email;
	}
}

function r_fill_first() {
	if (is_user_logged_in()) {
		global $current_user;
		get_currentuserinfo();
		if ($current_user->user_firstname) echo $current_user->user_firstname;
	}
}

function r_fill_last() {
	if (is_user_logged_in()) {
		global $current_user;
		get_currentuserinfo();
		if ($current_user->user_lastname) echo $current_user->user_lastname;
	}
}

function r_fill_ID() {
	if (is_user_logged_in()) {
		global $current_user;
		get_currentuserinfo();
		if ($current_user->user_lastname) echo $current_user->ID;
	}
}

function r_create_form() {
	?>
	<form method="post" enctype="multipart/form-data" action="http://www.formstack.com/forms/index.php" class="fsForm resource" id="fsForm1062791" <?php if(!is_category(25) || is_home()) { ?> style="display:none" <?php } ?>>
    <input type="hidden" name="form" value="1062791" />
    <input type="hidden" name="viewkey" value="dcRMIPaG0P" />
    <input type="hidden" name="hidden_fields" id="hidden_fields1062791" value="" />
    <input type="hidden" name="_submit" value="1" />
    <input type="hidden" name="incomplete" id="incomplete1062791" value="" />
    <input type="hidden" id="field11217164-first" name="field11217164-first" size="20" value="<?php r_fill_first(); ?>" class="fsField fsFieldName " />
    <input type="hidden" id="field11217164-last" name="field11217164-last" size="20" value="<?php r_fill_last(); ?>" class="fsField" />
    <input type="hidden" id="field11087077" name="field11087077" size="50" value="<?php r_fill_ID(); ?>" class="fsField " />
	<p class="explanation"><a href="/about/suggest-a-resource/">How does this work?</a></p>
    <label class="fsLabel fsRequiredLabel" for="field11086898">Suggested Resource <span class="required">*</span></label>
    <textarea id="field11217162" name="field11217162" rows="5" class="required"></textarea>
	<label class="fsLabel" for="field12391625">What inspires you to recommend this?</label>
    <input type="text" id="field12391625" name="field12391625" size="50" value="" class="fsField " style="width: 300px" />
    <label class="fsLabel fsRequiredLabel" for="field11086900">Email <span class="required">*</span></label>

 	<div style="float: left;"><input type="text" id="field11217163" name="field11217163" size="50" value="<?php r_fill_email(); ?>" class="required email" /></div><div style="float: right; margin-top: -3px;"><input id="fsSubmitButton1062791" class="fsSubmitButton" type="submit" value="Submit" /></div>
                <div class="clear">
                </div>
</form>
	<?php
}

function q_create_form() {
	?>
	<form method="post" enctype="multipart/form-data" action="http://www.formstack.com/forms/index.php" class="fsForm question" id="fsForm1058142" <?php if(!is_category(28) || is_home()) { ?>style="display:none"<?php } ?>>
    <input type="hidden" name="form" value="1058142" />
    <input type="hidden" name="viewkey" value="dcRMIPaG0P" />
    <input type="hidden" name="hidden_fields" id="hidden_fields1058142" value="" />
    <input type="hidden" name="_submit" value="1" />
    <input type="hidden" name="incomplete" id="incomplete1058142" value="" />
    <input type="hidden" id="field11086912-first" name="field11086912-first" size="20" value="<?php r_fill_first(); ?>" class="fsField fsFieldName " />
                        <input type="hidden" id="field11086912-last" name="field11086912-last" size="20" value="<?php r_fill_last(); ?>" class="fsField" />
                        <input type="hidden" id="field11087077" name="field11087077" size="50" value="<?php r_fill_ID(); ?>" class="fsField " />
						<p class="explanation"><a href="/about/ask-a-question/">How does this work?</a></p>
                        <label class="fsLabel fsRequiredLabel" for="field11086898">Your Question <span class="required">*</span>
                    </label>
                    <textarea id="field11086898" name="field11086898" rows="5" class="required"></textarea>

                    <label class="fsLabel fsRequiredLabel" for="field11086900">Email <span class="required">*</span>
                </label>
                <div style="float: left;"><input type="text" id="field11086900" name="field11086900" size="50" value="<?php r_fill_email(); ?>" class="required email" /></div><div style="float: right; margin-top: -3px;"><input id="fsSubmitButton1058142" class="fsSubmitButton" type="submit" value="Submit" /></div>
                <div class="clear">
                </div>
</form>
	<?php
}

function expand_script() {
?>
<script type="text/javascript">

jQuery(document).ready(function(){
			
	jQuery("h3.widgettitle").has("a.expand").addClass("expandheader");

	jQuery("#fsForm1058142").validate();
	jQuery("a.expand.question").click(function(event) {
		event.preventDefault();
		// jQuery("a.expand.resource").remove();
		jQuery("form.fsForm.resource").hide("fast");
		jQuery("form.fsForm.question").show("fast");
		
		/* jQuery("span.expandlink").unwrap();
		jQuery("h3.widgettitle.expandheader").removeClass("expandheader"); */
	});


	jQuery("#fsForm1062791").validate();
	jQuery("a.expand.resource").click(function(event) {
		event.preventDefault();
		// jQuery("a.expand.question").remove();
		jQuery("form.fsForm.question").hide("fast");
		jQuery("form.fsForm.resource").show("fast");
		
		/* jQuery("span.expandlink").unwrap();
		jQuery("h3.widgettitle.expandheader").removeClass("expandheader"); */
	});
});
</script>
<?php
}

function widget_suggest_a_resource($args) {
  extract($args);
  echo $before_widget;
  if (is_category(25) || in_category(25) && !is_home()) {
	  echo $before_title;
	  echo "What's Missing?";
	  echo $after_title;
	  echo "<p style=\"margin-bottom:0\">Would you like to share a resource on the Journalism Accelerator? Make a suggestion. Tell us why others need to know.</p>";
  }
  
    if (is_category(28) && !is_home()) {
	  echo $before_title;
	  echo "Ask a Question";
	  echo $after_title;
	  echo "<p style=\"margin-bottom:0\">Do you have a question for the Journalism Accelerator team? Let us know the question on your mind you think the broader community should be asking.</p>";
  }
  
 if (!is_category(25) && !is_category(28)) {
  echo $before_title;
?>

<?php if (!in_category(25) || is_home()) { ?><a href="#askaquestion" class="expand question"><span class="expandlink">Ask a Question</span></a><?php } ?>
<a href="#suggestaresource" class="expand resource"><span class="expandlink">Suggest a Resource</span></a>

<?php echo $after_title;
	}
  q_create_form();
  r_create_form();

  echo $after_widget;
}


function suggest_a_resource_init()
{
  register_sidebar_widget(__('Ask a Question and Suggest a Resource'), 'widget_suggest_a_resource');
}

add_action("plugins_loaded", "suggest_a_resource_init");
add_action("wp_head", "expand_script");

?>
