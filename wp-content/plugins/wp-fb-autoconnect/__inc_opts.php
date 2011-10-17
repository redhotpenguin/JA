<?php

//General Info
global $jfb_name, $jfb_version, $jfb_homepage;
$jfb_name       = "WP-FB AutoConnect";
$jfb_version    = "1.6.9";
$jfb_homepage   = "http://www.justin-klein.com/projects/wp-fb-autoconnect";


//Database options
//Note: Premium options are included by Premium.php, if present.
global $opt_jfb_app_id, $opt_jfb_api_key, $opt_jfb_api_sec, $opt_jfb_email_to, $opt_jfb_delay_redir, $opt_jfb_ask_perms, $opt_jfb_ask_stream, $opt_jfb_stream_content;
global $opt_jfb_req_perms, $opt_jfb_hide_button, $opt_jfb_mod_done, $opt_jfb_valid;
global $opt_jfb_bp_avatars, $opt_jfb_wp_avatars, $opt_jfb_fulllogerr, $opt_jfb_disablenonce, $opt_jfb_show_credit;
global $opt_jfb_username_style;
$opt_jfb_app_id     = "jfb_app_id";
$opt_jfb_api_key    = "jfb_api_key";
$opt_jfb_api_sec    = "jfb_api_sec";
$opt_jfb_email_to   = "jfb_email_to";
$opt_jfb_delay_redir= "jfb_delay_redirect";
$opt_jfb_ask_perms  = "jfb_ask_permissions";
$opt_jfb_req_perms  = "jfb_req_permissions";
$opt_jfb_ask_stream = "jfb_ask_stream";
$opt_jfb_stream_content = "jfb_stream_content";
$opt_jfb_hide_button= "jfb_hide_button";
$opt_jfb_mod_done   = "jfb_modrewrite_done";
$opt_jfb_valid      = "jfb_session_valid";
$opt_jfb_fulllogerr = "jfb_full_log_on_error";
$opt_jfb_disablenonce="jfb_disablenonce";
$opt_jfb_bp_avatars = "jfb_bp_avatars";
$opt_jfb_wp_avatars = "jfb_wp_avatars";
$opt_jfb_show_credit= "jfb_credit";
$opt_jfb_username_style = "jfb_username_style";

//Shouldn't ever need to change these
global $jfb_nonce_name, $jfb_uid_meta_name, $jfb_js_callbackfunc, $jfb_default_email;
$jfb_nonce_name     = "ahe4t50q4efy0";
$jfb_uid_meta_name  = "facebook_uid";
$jfb_js_callbackfunc= "jfb_js_login_callback";
$jfb_default_email  = '@unknown.com';


//Error reporting function
function j_die($msg)
{
    j_mail("Facebook Login Error", $msg);
    global $jfb_log, $opt_jfb_fulllogerr;
    if( isset($jfb_log) && get_option($opt_jfb_fulllogerr) )
        $msg .= "<pre>---LOG:---\n" . $jfb_log . "</pre>";
    die($msg);
}

//Log reporting function
function j_mail($subj, $msg='')
{
    global $opt_jfb_email_to, $jfb_log;
    $email_to = get_option($opt_jfb_email_to);
    if( isset($email_to) && $email_to )
    {
        if( $msg )            $msg .= "\n\n";
        if( isset($jfb_log) ) $msg .= "---LOG:---\n" . $jfb_log;
        $msg .= "\n---REQUEST:---\n" . print_r($_REQUEST, true);
        mail($email_to, $subj, $msg);
    }
}


/**
 * Test if this has the "Premium" features
 */
function jfb_premium()
{
    return defined('JFB_PREMIUM');
}


//This is TEMPORARY DEBUG code. In order to try and figure out that strange "nonce check failed" bug,
//I'll log the components used to generate the nonce (see wp_create_nonce()).  Then if the check fails,
//I can compare what's changed from when the form was submitted until _process_login.php started.
//Hopefully this'll reveal the cause of the problem...
global $opt_jfb_generated_nonce;
$opt_jfb_generated_nonce = "jfb_nonce_debugging";
function debug_nonce_components()
{
    global $opt_jfb_generated_nonce;
    $user = wp_get_current_user();
	$uid = (int) $user->id;
	
	$nonce_life = apply_filters('nonce_life', 86400);
	$time = time();
	$nonce_tick = ceil(time() / ( $nonce_life / 2 ));
	$tick_verify = wp_nonce_tick();
	
	$hash = wp_hash($i . $action . $uid, 'nonce');
    $nonce = substr($hash, -12, 10);
    return "NONCE: $nonce, uid: $uid, life: $nonce_life, time: $time, tick: $nonce_tick, verify: $tick_verify, hash: $hash";
}
?>