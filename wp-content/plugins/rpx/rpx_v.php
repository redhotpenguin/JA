<?php
// [rpxshare link="permalink" title="the_title" style="icon" label="option label" comment="false" imgsrc="img url"]summary[/rpxshare]
function rpxshare_shortcode($atts, $content=null) {
  $output = '';
  $rpx_social_option = get_option(RPX_SOCIAL_OPTION);
  $rpx_social_pub = get_option(RPX_SOCIAL_PUB);
  if ($rpx_social_option !== 'true' || empty($rpx_social_pub) ){
    return $output;
  }
  $att_def = array();
  $att_def['link']    = '';
  $att_def['title']   = '';
  $att_def['style']   = 'icon';
  $att_def['label']   = '';
  $att_def['comment'] = 'false';
  $att_def['imgsrc']  = '';
  $attributes = shortcode_atts($att_def, $atts);
  if ($attributes['comment'] == 'true') {
    $attributes['comment'] = true;
  } else {
    $attributes['comment'] = false;
  }
  if ( empty($content) ) {
    $content = '';
  }
  $output = rpx_social_share($content, $attributes['comment'], $attributes['style'], $attributes['label'], $attributes['title'], $attributes['link'], $attributes['imgsrc']);
  if ( empty($output) ) {
    $output = '';
  }
  return $output;
}
// [rpxlogin redirect="http://www.janrain.com" prompt="Cool Link" style="large"]
function rpxlogin_shortcode($atts, $content=null) {
  $output = '';
  $att_def = array();
  $att_def['redirect']    = '';
  $att_def['prompt']   = '';
  $att_def['style']   = 'small';
  $attributes = shortcode_atts($att_def, $atts);
  if ( !empty($attributes['redirect']) ) {
    rpx_set_redirect($attributes['redirect']);
  }
  if ($attributes['style'] === 'large') {
    $output = rpx_large_buttons($attributes['prompt']);
  } else {
    $output = rpx_small_buttons($attributes['prompt']);
  }
  if ( empty($output) ) {
    $output = '';
  }
  return $output;
}

function rpx_js_escape($string){
  if ( empty($string) ){
    $string = ' ';
  }
  $string = strip_tags($string);
  $string = str_replace("\0",' ',$string);
  $string = str_replace("\n",' ',$string);
  $string = str_replace("\r",' ',$string);
  $string = str_replace('  ', ' ', $string);
  $string = strip_shortcodes($string);
  return addcslashes($string,"\\\'\"&\n\r<>");
}

function rpx_inline_stylesheet(){

?>
<style type="text/css">
/* Janrain Engage plugin dynamic CSS elements */
.rpx_counter {
  background-image:url('<?php echo RPX_IMAGE_URL; ?>bubble-32.png');
}
.rpx_ct_total {
  background-image:url('<?php echo RPX_IMAGE_URL; ?>bubble-short.png');
}
.rpx_size30 {
  background-image:url('<?php echo RPX_IMAGE_URL; ?>rpx-icons30.png');
}
.rpx_size16 {
  background-image:url('<?php echo RPX_IMAGE_URL; ?>rpx-icons16.png');
}
</style>
<?php
}

function rpx_inline_javascript(){
  global $rpx_inline_js_done;
  if ($rpx_inline_js_done === true) {
    return false;
  }
  $rpx_social_option = get_option(RPX_SOCIAL_OPTION);
  $rpx_social_pub = get_option(RPX_SOCIAL_PUB);
  if ($rpx_social_option !== 'true' || empty($rpx_social_pub) ){
    return false;
  }
?>
<script type="text/javascript"><!--
function rpxWPsocial (rpxLabel, rpxSummary, rpxLink, rpxLinkText, rpxComment, rpxImageSrc, rpxPostID, rpxElement){
  if (typeof console != 'object') {
    //Dummy console log.
    var console = new Object();
    console.data = new Array();
    console.log = function(err) {
      this.data.push(err);
    }
  }
  RPXNOW.init({appId: '<?php echo get_option(RPX_APP_ID_OPTION); ?>', xdReceiver: '<?php echo RPX_PLUGIN_URL; ?>rpx_xdcomm.html'});
  RPXNOW.loadAndRun(['Social'], function () {
    var activity = new RPXNOW.Social.Activity(
       rpxLabel,
       rpxLinkText,
       rpxLink);
    activity.setUserGeneratedContent(rpxComment);
    activity.setDescription(rpxSummary);
    if (document.getElementById('rpxshareimg') != undefined && (rpxImageSrc == '' || rpxImageSrc == null)) {
      rpxImageSrc = document.getElementById('rpxshareimg').src;
    }
    if (rpxImageSrc != '' && rpxImageSrc != null) {
      var shareImage = new RPXNOW.Social.ImageMediaCollection();
      shareImage.addImage(rpxImageSrc,rpxLink);
      activity.setMediaItem(shareImage);
    }
    RPXNOW.Social.publishActivity(activity,
      {finishCallback:function(data){
        var rpxSharePost = new Array();
        var rpxShareParams = new Array();
        for (i in data) {
          try {
            var theData = data[i];
            if (theData.success == true && (rpxPostID != '' || rpxPostID != null)) {
              rpxSharePost[i] = new XMLHttpRequest();
              rpxSharePost[i].myData = theData;
              rpxSharePost[i].onreadystatechange=function() {
                if (this.readyState==4 && this.status==200) {
                  var rpxShareData = JSON.parse(this.responseText);
                  if (rpxShareData.stat == 'ok') {
                    var theDivs = rpxElement.getElementsByTagName('div');
                    var theTotal = 0;
                    var totalDiv = null;
                    for (n in theDivs) {
                      try {
                        var theDiv = theDivs[n];
                        if (typeof theDiv == 'object') {
                          var classReg = new RegExp('rpx_ct_'+this.myData.provider_name); 
                          if (theDiv.getAttribute('class').search(classReg) >= 0) {
                            var theCount = Number(theDiv.innerHTML);
                            theCount++;
                            theTotal++;
                            theDiv.innerHTML = String(theCount);
                            try {
                              rpx_showhide(theDiv);
                            } catch(err) {
                              console.log(err);
                            }
                          }
                          classReg = new RegExp('rpx_ct_total'); 
                          if (theDiv.getAttribute('class').search(classReg) >= 0) {
                            totalDiv = theDiv;
                          }
                        }
                      } catch(err) {
                        console.log(err);
                      }
                    }
                    if (totalDiv != null) {
                      totalDiv.innerHTML = String(Number(totalDiv.innerHTML) + theTotal);
                      totalDiv = null;
                    }
                  }
                }
              }
              rpxShareParams[i] = '?post_id='+encodeURIComponent(rpxPostID);
              rpxShareParams[i] += '&provider='+encodeURIComponent(theData.provider_name);
              rpxShareParams[i] += '&share_url='+encodeURIComponent(theData.provider_activity_url);
              rpxSharePost[i].open('GET','<?php echo RPX_PLUGIN_URL; ?>rpx_sharePost.php'+rpxShareParams[i],true);
              rpxSharePost[i].send();
            }
          } catch(err) {
            console.log(err);
          }
        }
      }
    });
  });
}
<?php
if ( get_option(RPX_SHARE_COUNT_OPTION) == 'hover' ) {
?>
function rpx_jquery_load() {
  if (typeof jQuery != 'undefined') {
    if (typeof $ == 'undefined') {
      $ = jQuery;
    }
    try{
      rpx_effects();
    }catch(err){
    }
    return true;
  }
  if (typeof rpx_jquery_written == 'undefined'){
    document.write("<scr" + "ipt type=\"text/javascript\" src=\"<?php echo RPX_PLUGIN_URL; ?>/files/jquery-1.6.2.min.js\"></scr" + "ipt>");
    rpx_jquery_written = true;
  }
  setTimeout('rpx_jquery_load()', 60);
  return false;
}
rpx_jquery_load();
<?php
}
?>
//--></script>
<?php
  $rpx_inline_js_done = true;
  return true;
}


function rpx_print_messages(){
  global $rpx_messages;
  $output = '';
  $id = 0;
  foreach ($rpx_messages as $key => $message_array){
    if ( empty($message_array['message']) ){
      $message_array['message'] = 'Empty message.';
    }
    if ( empty($message_array['class']) ){
      $message_array['class'] = 'message';
    }
    if ($message_array['class'] != 'html'){
      $message_array['class'] = htmlentities($message_array['class']);
      $message_array['message'] = nl2br(htmlentities($message_array['message']));
      $message_array['message'] = str_replace('[br]', '<br>', $message_array['message']);
    }else{
      $message_array['class'] = 'message updated';
    }
    $id++;
    $output .= '<p id="rpx_message_'.$id.'" class="'.$message_array['class'].'">'.RPX_MESSAGE_PRE.$message_array['message'].'</p>';
  }
  if ( !empty($output) ){
    echo '<div id="rpxmessage" class="message">'.$output.'</div>';
  }
}

function rpx_admin_menu_view(){
  if (current_user_can(RPX_OPTIONS_ROLE) === false) {
    return false;
  }
  global $rpx_comment_actions;
  global $rpx_http_vars;

  if ($rpx_http_vars['rpx_cleanup'] == 'true'){
    rpx_clean_locked();
  }
  $rpx_apikey = get_option(RPX_API_KEY_OPTION);
  if (strlen($rpx_apikey) != 40){
    rpx_message ('<strong>Enter valid API key.</strong><br />'
                .'Get your apiKey '
                 .'<a target="new" href="https://login.janrain.com/openid/v2/signin?token_url=https%3A%2F%2Frpxnow.com%2Ffinish">here</a>'
                 .' if you have an Engage account.<br />'
                .'View the account options '
                 .'<a target="new" href="http://www.janrain.com/products/engage/get-janrain-engage">here</a>'
                 .' to become a Janrain Engage customer.', 'html');
  }
  $rpx_test = rpx_test_api();
  $messages = array();
  $messages['select'] = 'No supported HTTP option.';
  $messages['post'] = 'Unable to send POST to Engage server.';
  $messages['api']  = 'Unable to retrive data from Engage API.';
  $messages['ssl_valid'] = 'This PHP does not support SSL certificate validation, certificate testing disabled.';
  foreach ($messages as $key=>$message){
    if ($rpx_test[$key] === false){
      rpx_message ($message, 'error');
    }
  }
?>
<style type="text/css">
.rpx_tr_sub td {
  padding-left:40px;
}
.rpx_tr_lt {
  background-color:#EEE;
}
.rpx_tr_dk {
  background-color:#DDD;
}
.rpx_td_left {
  text-align:left;
}
.rpx_note {
  float:left;
}
</style>
<script type="text/javascript">
function rpxshowhide(box,val) {
  if(document.getElementById(box).checked==true) {
    document.getElementById(val).style.visibility="visible";
  } else {
    document.getElementById(val).style.visibility="hidden";
  }
}
</script>
<div class="wrap">
<h2>Janrain Engage Setup</h2>
<?php rpx_print_messages();  echo $rpx_test['select']; ?>
<form method="post" action="options.php">
  <?php settings_fields( 'rpx_settings_group' ); ?>
  <table class="form-table">
    <tr class="rpx_tr_dk">
      <td class="rpx_td">
      <label for="rpxapikey">Engage API Key: </label>
      <input id="rpxapikey" type="text" name="<?php echo RPX_API_KEY_OPTION; ?>" style="width:40em;" value="<?php echo get_option(RPX_API_KEY_OPTION); ?>" />
      </td>
      <td class="rpx_td">&nbsp;</td>
    </tr><?php
  if (strlen(get_option(RPX_API_KEY_OPTION)) == 40){?>
    <tr class="rpx_tr_lt">
      <td class="rpx_td">
      <h3>Sign-In Settings</h3>
      Setup Sign-In widget <a target="_blank" href="<?php echo get_option(RPX_ADMIN_URL_OPTION); echo RPX_SIGNIN_SETUP_URL; ?>">here</a>.<br />
      Add your site domain to the list <a target="_blank" href="<?php echo get_option(RPX_ADMIN_URL_OPTION); echo RPX_SETTINGS_SETUP_URL; ?>">here</a>.<br />
      Click save to update Engage provider icons.<?php echo rpx_small_buttons(); ?>
      </td>
      <td class="rpx_td">Expert widget options:
      <br>
      <label for="rpxparamstxt">Additional iframe URL parameters (use &amp; to separate):</label>
      <input id="rpxparamstxt" name="<?php echo RPX_PARAMS_OPTION; ?>" type="text" size=50 value="<?php
  $rpx_params_txt = get_option(RPX_PARAMS_OPTION);
  if ( empty($rpx_params_txt) ) {
    $rpx_params_txt = RPX_PARAMS_OPTION_DEFAULT;
  }
  echo $rpx_params_txt;
  ?>" />
      </td>
    </tr>
    <tr class="rpx_tr_lt rpx_tr_sub">
      <td class="rpx_td">
      <label for="rpxvemail">Allow sign in based on verifiedEmail match:</label>
      <input id="rpxvemail" type="checkbox" name="<?php echo RPX_VEMAIL_OPTION; ?>" value="true"<?php if (get_option(RPX_VEMAIL_OPTION) == 'true'){ echo ' checked="checked"'; } ?> />
      </td>
      <td class="rpx_td">&nbsp;</td>
    </tr>
    <tr id="rpx_comment_option" class="rpx_tr_lt rpx_tr_sub">
      <td class="rpx_td">
       <span  class="rpx_note"><label for="rpxcomm">Login link for comments:</label>
       <select id="rpxcomm" name="<?php echo RPX_COMMENT_OPTION; ?>">
         <option value="none"<?php if (get_option(RPX_COMMENT_OPTION) == 'none'){ echo ' selected="selected"'; } ?>>None</option><?php
    foreach($rpx_comment_actions as $key => $val){?>
         <option value="<?php echo $val; ?>"<?php if (get_option(RPX_COMMENT_OPTION) == $val){ echo ' selected="selected"'; } ?>><?php echo $key; ?></option><?php
    }?>
       </select></span>
       <span>*&nbsp;Wordpress&nbsp;3&nbsp;themes.<br />&sup1;&nbsp;For&nbsp;&quot;registered&nbsp;and&nbsp;logged&nbsp;in&nbsp;to&nbsp;comment&quot;</span>
      </td>
      <td class="rpx_td_left">&nbsp;</td>
    </tr>
    <tr class="rpx_tr_dk">
      <td class="rpx_td">
      <h3>Sign-In Registration</h3>
      Click <a href="?page=<?php echo RPX_MENU_SLUG; ?>&amp;rpx_cleanup=true">here</a> to remove Engage incomplete (no email) accounts older than <?php echo RPX_CLEANUP_AGE; ?> minutes old.
      </td>
      <td class="rpx_td">&nbsp;</td>
    </tr>
    <tr class="rpx_tr_dk rpx_tr_sub">
      <td class="rpx_td"><?php
    $rpx_reg_mesg = '';
    if (get_option('users_can_register') != 1){
      $rpx_reg_mesg = '(You must enable the Wordpress General Setting for Membership "<a href="./options-general.php#users_can_register">Anyone can register</a>".)';
    }
?>
      <label for="rpxautoreg">Enable automatic user registration <?php echo $rpx_reg_mesg; ?></label>
      <input id="rpxautoreg" type="checkbox" name="<?php echo RPX_AUTOREG_OPTION; ?>" value="true"<?php if (get_option(RPX_AUTOREG_OPTION) == 'true'){ echo ' checked="checked"'; } ?> />
      </td>
      <td class="rpx_td">
      <?php if (get_option(RPX_AUTOREG_OPTION) == 'true'){ ?>
      <label for="rpxverifyname">Force users to select username:</label>
      <input id="rpxverifyname" type="checkbox" name="<?php echo RPX_VERIFYNAME_OPTION; ?>" value="true"<?php if (get_option(RPX_VERIFYNAME_OPTION) == 'true'){ echo ' checked="checked"'; } ?> />
      <?php } else { ?>
      &nbsp;
      <?php } ?>
      </td>
    </tr>
    <tr class="rpx_tr_lt">
      <td class="rpx_td">
      <h3>General User Experience</h3>
      </td>
      <td class="rpx_td">&nbsp;</td>
    </tr>
    <tr class="rpx_tr_lt rpx_tr_sub">
      <td class="rpx_td">
      <label for="rpxavatar">Use social provider avatars on comments:</label>
      <input id="rpxavatar" type="checkbox" name="<?php echo RPX_AVATAR_OPTION; ?>" value="true"<?php if (get_option(RPX_AVATAR_OPTION) == 'true'){ echo ' checked="checked"'; } ?> />
      </td>
      <td class="rpx_td">&nbsp;</td>
    </tr>
    <tr class="rpx_tr_lt rpx_tr_sub">
      <td class="rpx_td">
      <label for="rpxremovable">Allow users to remove their Engage provider and data:</label>
      <input id="rpxremovable" type="checkbox" name="<?php echo RPX_REMOVABLE_OPTION; ?>" value="true"<?php if (get_option(RPX_REMOVABLE_OPTION) == 'true'){ echo ' checked="checked"'; } ?> />
      </td>
      <td class="rpx_td">&nbsp;</td>
    </tr>
    <?php
    $rpx_social_pub = get_option(RPX_SOCIAL_PUB);
    if ( !empty($rpx_social_pub) ){?>
    <tr class="rpx_tr_dk">
      <td class="rpx_td">
      <h3>Social Sharing Settings</h3>
      Setup Social Sharing widget <a target="_blank" href="<?php echo get_option(RPX_ADMIN_URL_OPTION); echo RPX_SOCIAL_SETUP_URL; ?>">here</a>.
      </td>
      <td class="rpx_td">&nbsp;</td>
    </tr>
    <tr class="rpx_tr_dk rpx_tr_sub">
      <td class="rpx_td">
      <label for="rpxsocial">Enable social sharing:</label>
      <input onclick="rpxshowhide('rpxsocial','rpx_share_option');rpxshowhide('rpxsocial','rpx_share_option2');" id="rpxsocial" type="checkbox" name="<?php echo RPX_SOCIAL_OPTION; ?>" value="true"<?php if (get_option(RPX_SOCIAL_OPTION) == 'true'){ echo ' checked="checked"'; } ?> />
      </td>
      <td class="rpx_td">&nbsp;</td>
    </tr>
    <tr id="rpx_share_option" class="rpx_tr_dk rpx_tr_sub">
      <td class="rpx_td">
       <label for="rpxsloc">Share link on articles:</label>
       <select id="rpxsloc" name="<?php echo RPX_S_LOC_OPTION; ?>">
         <option value="none"<?php if (get_option(RPX_S_LOC_OPTION) == 'none'){ echo ' selected="selected"'; } ?>>None</option>
         <option value="top"<?php if (get_option(RPX_S_LOC_OPTION) == 'top'){ echo ' selected="selected"'; } ?>>at opening of article</option>
         <option value="bottom"<?php if (get_option(RPX_S_LOC_OPTION) == 'bottom'){ echo ' selected="selected"'; } ?>>at closing of article</option>
       </select>
      </td>
      <td class="rpx_td">&nbsp;</td>
    </tr>
    <tr id="rpx_share_option2" class="rpx_tr_dk rpx_tr_sub">
      <td class="rpx_td">
      <label for="rpxsoccom">Share link on comments:</label>
      <input id="rpxsoccom" type="checkbox" name="<?php echo RPX_SOCIAL_COMMENT_OPTION; ?>" value="true"<?php if (get_option(RPX_SOCIAL_COMMENT_OPTION) == 'true'){ echo ' checked="checked"'; } ?> />
      </td>
      <td class="rpx_td">
       <label for="rpxshct">Show count bubbles:</label>
       <select id="rpxshct" name="<?php echo RPX_SHARE_COUNT_OPTION; ?>">
         <option value="false"<?php if (get_option(RPX_SHARE_COUNT_OPTION) == 'false'){ echo ' selected="selected"'; } ?>>none</option>
         <option value="hover"<?php if (get_option(RPX_SHARE_COUNT_OPTION) == 'hover'){ echo ' selected="selected"'; } ?>>on mouse hover</option>
         <option value="always"<?php if (get_option(RPX_SHARE_COUNT_OPTION) == 'always'){ echo ' selected="selected"'; } ?>>always on</option>
       </select>
    </tr>
    <tr id="rpx_share_option3" class="rpx_tr_dk rpx_tr_sub">
      <td class="rpx_td">
       <label for="rpxsloc">Share link style:</label>
       <select id="rpxsloc" name="<?php echo RPX_S_STYLE_OPTION; ?>">
         <option value="icon"<?php if (get_option(RPX_S_STYLE_OPTION) == 'icon'){ echo ' selected="selected"'; } ?>>Icons</option>
         <option value="label"<?php if (get_option(RPX_S_STYLE_OPTION) == 'label'){ echo ' selected="selected"'; } ?>>Text</option>
       </select>
      </td>
      <td class="rpx_td">
        <label for="rpxsharetxt">Share label/button text (use &amp;nbsp; for none):</label>
        <input id="rpxsharetxt" name="<?php echo RPX_S_TXT_OPTION; ?>" type="text" size=30 value="<?php
  $rpx_s_txt = get_option(RPX_S_TXT_OPTION);
  if ( empty($rpx_s_txt) ) {
    $rpx_s_txt = RPX_S_TXT_OPTION_DEFAULT;
  }
  echo $rpx_s_txt;
  ?>" />
      </td>
    </tr>
    <script type="text/javascript">rpxshowhide('rpxsocial','rpx_share_option');rpxshowhide('rpxsocial','rpx_share_option2');rpxshowhide('rpxsocial','rpx_share_option3');</script><?php
    }else{?>
    <tr class="rpx_tr_dk rpx_tr_sub">
      <td class="rpx_td">
      Visit your <a href="<?php echo get_option(RPX_ADMIN_URL_OPTION); echo RPX_SOCIAL_SETUP_URL; ?>" target="_blank">Social Widget Setup</a> if you would like to enable social sharing.<br />
      To update the plugin you must click Save after you are done.
      </td>
      <td class="rpx_td">&nbsp;</td>
    </tr><?php
    }
  }?>
  </table>
  <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
  </p>
</form>
</div><?php
}

function rpx_admin_advanced_menu_view() {
  if (current_user_can(RPX_OPTIONS_ROLE) === false) {
    return false;
  }
  global $rpx_advanced;
  ?>
<style type="text/css">
.rpx_tr_lt {
  background-color:#EEE;
}
.rpx_tr_dk {
  background-color:#DDD;
}
.rpx_td {
  text-align:left;
}
.rpx_td label {
  display:block;
  float:left;
  width:180px;
}
.rpx_string {
  width:220px;
}
</style>
<div class="wrap">
<h2>Janrain Engage Expert Options</h2>
<?php rpx_print_messages(); ?>
<p style="font-weight:bold">Do not change these if you are unsure. Erase any setting and Save to reset to default.</p>
<form method="post" action="options.php">
  <table class="form-table">
  <?php settings_fields( 'rpx_advanced_settings_group' );
  $rpx_advanced_array = get_option(RPX_ADVANCED_OPTION);
  $rpx_flip = 'lt';
  foreach($rpx_advanced as $key => $val) {
    if ( !empty($rpx_advanced_array[$key]) ) {
      $value = $rpx_advanced_array[$key];
    }else{
      $value = $val['default'];
    }
  ?>
    <tr class="rpx_tr_<?php echo $rpx_flip; if ($rpx_flip == 'lt') { $rpx_flip = 'dk'; }else{ $rpx_flip = 'lt'; } ?>">
      <td class="rpx_td">
      <label class="rpx_label" for="<?php echo $key; ?>"><?php echo $key; ?>: </label>
      <input id="<?php echo $key; ?>" class="rpx_string" type="text" name="<?php echo RPX_ADVANCED_OPTION.'['.$key.']'; ?>" value="<?php echo $value; ?>" style="width:100%"/>
      </td>
      <td class="rpx_td"><?php echo $val['desc']; ?></td>
    </tr><?php
  }
  ?>
  </table>
  <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
  </p>
</form>
</div>
<?php
}

function rpx_admin_string_menu_view() {
  if (current_user_can(RPX_OPTIONS_ROLE) === false) {
    return false;
  }
  global $rpx_strings;
  ?>
<style type="text/css">
.rpx_tr_lt {
  background-color:#EEE;
}
.rpx_tr_dk {
  background-color:#DDD;
}
.rpx_td {
  text-align:left;
}
.rpx_td label {
  display:block;
  float:left;
  width:180px;
}
.rpx_string {
  width:220px;
}
</style>
<div class="wrap">
<h2>Janrain Engage Strings</h2>
<?php rpx_print_messages(); ?>
<form method="post" action="options.php">
  <table class="form-table">
  <?php settings_fields( 'rpx_string_settings_group' );
  $rpx_strings_array = get_option(RPX_STRINGS_OPTION);
  $rpx_flip = 'lt';
  foreach($rpx_strings as $key => $val) {
    if ( !empty($rpx_strings_array[$key]) ) {
      $value = $rpx_strings_array[$key];
    }else{
      $value = $val['default'];
    }
  ?>
    <tr class="rpx_tr_<?php echo $rpx_flip; if ($rpx_flip == 'lt') { $rpx_flip = 'dk'; }else{ $rpx_flip = 'lt'; } ?>">
      <td class="rpx_td">
      <label class="rpx_label" for="<?php echo $key; ?>"><?php echo $key; ?>: </label>
      <input id="<?php echo $key; ?>" class="rpx_string" type="text" name="<?php echo RPX_STRINGS_OPTION.'['.$key.']'; ?>" value="<?php echo $value; ?>" style="width:100%"/>
      </td>
      <td class="rpx_td"><?php echo $val['desc']; ?></td>
    </tr><?php
  }
  ?>
  </table>
  <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
  </p>
</form>
</div>
<?php
}

function rpx_admin_help_menu_view() {
  if (current_user_can(RPX_OPTIONS_ROLE) === false) {
    return false;
  }
  $readme_txt = file_get_contents(RPX_PATH_ROOT.'/readme.txt');
  $readme_txt_lines = explode("\n", $readme_txt);
  $readme_txt_clean = '';
  foreach ($readme_txt_lines as $key => $val) {
    $readme_txt_clean .= htmlentities(wordwrap($val, 90, "\n    "));
    $readme_txt_clean .= "\n";
  }
  ?>
<style type="text/css">
.rpx_pre_box {
  display:block;
  overflow:auto;
  font-family:serif;
  font-size:14px;
  width:760px;
  height:570px;
  border:5px solid #C6C6C6;
  border-top:0;
  border-right:2px solid #C6C6C6;
  background-color:transparent;
  margin:4px;
  margin-top:0;  
  padding:6px;
  padding-top:0px;
}
.rpx_pre_box p {
  font-family:serif;
  font-size:14px;
}
#rpx_help_menu {
  width:773px;  
  margin:4px;
  margin-top:16px;
  margin-bottom:0;
  padding:3px;
  background-color:#C6C6C6;
}
#rpx_help_menu span {
  padding:6px;
  margin:5px;
  background-color:#EEE;
}
</style>
<h2>Janrain Engage Help</h2>
<div id="rpx_help_menu">
<span id="rpx_setupguide_button" class="rpx_button" onclick="rpxShowById('rpx_setupguide'); return false"><a href="">setup guide</a></span>
<span id="rpx_shortcodes_button" class="rpx_button" onclick="rpxShowById('rpx_shortcodes'); return false"><a href="">shortcodes</a></span>
<span id="rpx_trouble_button" class="rpx_button" onclick="rpxShowById('rpx_trouble'); return false"><a href="">troubleshooting</a></span>
<span id="rpx_readme_button" class="rpx_button" onclick="rpxShowById('rpx_readme'); return false"><a href="">read me</a></span>
</div>
<div id="rpx_help_items">
<div id="rpx_setupguide" class="rpx_pre_box rpx_help_item">
<h3>Setup Guide</h3>
<p>
Welcome to Janrain Engage for Wordpress. This guide will help you get this plugin setup.
<br>
Use of this plugin requires an Engage application. Janrain offers Engage applications at 
 multiple service levels and with prices starting at $0. Please visit our 
 <a href="http://www.janrain.com/products/engage/features-comparison" target="_blank">feature comparison page</a>
 to find the service level that is right for your site(s).
</p><p>
Once you  
 <a href="https://rpxnow.com/" target="_blank">sign in</a> to your Engage application you 
 can easily access the API key to enable the plugin.
</p>
<h3>Sign In Setup</h3>
<p>
Copy the API key from the Application Info on the main page of your Engage dashboard and paste 
 into the API key option on the Setup panel for Janrain Engage on your Wordpress admin dashboard.
 Click the Save Settings button; the plugin will retrive the needed settings from the Engage system 
 and save those settings for you. This will unlock the full set of options.
</p><p>
Setup the sign in providers on <a href="https://rpxnow.com/" target="_blank">Engage</a>. Select 
 the sign-in for web settings. Skip to the Choose Providers step. Drag and drop your desired
 providers into the widget. If a provider has a grey gear icon it will require setup steps on
 that provider's website. When you drag the provider or click the gear you will be offered a 
 setup guide to walk you through enabling the provider. Click save when you are done.
</p><p>
After you click save you will see a link to allow you to test the widget. Please test each provider 
 to ensure that everything works. Providers that offer an email address will work best with 
 Wordpress due to the requirement for email addresses for each user. Check the 
 <a href="https://rpxnow.com/docs/providers" target="_blank">provider guide</a> to familiarize 
 yourself with what each provider offers in the data returned.
</p><p>
Add your site domain to the token URL domain list. Click the settings option on your Engage 
 dashboard to access the page with the allowed domain list. Add your domain and click save.
</p><p>
Back to the Wordpress Janrain Engage Setup page. Clicking save will update the icons shown to 
 your visitors. Click it a second time if needed.
</p><p>
The verfiedEmail match setting allows existing users to get automatically connected to thier 
 existing account if possible. This is not needed for a new site. There are some security concerns 
 around this feature. Do not enable this if security is a top concern. This feature does not work 
 to connect to the admin account (id 0). If you would like to connect this account to Engage please 
 do so via the profile page for that user.
</p><p>
The login link for comments feature can be confusing. This feature adds an Engage login prompt to
 the comment area. There are many template action hook options for this area and you may need to 
 experiment or examine the template source to find the correct option. For a template that is up to
 WP3 standards the main question is if you require sign in or not. If you do you will want the hook 
 named comment_form_must_log_in, and if not you will likely want comment_form_before_fields.
</p><p>
The plugin includes a Wordpress Sidebar Widget. Look in Appearance -&gt; Widgets to find this widget.
</p><p>
If none of these options are working you may want to look at using a shortcode in your template. 
<br>See the shortcode page for more info.
</p><p>
Hint: 
<br><em>To set a language for the widget you can use the expert option for additional URL parameters.
<br>(e.g. language_preference=fr would set French)</em>
</p>
<h3>Registration Setup</h3>
<p>
Enable automatic user registration if your blog is not using user registration customizations or 
 supplimentary user registration forms (such as BuddyPress). This feature will enable your visitors 
 register and sign in very quickly if they have a full profile shared from the Engage sign on.
</p>
<h3>General User Experience Setup</h3>
<p>
Enable social provider icons to display the avatar from the Engage provider instead of the generic 
 Wordpress icons.
</p>
<h3>Social Sharing Setup</h3>
<p>
Setup the social providers on <a href="https://rpxnow.com/" target="_blank">Engage</a>. Select 
 the social sharing for websites panel. Skip to the Choose Providers step. Drag and drop your desired
 providers into the widget. These providers will require setup steps on each provider's website. 
 When you drag the provider or click the gear you will be offered a setup guide to walk you through 
 enabling the provider. Click save when you are done.
</p><p>
<p>
Enable social sharing and click save changes to reveal the full options. These options are intended 
 to work with fully up to date WP3 templates. The JS for this widget looks for an img element with 
 id="rpxshareimg" and adds this image to the share if found.
 <br>Enable the count bubbles to track and display counters for each successful share per provider.
 <br> (counts are not tracked if this feature is not enabled)
 <br>
 <br>If you are having trouble getting these options to work you may need to add shortcodes to your 
 template. See the shortcode page for more info.
<br>
The share widget button can be displayed as a row of icons or as a text link.
</p>
</div>
<div id="rpx_shortcodes" class="rpx_pre_box rpx_help_item">
<h3>Shortcodes</h3>
<div id="rpxshare_shortcode" style="width:734px; background-color:#FEFEFE; padding:6px;">
<p><strong>Social share button</strong> (social sharing must be enabled in setup):</p>
Tag name: <em>rpxshare</em>
<br>Parameters:
<ul>
<li>link = URL for the shared comment, defaults to the permalink for the current post or page.</li>
<li>title = TEXT for the link, defaults to the title of the current post or page.</li>
<li>style = "icon" or "label", defaults to "icon" labelled icons style link. The "label" style produces a text only link.</li>
<li>label = TEXT for the link
<li>imgsrc = URL for an image to be shown with the share, defaults to none unless there is an img with id="rpxshareimg" on the page.</li>
<li>[rpxshare]TEXT[/rpxshare] - The text inside the rpxshare shortcode tags is used as the link descriptive summary for the post.</li>
</ul>
<pre style="font-size:10px;">Example shortcode:
[rpxshare link="http://www.janrain.com" title="Cool Link" style="icon" label="cool"]Welcome to social blogging.[/rpxshare]

</pre>
<div style="width:150px; float:left; position:relative;">
Example result:<br><?php 
rpx_inline_javascript();
echo do_shortcode('[rpxshare link="http://www.janrain.com" title="Cool Link" style="icon" label="cool"]Welcome to social blogging.[/rpxshare]'); ?>
</div><div class="clear">&nbsp;</div>
</div>
<div id="rpxlogin_shortcode" style="width:734px; background-color:#FEFEFE; padding:6px;">
<p><strong>Social login button</strong> (plugin must be enabled and setup):</p>
Tag name: <em>rpxlogin</em>
<br>Parameters:
<ul>
<li>redirect = URL for the shared comment, defaults to the permalink for the current post or page.</li>
<li>prompt = TEXT for the link, defaults to the title of the current post or page.</li>
<li>style = "small" or "large", defaults to "icon" labelled icons style link. The "label" style produces a text only link.</li>
</ul>
<pre style="font-size:10px;">Example shortcode:
[rpxlogin redirect="http://www.janrain.com" prompt="Authenticate!" style="large"]

</pre>
<div style="width:150px; float:left; position:relative;">
Example result:<br><?php 
echo do_shortcode('[rpxlogin redirect="http://www.janrain.com" prompt="Authenticate!" style="large"]'); ?>
</div><div class="clear">&nbsp;</div>
</div>
</div>
<div id="rpx_trouble" class="rpx_pre_box rpx_help_item">
<h3>Troubleshooting</h3>
<p>Visit the Q&amp;A discussion on the <a href="<?php echo RPX_PLUGIN_HELP_URL; ?>" target="_blank">Janrain Support site</a>.</p>
<div id="status" style="display:none"></div>
<div id="ajaxreader"></div>
</div>
<div id="rpx_readme" class="rpx_pre_box rpx_help_item">
<h3>Read Me</h3>
<pre>
<?php echo $readme_txt_clean; ?>
</pre>
</div>
</div>
<script type="text/javascript">
function rpxHideAll() {
  var theItems = document.getElementById('rpx_help_items').getElementsByTagName('div');
  if (theItems != null) {
    for(var i in theItems){
      if (theItems[i].id != null) {
        if (theItems[i].className.search('rpx_help_item') >= 0) {
          rpxHideById(theItems[i].id)
        }
      }
    }
  }
  var menuItems = document.getElementById('rpx_help_menu').getElementsByTagName('span');
  if (menuItems != null) {  
    for(var i in menuItems){
      if (menuItems[i].id != null) {
        if (menuItems[i].className.search('rpx_button') >= 0) {
          rpxClearOutById(menuItems[i].id)
        }
      }
    }
  }
}
function rpxOutById(theId) {
  document.getElementById(theId).style.outline = '1px solid #BBB';
}
function rpxClearOutById(theId) {
  document.getElementById(theId).style.outline = '';
}
function rpxShowById(theId) {
  rpxHideAll();
  document.getElementById(theId).style.display = 'block';
  rpxOutById(theId+'_button');
}
function rpxHideById(theId) {
  document.getElementById(theId).style.display = 'none';
}
rpxShowById('rpx_setupguide');
</script>
<script type="text/javascript">
var RSSRequestObject = false; // XMLHttpRequest Object
var Backend = '<?php echo RPX_PLUGIN_URL; ?>/help_feed.php'; // Backend url

if (window.XMLHttpRequest) // try to create XMLHttpRequest
  RSSRequestObject = new XMLHttpRequest();

if (window.ActiveXObject)  // if ActiveXObject use the Microsoft.XMLHTTP
  RSSRequestObject = new ActiveXObject("Microsoft.XMLHTTP");


/*
* onreadystatechange function
*/
function ReqChange() {
  // If data received correctly
  if (RSSRequestObject.readyState==4) {
    // if data is valid
    if (RSSRequestObject.responseText.indexOf('invalid') == -1) {   
      // Parsing RSS
      var node = RSSRequestObject.responseXML.documentElement; 
      // Get Channel information
      var channel = node.getElementsByTagName('channel').item(0);
      var title = channel.getElementsByTagName('title').item(0).firstChild.data;
      var link = channel.getElementsByTagName('link').item(0).firstChild.data;
      var link = '<?php echo RPX_PLUGIN_HELP_URL; ?>';
      content = '<div class="channeltitle"><a href="'+link+'" target="_blank">'+title+'</a></div><ul>';
      // Browse items
      var items = channel.getElementsByTagName('item');
      for (var n=0; n < items.length; n++) {
        var itemTitle = items[n].getElementsByTagName('title').item(0).firstChild.data.replace('in Wordpress Plugin Q&A ','').slice(0,130);
        var itemLink = items[n].getElementsByTagName('link').item(0).firstChild.data;
        try { 
          //theDateShort = items[n].getElementsByTagName('pubDate').item(0).firstChild.data;
          var theDate = new Date(items[n].getElementsByTagName('pubDate').item(0).firstChild.data);
          var theMonth = theDate.getMonth();
          theMonth++;
          var theDay = theDate.getDate();
          var theDateShort = theMonth+'/'+theDay;
          var itemPubDate = '<font color=gray>['+theDateShort+'] ';
        } 
        catch (e) { 
          var itemPubDate = '';
        }
        content += '<li>'+itemPubDate+'</font><a href="'+itemLink+'" target="_blank">'+itemTitle+'</a></li>';
      }
      content += '</ul>';
      // Display the result
      document.getElementById("ajaxreader").innerHTML = content;
      // Tell the reader the everything is done
      document.getElementById("status").innerHTML = "Done.";
    } else {
      // Tell the reader that there was error requesting data
      document.getElementById("status").innerHTML = "<div class=error>Error requesting data.<div>";
    }
    HideShow('status');
  }
}

/*
* Main AJAX RSS reader request
*/
function RSSRequest() {
  // change the status to requesting data
  HideShow('status');
  document.getElementById("status").innerHTML = "Requesting data ...";
  // Prepare the request
  RSSRequestObject.open("GET", Backend , true);
  // Set the onreadystatechange function
  RSSRequestObject.onreadystatechange = ReqChange;
  // Send
  RSSRequestObject.send(null); 
}

/*
* Timer
*/
function update_timer() {
  RSSRequest();
}


function HideShow(id){
  var el = GetObject(id);
  if(el.style.display=="none")
  el.style.display='';
  else
  el.style.display='none';
}

function GetObject(id){
  var el = document.getElementById(id);
  return(el);
}

RSSRequest();
</script>
<?php
}


function rpx_message_box($message='') {
  if ( empty($message) ){
    global $rpx_messages;
    $messages = array();
    foreach ($rpx_messages as $key=>$msg){
      if ($msg['class'] == 'message'){
        $messages[] = $msg['message'];
      }
    }
    $message = '<p>'.implode('<br />', $messages).'</p>';
  }
?>
<div id="rpxmsgbox" class="rpxbox rpxmsgbox">
<div id="rpxmsgw1" class="rpxhoriz"></div>
<table id="rpxmsgw2" class="rpxvert"><tr id="rpxvrow" class="rpxvrow"><td id="rpxvcol" class="rpxvcol">
<span id="rpxmsgborder" class="rpxborder">
<span id="rpxmsgclose" class="rpxclose" onclick="hideRPX('rpxmsgbox')"><img src="<?php echo RPX_IMAGE_URL; ?>close.png" alt="close" /></span>
<div id="rpxmsg" class="rpxmsg">
<div id="rpxmessage" class="rpxmessage"><?php echo $message; ?></div>
</div></span></td></tr></table></div>
<script type="text/javascript">
  showRPX('rpxmsgbox');
</script>
<?php
}

function rpx_register_form($collect='email') {
  global $rpx_http_vars;
  if ($rpx_http_vars['rpx_collect'] == 'username'){
    $collect = 'username';
  }
?>
<div id="rpxregbox" class="rpxbox rpxregbox" onload="showRPX('rpxregbox');">
<div id="rpxregw1" class="rpxhoriz"></div>
<table id="rpxregw2" class="rpxvert"><tr id="rpxvrow" class="rpxvrow"><td id="rpxvcol" class="rpxvcol">
<span id="rpxregborder" class="rpxborder">
<span id="rpxregclose" class="rpxclose" onclick="hideRPX('rpxregbox');"><img src="<?php echo RPX_IMAGE_URL; ?>close.png" alt="close" /></span>
<div id="rpxregister" class="rpxregister">
<?php rpx_print_messages(); ?>
<form id="rpxregform" class="rpxregform" action="" method="get">
 <input type="hidden" name="action"       value="<?php echo RPX_REGISTER_FORM_ACTION ?>" />
 <input type="hidden" name="rpx_collect"  value="<?php echo urlencode($rpx_http_vars['rpx_collect']); ?>" />
 <input type="hidden" name="rpx_session"  value="<?php echo urlencode($rpx_http_vars['rpx_session']); ?>" />
 <input type="hidden" name="rpx_provider" value="<?php echo urlencode($rpx_http_vars['rpx_provider']); ?>" />
 <input type="hidden" name="redirect_to"  value="<?php echo urlencode($rpx_http_vars['redirect_to']); ?>" />
<?php
  if ($collect == 'email'){
    echo ' <input type="hidden" name="rpx_username" value="'.urlencode($rpx_http_vars['rpx_username']).'" />'."\n";
    echo ' <p><input type="text"   name="rpx_email"    value="" id="rpxemail" class="rpxemail" size="30" /></p>';
  }elseif ($collect == 'username'){
    echo ' <input type="hidden" name="rpx_email"    value="'.urlencode($rpx_http_vars['rpx_email']).'" />'."\n";
    echo ' <p><input type="text"   name="rpx_username" value="" id="rpxusername" class="rpxusername" size="30" /></p>';
    if (RPX_REQUIRE_EULA == 'true') {
      echo ' <p><label for="rpx_eula">'.RPX_EULA_PROMPT.'</label><input id="rpx_eula" name="rpx_eula" type="checkbox" value="eula" /><a href="'.RPX_EULA_URL.'" target="_blank">EULA</a><p>';
    }
  }else{
    echo ' <input type="hidden" name="rpx_username" value="'.urlencode($rpx_http_vars['rpx_username']).'" />'."\n";
    echo ' <input type="hidden" name="rpx_email"    value="'.urlencode($rpx_http_vars['rpx_email']).'" />'."\n";
    echo $collect;//$collect as html to ask for something else, maybe useful later
  }
?>
<input id="rpxsubmit" class="rpxsubmit" type="submit" value="Submit" />
</form>
<p style='margin-top: 30px; margin-bottom:5px;'>  <a href="/support/my-account-information/">Retrieve your account information.</a></p><p>  <a href='mailto:support@journalismaccelerator.com?subject=Authentification Problem'>Contact us</a> for more information.</p>
</div>

</span></td></tr></table>

</div>
<script type="text/javascript">
  showRPX('rpxregbox');
</script>
<?php
}

function rpx_open_widget(){
  global $rpx_http_vars;
  $add_parameters = '';
  if( !empty($rpx_http_vars['rpx_username']) ){
    $add_parameters .= '&rpx_username='.urlencode($rpx_http_vars['rpx_username']);
  }
  $add_parameters = urlencode($add_parameters);
?>
<script type="text/javascript">
  document.getElementById('rpxiframe').src += '<?php echo $add_parameters; ?>';
  showRPX('rpxlogin');
</script>
<?php
}

function rpx_wp_footer(){
  global $rpx_http_vars;
  global $rpx_footer_done;
  if ($rpx_footer_done === true) {
    return false;
  }
  if ($rpx_http_vars['action'] == RPX_REGISTER_FORM_ACTION){
    rpx_register_form();
  }
  $user_data = rpx_user_data();
  if ($user_data != false && !empty($user_data->rpx_provider) && did_action('show_user_profile') === false){
    return true;
  }
  ?>
<div id="rpxlogin" class="rpxbox" style="display:none">
<div id="fiftyfifty" class="rpxhoriz"></div>
<table id="rpxvertical" class="rpxvert"><tr id="rpxvrow" class="rpxvrow"><td id="rpxvcol" class="rpxvcol">
<span id="rpx_border" class="rpxborder">
<span id="rpx_close" class="rpxclose" onclick="hideRPX('rpxlogin')"><img src="<?php echo RPX_IMAGE_URL; ?>close.png" alt="close" /></span>
<?php echo rpx_iframe_widget(); ?>
</span></td></tr></table></div>
<?php
  $rpx_footer_done = true;
}

function rpx_iframe_widget($redirect_url=NULL) {

  global $rpx_http_vars;
  $iframe = '';
  $realm = get_option(RPX_REALM_OPTION);
  $site_url = site_url();
  $permalink = get_permalink();
  if (stripos($site_url, 'https:') === false){
    $realm_scheme = 'http';
  }else{
    $realm_scheme = 'https';
  }
  if ( empty($redirect_url) && !empty($rpx_http_vars['redirect_to']) ) {
          $redirect_url = $rpx_http_vars['redirect_to'];
  }
  if ( empty($redirect_url) && !empty($permalink) && !is_front_page() ) {
	$redirect_url = $permalink;
	
  }
  if ( empty($redirect_url) && !empty($site_url) ) { // homepage
    $redirect_url = $site_url;
  }
  if ( !empty($redirect_url) ) {
    $redirect_to = urlencode($redirect_url);
  // $rpx_token_url = urlencode(RPX_TOKEN_URL . '&redirect_to=' . $redirect_to);
	$rpx_token_url = urlencode(RPX_TOKEN_URL . '&redirect_to=') . $redirect_to;

  }
  $add_params = get_option(RPX_PARAMS_OPTION);
  if ( empty($add_params) ) {
    $param_query = '';
  } else {
    $param_query = $add_params.'&';
  }
  //token_url must be the final param to allow for easy JS modification
   $iframe_src = $realm_scheme.'://'.$realm .'/openid/embed?'.$param_query.'token_url='.$rpx_token_url;
  $iframe = '<iframe id="rpxiframe" class="rpxiframe" scrolling="no" src="'.$iframe_src.'"></iframe>';

  return $iframe;
}

function rpx_login_form() {
  global $rpx_http_vars;
  if (is_user_logged_in()){
    return true;
  }
  if ($rpx_http_vars['action'] == 'register'){
    if (rpx_allow_register() === false){
      return false;
    }
    $logreg = RPX_OR_REGISTER_PROMPT;
  }else{
    $logreg = RPX_OR_LOGIN_PROMPT;
  }
  rpx_print_messages();
  if ( strstr(wp_login_url(), 'wp-login.php') !== false ) {
    rpx_wp_footer();
  }
?>
<div class="rpx_label"><?php echo $logreg; ?></div><?php echo rpx_large_buttons();
}

function rpx_login_head() {
  if ( strstr(wp_login_url(), 'wp-login.php') === false ) {
    return false;
  }
?>
<link rel='stylesheet' type='text/css' media='all' href='<?php echo RPX_FILES_URL; ?>stylesheet.css' />
<script type='text/javascript' src='<?php echo RPX_FILES_URL; ?>javascript.js'></script>
<?php
  rpx_inline_stylesheet();
}

function rpx_content_filter($content){
  $rpx_social_option = get_option(RPX_SOCIAL_OPTION);
  $rpx_social_pub = get_option(RPX_SOCIAL_PUB);
  $rpx_s_loc_option = get_option(RPX_S_LOC_OPTION);
  $in_the_loop = in_the_loop();
  if ($rpx_social_option != 'true' || empty($rpx_social_pub) || $rpx_s_loc_option == 'none' || $in_the_loop === false){
    return $content;
  }
  $rpx_social = rpx_social_share($content);
  if ($rpx_s_loc_option == 'top'){
    return $rpx_social.$content;
  }else{
    return $content.$rpx_social;
  }
}

function rpx_comment_filter($comment){
  $rpx_social_comment_option = get_option(RPX_SOCIAL_COMMENT_OPTION);
  $rpx_social_pub = get_option(RPX_SOCIAL_PUB);
  $in_the_loop = in_the_loop();
  if ($rpx_social_comment_option != 'true' || empty($rpx_social_pub) || $in_the_loop === false){
    return $comment;
  }
  $share = rpx_social_share($comment, true);
  return $comment.$share;
}

function rpx_avatar_filter($avatar){
  $rpx_avatar_option = get_option(RPX_AVATAR_OPTION);
  if ($rpx_avatar_option != 'true'){
    return $avatar;
  }
  $rpx_avatar = $avatar;
  $rpx_photo = '';
  if (in_the_loop() != false){
    $zero = 0;
    $comment = get_comment($zero);
    if (!is_wp_error($comment->user_id)){
      $user = get_userdata($comment->user_id);
      if (!is_wp_error($user)){
        if (isset($user->rpx_photo)){
          $rpx_photo = $user->rpx_photo;
        }
      }
    }
  }
  if ( !empty($rpx_photo) ) {
    $avatar = str_replace("'", '"', $avatar);
    $pattern = '/src="[^"]*"/';
    $replace = 'src="'.$rpx_photo.'"';
    $rpx_avatar = preg_replace($pattern, $replace, $avatar);
  }
  return $rpx_avatar;
}

function rpx_user_provider_icon($author_name = NULL){
  if (in_the_loop() === false && $author_name != NULL){
    return $author_name;
  }
  global $rpx_providers;
  if ($author_name != NULL){
    $zero = 0;
    $comment = get_comment($zero);
    if (!empty($comment->user_id)){
      $user = get_userdata($comment->user_id);
    }
  }else{
    $user = rpx_user_data();
    $author_name = $user->user_login;
  }
  $icon = '';
  if (!empty($user->rpx_provider)){
    $provider = $user->rpx_provider;
    if ( !empty($provider) ){
      $provider = $rpx_providers["$provider"];
      $author = $user->user_login;
      $url = $user->user_url;
      if ( !empty($user->rpx_url) ){
        $url = $user->rpx_url;
      }
      $icon = '<div class="rpx_icon rpx_size16 rpx_'.$provider.' rpx_author" title="'.$author.'"></div>';
      if (  !empty($url) ){
        $icon = '<a href="'.$url.'" rel="external nofollow" target="_blank">'.$icon.'</a>';
      }
    }
  }
  return $icon;
}

function rpx_social_share($message, $comment=false, $style=NULL, $share_label=NULL, $title=NULL, $link=NULL, $imgsrc=NULL){
  if ( empty($title) ) {
    $title = get_the_title();
  }
  $posttitle = rpx_js_escape(strip_tags($title));

  if ( empty($link) ) {
    $link = get_permalink();
  }
  $postlink = rpx_js_escape(strip_tags($link));
  
  if ( empty($share_label) ) {
    $share_label = get_option(RPX_S_TXT_OPTION);
  }
  $share_label = strip_tags($share_label);

  if ( empty($imgsrc) ) {
    $imgsrc = '';
  }

  $post_id = get_the_ID();
  
  $postsummary = rpx_js_escape(substr(strip_tags(strip_shortcodes($message)), 0, 128)).'...';
  $blogname    = rpx_js_escape(strip_tags(get_option('blogdescription')));
  $class = 'rpxsocial';
  $verb = RPX_SHARED;
  $label = RPX_SHARE_LABEL;
  if ($comment === true){
    $postlink = rpx_js_escape(strip_tags(get_comment_link()));
    $class = 'rpxsocial_small';
    $verb = RPX_COMMENTED_ON;
    $label = RPX_COMMENT_LABEL;
    $style = 'label';
  }
  if (empty($style)) {
    $style = get_option(RPX_S_STYLE_OPTION);
  }
  $rpx_share_counts = get_post_meta($post_id, RPX_POST_META_COUNTS);
  $share_icons = rpx_social_icons($share_label,$rpx_share_counts);
  switch ($style){
    case 'icon':
      $button = $share_icons;
      $button_label = '';
      break;
    case 'label':
      $button = $share_label;
      $button_label = '';
      break;
    default:
      $button = $share_icons;
      $button_label = $share_label;
      break;
  }
  $share_open  = '<div class="'.$class.'">';
  $share_button = '<div class="rpxsharebutton" onclick="rpxWPsocial(';
  $share_button .= "'".$label."','".$postsummary."','".$postlink."','"
    .$posttitle."','".$verb." ".$posttitle."','".$imgsrc."','".$post_id."', this);".'">'.$button.'</div>';
  $share_label = '';
  if ( !empty($button_label) ) {
    $share_label = '<div class="rpx_share_label">'.$button_label.'</div>';
  }
  $share_close = ' &nbsp;</div> <div class="rpx_clear"></div>';
  $share = $share_open.$share_label.$share_button.$share_close;
  return $share;
}





class RPX_Widget extends WP_Widget {
  
  function RPX_Widget() {
  
    $widget_options = array('classname' => 'rpx-widget', 'description' => 'Sign in with Janrain Engage.');
    $this->WP_Widget('janrain-engage-widget', RPX_WIDGET_TITLE, $widget_options);
  }

  function widget( $args, $instance ) {
  
    $user_data = rpx_user_data();
	$user_data->url;
	


    if ($instance['hide'] != 'true' || ($user_data == false || empty($user_data->rpx_provider))) {
  
      extract($args);
      $title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
      echo $before_widget;

      if ( !empty( $title ) ) {
        echo $before_title . $title . $after_title;
      }

      if ($user_data != false && !empty($user_data->rpx_provider) ){
  
        global $rpx_http_vars;
        $rpx_user_icon = rpx_user_provider_icon();
        $avatar = '';
        if ( !empty($user_data->rpx_photo) ) {
          $avatar = '<img class="rpx_sidebar_avatar" src="'.$user_data->rpx_photo.'">';
        }else{
          //$avatar = '<div class="rpx_sidebar_avatar">abc&nbsp;</div>';
        $avatar = '<img class="rpx_sidebar_avatar" src="'.RPX_IMAGE_URL.'default_gravatar.jpg">';
		
		}
		
		$author_name = $user_data->display_name;
		$username = bp_get_loggedin_user_username();
?>
<div id="rpx_sidebar-me">
    <div class="rpx_user_icon">
       <?php  echo $avatar; ?>
       <?php  echo $rpx_user_icon; ?>
	
	<div class="rpx_user_info">
	   <?php  echo "<h4><a class='rpx_sidebar_name' href='/members/$username'>$author_name</a></h4>"; ?>
	   		<a href="<?php echo wp_logout_url(home_url() ); ?>" class="button logout" title="Logout">Log Out</a>
			<a href="/members/<?php echo $username; ?>/profile/edit/group/1" class="edit_profile">Edit My Profile</a>
	</div>
	</div> 
	
	
</div>

 
  
  <?php
      }elseif ($user_data != false && empty($user_data->rpx_provider) ){
       // echo rpx_small_buttons(RPX_CONNECT_PROMPT);
	   
	 echo '<div id="rpxwidget"><div id="rpx_sidebar-me">';
	 
	 if( is_super_admin() )
		echo '<h3>Logged in as: Admin</h3>';
	 
	 echo '<p><a href="'.wp_logout_url( home_url() ).' " title="Log Out" class="button logout">Log out</a></p></div></div>';

	   
	   //debug 
      }else{
        echo rpx_small_buttons(RPX_LOGIN_PROMPT);
      }
      echo $after_widget;
    }
  }

  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['hide'] = strip_tags($new_instance['hide']);
    return $instance;
  }

  function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'hide' => 'false' ) );
    $instance['title'] = strip_tags($instance['title']);
    $hide_checked = ' checked="checked"';
    if ($instance['hide'] != 'true') {
      $instance['hide'] = 'false';
      $hide_checked = '';
    }
?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>

<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" /></p>

<p><label for="<?php echo $this->get_field_id('hide'); ?>"><?php _e('Hide widget when connected:'); ?></label>

<input class="checkbox" id="<?php echo $this->get_field_id('hide'); ?>" name="<?php echo $this->get_field_name('hide'); ?>" type="checkbox" value="true"<?php echo $hide_checked; ?>/></p>
<?php
  }
}



function rpx_small_buttons($label = ''){
  global $rpx_button_count;
  global $rpx_icon_override;
  global $rpx_providers;

  $providers = '';
  $rpx_provider_list = get_option(RPX_PROVIDERS_OPTION);
  if (!empty($rpx_provider_list)) {
    $rpx_provider_list = explode(',',$rpx_provider_list);
  }
  if (is_array($rpx_provider_list)){
    $providers = array_flip(array_intersect($rpx_providers,$rpx_provider_list));
    $providers = array_slice($providers, 0, RPX_SMALL_ICONS_LIMIT);
  }

  if ($rpx_icon_override === true || empty($providers)){
    global $rpx_providers_small;
    $providers = $rpx_providers_small;
  }

  if ( !empty($label) ){
    $label = '<div class="rpx_label">'.$label.'</div>';
  }
  $rpx_buttons = '';
  foreach ($providers as $key => $val){
    $rpx_buttons .= '<div class="rpx_icon rpx_size16 rpx_'.$key.'" title="'.htmlentities($val).'"></div>';
  }
  //$buttons = '<div class="rpx_button" id="rpx_button_'.++$rpx_button_count.'"><div class="rpx_small_icons" id="rpx_small_icons_'.++$rpx_button_count.'" onclick="showRPX(\'rpxlogin\')">'.$rpx_buttons.'</div></div><div class="rpx_clear"></div>';
  
  $buttons .= "<div class='rpx_button'>";
  $buttons .= "<div class='rpx_small_icons rpxsharebutton'   onclick=\"showRPX('rpxlogin')\"> <img title='Google' class='rpx_icon' src='".RPX_IMAGE_URL."google_32.png'> </div>"; 
  $buttons .= "<div class='rpx_small_icons rpxsharebutton'   onclick=\"showRPX('rpxlogin')\"> <img title='Facebook' class='rpx_icon' src='".RPX_IMAGE_URL."facebook_32.png'> </div>"; 
  $buttons .= "<div class='rpx_small_icons rpxsharebutton'   onclick=\"showRPX('rpxlogin')\"> <img title='Twitter' class='rpx_icon' src='".RPX_IMAGE_URL."twitter_32.png'> </div>"; 
  $buttons .= "<div class='rpx_small_icons rpxsharebutton'   onclick=\"showRPX('rpxlogin')\"> <img title='LinkedIn' class='rpx_icon' src='".RPX_IMAGE_URL."linkedin_32.png'> </div>"; 
  $buttons .= "<div class='rpx_clear'></div>";
  $buttons .= "</div>";
  
  return $label.$buttons;
}

function rpx_large_buttons($label = ''){
  global $rpx_button_count;
  global $rpx_icon_override;
  global $rpx_providers;

  $providers = '';
  $rpx_provider_list = get_option(RPX_PROVIDERS_OPTION);
  if (!empty($rpx_provider_list)) {
    $rpx_provider_list = explode(',',$rpx_provider_list);
  }
  if (is_array($rpx_provider_list)){
    $providers = array_flip(array_intersect($rpx_providers,$rpx_provider_list));
    $providers = array_slice($providers, 0, RPX_LARGE_ICONS_LIMIT);
  }

  if ($rpx_icon_override === true || empty($providers)){// see rpx.conf.php
    global $rpx_providers_large;
    $providers = $rpx_providers_large;
  }

  if ( !empty($label) ){
    $label = '<div class="rpx_label">'.$label.'</div>';
  }
  $rpx_buttons = '';
  foreach ($providers as $key => $val){
    $rpx_buttons .= '<div class="rpx_icon rpx_size30 rpx_'.$key.'" title="'.htmlentities($val).'"></div>';
  }
  $buttons = '<div class="rpx_button" id="rpx_button_'.++$rpx_button_count.'"><div class="rpx_large_icons" id="rpx_large_icons_'.++$rpx_button_count.'" onclick="showRPX(\'rpxlogin\')">'.$rpx_buttons.'</div></div><div class="rpx_clear"></div>';
  return $label.$buttons;
}

function rpx_edit_user_profile(){
  $user_data = rpx_user_data();
  if ( !empty($user_data->rpx_provider) ){
    $provider = htmlentities($user_data->rpx_provider);
    ?>
<h3>Currently connected to <?php echo $provider; ?></h3>
<?php
    $removable = get_option(RPX_REMOVABLE_OPTION);
    if($removable == 'true'){ 
      ?>
<p>You can remove all <?php echo $provider; ?> data and disconnect your account from <?php echo $provider; ?> by clicking <a href="?action=<?php echo RPX_REMOVE_ACTION; ?>">remove</a>.
<br><strong>Be certain before you click "remove" and set a password for this account so you can use it without social sign in.</strong></p>
<?php
    }
  }
  echo rpx_large_buttons(RPX_CONNECT_PROMPT);
}

function rpx_social_icons($label, $share_counts){
  global $rpx_providers;
  $social_pub = get_option(RPX_SOCIAL_PUB);
  $share_count_option = get_option(RPX_SHARE_COUNT_OPTION);
  if ( $share_count_option != 'false' ){
    $do_count = true;
  } else {
    $do_count = false;
  }
  if ($share_count_option == 'hover') {
    $hide_style = ' style="display: none"';
  } else {
    $hide_style = '';
  }
  $social_providers = array_filter(explode(',',$social_pub));
  $rpx_social_icons = '';
  $total = 0;
  foreach ($social_providers as $key => $val){
    $count = 0;
    $hide = $hide_style;
    $share_count = '';
    if ( $do_count === true ){
      if ( !empty($share_counts[0][$val]) ){
        $count = $share_counts[0][$val];
        $total += $count;
        //$hide = '';
      }
      $share_count = '<div class="rpx_counter rpx_ct_'.$val.'"'.$hide.'>'.$count.'</div>';
    }
    $rpx_social_icons .= '<div class="rpx_icon '.RPX_SHARE_ICON_CLASS.' rpx_'.$val.'" title="'.htmlentities(array_search($val,$rpx_providers)).'">'.$share_count.'</div>';
  }
  $total_count = '';
  if ( $do_count === true ) {
    $total_count = '<div class="rpx_ct_total" title="total">'.$total.'</div><br>';
  }
  $buttons = '<div class="rpx_share_label">'.$total_count.$label.'</div><div class="rpx_social_icons">'.$rpx_social_icons.'</div>';
  return $buttons;
}

function rpx_comment_login() {
  global $rpx_http_vars;
  rpx_set_redirect();
  if (is_user_logged_in()){
    global $current_user;
    get_currentuserinfo();
    ?>
<div id="rpxwidget"><p>Welcome <?php echo $current_user->display_name; ?><br />
<a href="<?php echo wp_logout_url( $rpx_http_vars['redirect_to'] ); ?>" title="Logout">Log out</a></p></div>
<?php
  }else{
    ?>
<div id="rpxcomment"><?php echo rpx_small_buttons(RPX_COMMENT_PROMPT); ?></div><br />
<?php
  }
}


?>
