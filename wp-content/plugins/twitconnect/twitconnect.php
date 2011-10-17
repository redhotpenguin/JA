<?php
/*
Plugin Name: Twit Connect
Author:  Shannon Whitley 
Author URI: http://voiceoftech.com/swhitley/
Plugin URI: http://www.voiceoftech.com/swhitley/?page_id=706
Description: Integrate Twitter and Wordpress.  Provides single-signon using oAuth and displays Twitter avatars.  Includes @anywhere and Tweet Quotes.
Acknowledgments:  
  Adam Hupp  (email : adam at hupp.org / ahupp at facebook.com) - Facebook Plugin  
  Brooks Bennett (http://www.brooksskybennett.com/) - oAuth Popup
  Peter Denton (http://twibs.com/oAuthButtons.php) - 'Signin with Twitter' button
  Abraham Williams (http://github.com/abraham/twitteroauth/) - TwitterOAuth
  Alexander Morris (http://www.vlogolution.com) - Unique account fix
Version: 2.57
************************************************************************************
M O D I F I C A T I O N S
1. 03/23/2009 Shannon Whitley - Initial Release
... Version 2.0 ...
2. 10/23/2009 Shannon Whitley   Significant code restructuring and cleanup.
                                oAuth Lib Change.
                                New Feature - Post comments to Twitter.
                                Changed tab-index of the button on the login page.
                                Position the button using javascript.
                                External stylesheet.
                                Comment saved before page refresh.
3. 11/07/2009 Shannon Whitley   Twitter Self-Hosted still not supported on PHP4.
4. 02/06/2010 Shannon Whitley   Akismet fails if nomail@nomail.com is used as
                                default e-mail address.  Modify for valid domain.
5. 03/05/2010 Shannon Whitley   Enable image cache options.
6. 04/01/2010 Shannon Whitley   Removed SPIURL default and converted to option
                                with donation link.
7. 04/02/2010 Shannon Whitley   Added support for BuddyPress login and avatars.
                                New option to show or hide button on the comment page.
8. 04/15/2010 Shannon Whitley   Added @anywhere javascript library.
                                Hovercards, Follow Button, Tweetbox
9. 05/05/2010 Shannon Whitley   Tweet Quotes
10. 08/18/2010 Shannon Whitley  Tweet Button, removed Snoopy, jQuery UI tabs, author
                                link fix, author image directly from Twitter,
                                override author image if selected.
11. 09/09/2010 Shannon Whitley  Updated the oAuth library and method calls.
12. 10/06/2010 Shannon Whitley  Allow any button image.  Prevent empty account creation.
                                Fix for https and trailing slashes.
                                Login redirect to profile option.
                                Multiple buttons on a single page.
13. 01/20/2011 Shannon Whitley  Use oAuth callback.  No longer need wp_redirect.
************************************************************************************
************************************************************************************
I N S T R U C T I O N S

There are two ways to display the button:

1) Add the following code to your template page where you want the button to appear:

    <!-- Begin Twit Connect -->
    <?php if(function_exists('twit_connect')){twit_connect();} ?>
    <!-- End Twit Connect -->

2) Or, do nothing and the button will show below the comment form where 
   the following action usually exists (in comments.php).
 
    <?php do_action('comment_form', $post->ID); ?>

************************************************************************************
*/

if(!version_compare(PHP_VERSION, '5.0.0', '<'))
{
  if(!class_exists('TwitterOAuth')) { 
    include dirname(__FILE__).'/twitterOAuth.php';
  }
}

$twc_btn_images = array();

$twc_user_login_suffix = get_option("twc_user_login_suffix");
$twc_email_default = get_option("twc_email_default");

//************************************************************************************
//* Actions and Filters
//************************************************************************************
add_action('init', 'twc_init');
add_filter("get_avatar", "twc_get_avatar",10,4);
add_filter("bp_core_fetch_avatar","twc_bp_get_avatar",10,4);
add_action("admin_menu", "twc_config_page");
add_action("wp_head", "twc_wp_head");
add_action('wp_print_styles', 'twc_stylesheet_add');
add_action('wp_admin_css','twc_stylesheet_add');
add_filter('the_content', 'twc_the_content');

//Login Form
add_action('login_form', 'twc_login_form');
add_action('bp_after_sidebar_login_form', 'twc_login_form');    

if (session_id() == "") {
    session_start();
}

$twc_add_to_comment_page = get_option('twc_add_to_comment_page');
if(empty($twc_add_to_comment_page))
{
    update_option('twc_add_to_comment_page', 'Y');
}

if($twc_add_to_comment_page == 'Y')
{
    add_action('comment_form', 'twc_comment_form');
}

$twc_tweet_this = get_option('twc_tweet_this');
if($twc_tweet_this == 'Y')
{
    add_action('comment_post', 'twc_comment_post');
}

$twc_local = get_option("twc_local");
if($twc_local == 'Y')
{
    $twc_url = rtrim(get_option('siteurl'),"/");
    $twc_page = 'index.php?twc_oauth_start=true';
    $twc_a = '';
}
else
{
    $twc_url = 'http://mytweeple.com';
    $twc_page = 'twc.aspx?f=1';
    $twc_a = 'location.href+"#twcbutton"';
}

$twc_profile_images = '';
$twc_loaded = false;
$twc_tweet_button = get_option('twc_tweet_button');  


//************************************************************************************
//* twc_init
//************************************************************************************
function twc_init()
{
    global $twc_tweet_button, $user_email;
   
    if(!is_user_logged_in())
    {
        if(isset($_GET['oauth_token']))
        {
            twc_oAuth_Confirm();
        }
        else
        {
            if(isset($_GET['twc_oauth_start']))
            {
                twc_oAuth_Start();
            }
        }
        if(isset($_GET['twc_req_key']))
        {
            twc_TwitterInfoGet($_GET['twc_req_key']);
        }
    }
    else
    {
	    if ( isset($_GET['page']) ) {
  	        $plugin_page = stripslashes($_GET['page']);
      	    $plugin_page = plugin_basename($plugin_page);
	        if($plugin_page == 'twitconnect/twitconnect.php')
	        {
 		        wp_enqueue_script('jquery');
    	        wp_enqueue_script('jquery-ui-core');
    	        wp_enqueue_script('jquery-ui-tabs');
		        wp_enqueue_style('jquery-ui','http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/base/jquery-ui.css');
	        }
    	}
    }
    $twc_at_anywhere = get_option('twc_at_anywhere');
    if($twc_at_anywhere == 'Y')
    {
        $twc_consumer_key = get_option('twc_consumer_key');
        wp_enqueue_script( 'at_anywhere', 'http://platform.twitter.com/anywhere.js?id='.$twc_consumer_key.'&v=1');
    }
    if($twc_tweet_button == 'Y')
    {
        wp_enqueue_script( 'twitter_widgets', 'http://platform.twitter.com/widgets.js');
    }
}

//************************************************************************************
//* twit_connect
//************************************************************************************
function twit_connect()
{
    global $twc_loaded;
    twc_show_twit_connect_button();
    $twc_loaded = true;
}

//************************************************************************************
//* twc_login_form
//************************************************************************************
function twc_login_form()
{
    if(get_option('twc_add_to_login_page') == 'Y')
    {
        echo '<link rel="stylesheet" href="'.WP_PLUGIN_URL . '/twitconnect/style.css" type="text/css" media="all" />';    
        twc_show_twit_connect_button(0,'login');
    }
}

function twc_comment_form()
{
    global $post_ID;
    twc_show_twit_connect_button();
    
    $twc_at_anywhere_tweetbox = get_option('twc_at_anywhere_tweetbox');
    if($twc_at_anywhere_tweetbox == 'Y')
    {
        $permalink = get_permalink($post_ID);
		$post_title = strip_tags(get_the_title( $post_ID ));
		$blog_title = get_bloginfo('name');
        echo '<div><span id="twc_at_anywhere_tweetbox"></span></div>
            <script type="text/javascript">
                twttr.anywhere("1", function (twitter) {
                    twitter("#twc_at_anywhere_tweetbox").tweetBox({
                        height: 100,
                        width: 400,
                        label: "Tweet about this post:",
                        defaultContent: "\"'.$post_title.'\" ('.$blog_title.') '.$permalink.'"';
        echo '           });
                });
            </script>';
    }

}

//************************************************************************************
//* twc_wp_head
//************************************************************************************
function twc_wp_head()
{
    global $user_email;
    
    if(is_user_logged_in())
    {
        $url = '';
        
  	   // if(get_option('twc_comment_redirect') == 'Y')
	    //{
            if(strpos($user_email, 'changeme') !== false)
	        {
			
		    $user = wp_get_current_user();
		    $url = '/members/' . $user->display_name . '/settings';
	        }
	   // }
	    
        echo '<script type="text/javascript">'."\n";
        echo '<!--'."\n";
        echo 'if(window.opener){if(window.opener.document.getElementById("twc_connect") || window.opener.getElementsByClass("twc_connect")){window.opener.twc_bookmark("'.$url.'");window.close();}}'."\n";
        echo '//-->'."\n";
        echo '</script>';
    }
    $twc_at_anywhere = get_option('twc_at_anywhere');
    if($twc_at_anywhere == 'Y')
    {
        echo '<script type="text/javascript">
			twttr.anywhere(onAnywhereLoad);
            function onAnywhereLoad(twitter) {';
        $twc_at_anywhere_hovercards = get_option('twc_at_anywhere_hovercards');            
        if($twc_at_anywhere_hovercards == 'Y')
        {
                echo 'twitter.hovercards();';
        }
        $twc_at_anywhere_followbutton = get_option('twc_at_anywhere_followbutton');
        if(!empty($twc_at_anywhere_followbutton))
        {
            echo 'twitter("#follow-on-twitter-'.$twc_at_anywhere_followbutton.'").followButton("'.$twc_at_anywhere_followbutton.'");';
        }
        echo '   };
            </script>';    
    }
}

//************************************************************************************
//* twc_show_twit_connect_button
//************************************************************************************
function twc_show_twit_connect_button($id='0',$type='comment')
{
    global $user_ID, $user_email, $twc_tweet_this, $twc_loaded, $twc_url, $twc_page, $twc_a, $twc_local;
   
    if(is_user_logged_in())
    {
        if($type == 'login')
        {
            echo '<script type="text/javascript">'."\r\n";
            echo '<!--'."\r\n";
            echo 'if(window.opener){if(window.opener.document.getElementById("#twc_connect") || window.opener.getElementsByClass("twc_connect")){window.opener.twc_bookmark("");window.close();}}'."\r\n";
            echo '//-->'."\r\n";
            echo '</script>';
        }
        else
        {
            if($twc_tweet_this == 'Y' && get_usermeta($user_ID, 'twcid'))
            {
                echo '<p class="twc-tweet-this"><input type="checkbox" id="twc_tweet_this" name="twc_tweet_this" style="width:auto" /> Tweet This Comment [<a href="javascript:none" title="Post this comment to Twitter">?</a>]</p>';
            }
            echo '<p>Update your e-mail address: <a href="./wp-admin/profile.php" name="twcbutton">'.$user_email.'</a></p>';
            echo '<script type="text/javascript">'."\r\n";
            echo '<!--'."\r\n";
            echo 'window.onload=function(){if(!window.opener && document.getElementById("comment")){'."\r\n";
            echo '    if(document.getElementById("comment").value.length == 0)'."\r\n";
            echo '    {'."\r\n";
            echo '        twc_updateComment(twc_readCookie("twc_comment"));'."\r\n";
            echo '    }'."\r\n";
            echo '}};'."\r\n";
            echo '//-->'."\r\n";
            echo '</script>'."\r\n";
            
        }
    }
    
     echo '<script type="text/javascript">
    <!--
    //No jQuery
    if(typeof jQuery == "undefined")
    {
        window.onload = function(){
            if(document.getElementById("twc_connect"))
            {
                if(!document.getElementById("twc_connect").getAttribute("loaded"))
                {
                    twc_createButton(document.getElementById("twc_connect"));
                }
            }
            var elems = getElementsByClass("twc_connect");
            for(var ndx=0;ndx<elems.length;ndx++)
            {
                if(!elems[ndx].getAttribute("loaded"))
                {
                    twc_createButton(elems[ndx]);
                }
            }
        };
    }
    else
    {
        jQuery(document).ready(function(){
            jQuery("#twc_connect").each(function(i){
                if(!jQuery(this).attr("loaded"))
                {
                    twc_createButton(jQuery(this));
                }
            });
            jQuery(".twc_connect").each(function(i){
                if(!jQuery(this).attr("loaded"))
                {
                    twc_createButton(jQuery(this));
                }
            });
        });
    }
    //-->
    </script>';
    
    if(!$twc_loaded)
    {
        //************************************************************************************
        //* Cookie Javascript
        //************************************************************************************
        echo '<script type="text/javascript">
        <!--
            function twc_createCookie(name,value,days) {
	            if (days) {
		            var date = new Date();
		            date.setTime(date.getTime()+(days*24*60*60*1000));
		            var expires = "; expires="+date.toGMTString();
	            }
	            else var expires = "";
	            document.cookie = name+"="+value+expires+"; path=/";
            }
            function twc_readCookie(name) {
	            var nameEQ = name + "=";
	            var ca = document.cookie.split(\';\');
	            for(var i=0;i < ca.length;i++) {
		            var c = ca[i];
		            while (c.charAt(0)==\' \') c = c.substring(1,c.length);
		            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	            }
	            return null;
            }
            function twc_eraseCookie(name) {
	            twc_createCookie(name,"",-1);
            }
            function twc_updateComment(comment) { 
                if(comment){
                    document.getElementById("comment").value = comment.replace(/<br\/>/g,"\n");
                    twc_eraseCookie("twc_comment");
                    
                }
            }
            function getElementsByClass( searchClass, domNode, tagName) { 
	            if (domNode == null) domNode = document;
	            if (tagName == null) tagName = "*";
	            var el = new Array();
	            var tags = domNode.getElementsByTagName(tagName);
	            var tcl = " "+searchClass+" ";
	            for(i=0,j=0; i<tags.length; i++) { 
		            var test = " " + tags[i].className + " ";
		            if (test.indexOf(tcl) != -1) 
			            el[j++] = tags[i];
	            } 
	            return el;
            }         
            //-->
            </script>';               
        //************************************************************************************
        //* End Cookie Javascript
        //************************************************************************************
    }

    if(is_user_logged_in())
    {
        return;
    }
    
    $twc_before = '';
    if($type == 'login')
    {
        $twc_login_text = get_option("twc_login_text");
        echo '<br/>'.$twc_login_text;
        $twc_before = get_option('twc_before_login');
    }
    else
    {
    	$twc_template = get_option('twc_template');
        echo $twc_template;
        $twc_before = get_option('twc_before_comment');
    }
    
    $twc_redirect = get_option('twc_redirect');  
    $twc_btn_image = get_option('twc_btn_choice');
    
    if(strpos($twc_btn_image, 'http') === false )
    {
       $twc_btn_images = twc_get_buttons(); 
       update_option('twc_btn_choice', end($twc_btn_images));
       $twc_btn_image = end($twc_btn_images);
    }
    
    if($twc_loaded)
    {
        return;
    }
   
    //************************************************************************************
    //* Button Javascript
    //************************************************************************************
    echo '<script type="text/javascript">
    <!--
    function twc_bookmark(url){
       var reload = false;
       if(url == location.href)       
       {
            reload = true;
       }
       if(url.length == 0)
       {
            url=location.href;
            reload = true;
       }
       if(url.indexOf("wp-login.php") > 0)
       {
           url = "'.$twc_redirect.'";
           location.href = url;
       }
       else
       {
           var temp = url.split("#");
           url = temp[0];
           url += "#twcbutton";
           location.href = url;
           if(reload)
           {
              location.reload();
           }
       }
    }
    
    function twc_createButton(obj)
    {
        var url = location.href;
        var button = document.createElement("button");
        button.setAttribute("class","btn");
        button.setAttribute("type","button");
        button.setAttribute("tabindex","999");
        button.onclick = function(){
            if(document.getElementById("comment"))
            {
                if(document.getElementById("comment").value.length > 0)
                {
                    var comment = document.getElementById("comment").value;
                    comment = comment.replace(/\r\n/g,"\n").replace(/\n/g,"<br/>");
                    twc_createCookie("twc_comment",comment,1);
                    var cookie = twc_readCookie("twc_comment");
                    if(cookie != comment)
                    {
                        twc_eraseCookie("twc_comment");
                        alert("The comment field must be blank before you Sign in with Twitter.\r\nPlease make a copy of your comment and clear the comment field.");
                        return false;
                    }
                }
            }
            window.open("'.$twc_url.'/'.$twc_page.'&a="+escape('.$twc_a.')+"&twcver=2&loc="+escape(url), "twcWindow","width=800,height=400,left=150,top=100,scrollbar=no,resize=no");
            return false;
        };
        button.innerHTML = "<img src=\''.$twc_btn_image.'\' alt=\'Signin with Twitter\' style=\'margin:0;\' />";
        if(typeof jQuery == "undefined")
        {
            obj.appendChild(button);
            obj.setAttribute("loaded","true");
        }
        else
        {
	        obj.append(button);
	        obj.attr("loaded","true");
	    }
	    ';
        /* PHP */
        if(strlen($twc_before) > 0)
        {
            echo 'if(document.getElementById("'.$twc_before.'"))
                {
                    var twc_before = document.getElementById("'.$twc_before.'");
                    twc_before.parentNode.insertBefore(document.getElementById("twc_connect"),twc_before);
                }
                ';
        }
        /* END PHP */        
        echo '}
        //-->
        </script>';
    //************************************************************************************
    //* End - Button Javascript
    //************************************************************************************

    $twc_loaded = true;

}

//************************************************************************************
//* twc_stylesheet_add
//************************************************************************************
function twc_stylesheet_add() {
    $src = WP_PLUGIN_URL . '/twitconnect/style.css';
    wp_enqueue_style('twc_style', $src);
} 


//************************************************************************************
//* twc_bp_get_avatar
//************************************************************************************
function twc_bp_get_avatar($avatar, $params='')
{
   global $comment, $twc_user_login_suffix, $twc_profile_images;

   //If not default gravatar then assume using custom avatar.
  // if(empty($params) || strpos($avatar, 'bp-core/images') === FALSE) {
  //	return $avatar;
   //}

   if (get_usermeta($params['item_id'], 'twcid')) {
    $twc_profile_images = twc_profile_images_get($twc_profile_images);
    $user_info = get_userdata($params['item_id']);
    $username = str_replace($twc_user_login_suffix,"",$user_info->user_login);
    
    $out = str_replace('%%username%%',urlencode($username),$twc_profile_images);
    
    return str_replace(preg_replace('/.*src=["|\'](.*?)["|\'].*/i', "$1", $avatar),$out,$avatar);
  } else {
    return $avatar;
  }

}

//************************************************************************************
//* twc_get_avatar
//************************************************************************************
function twc_get_avatar($avatar, $id_or_email='',$size='32') {
  global $comment, $twc_user_login_suffix, $twc_profile_images;

  if(is_object($comment))
  {
      $id_or_email = $comment->user_id;
  }

  if (is_object($id_or_email)) {
     $id_or_email = $id_or_email->user_id;
  }

  if (get_usermeta($id_or_email, 'twcid')) {
    $twc_profile_images = twc_profile_images_get($twc_profile_images);
    $user_info = get_userdata($id_or_email);
    $username = str_replace($twc_user_login_suffix,"",$user_info->user_login);
    
    $out = str_replace('%%username%%',urlencode($username),$twc_profile_images);
    
    $avatar = "<img alt='' src='{$out}' class='avatar avatar-{$size}' height='{$size}' width='{$size}' />";
    return $avatar;
  } else {
    return $avatar;
  }
}

//************************************************************************************
//* twc_profile_images_get
//************************************************************************************
function twc_profile_images_get($twc_profile_images)
{
    if(empty($twc_profile_images))
    {
        $twc_profile_images = get_option('twc_profile_images');
    }
    //Default image service
    if(empty($twc_profile_images))
    {
        if(function_exists('spi_profile_image_get'))
        {
	        $twc_profile_images =  rtrim(get_option("home"), "/").'/?spiurl_user=%%username%%';
        }
        else
        {
            $twc_profile_images = 'http://api.twitter.com/1/users/profile_image/%%username%%'; 
        }
        update_option('twc_profile_images', $twc_profile_images);
    }
    
    return $twc_profile_images;
}

//************************************************************************************
//* twc_TwitterInfoGet
//************************************************************************************
function twc_TwitterInfoGet($req_key)
{
    global $twc_url, $twc_page;
    
    $_SESSION['twc_req_key'] = $req_key;

    $url = $twc_url.'/'.$twc_page.'&twc_req_key='.urlencode($req_key);
	
    $result = wp_remote_get($url); 

    if (is_wp_error( $result ) ) {
        $results = "Error contacting Twit Connect: ".$result->get_error_message()."\n";
        die($results);
    } else {
	    $results = $result['body'];
	    twc_Login($results);
   }    
}

//************************************************************************************
//* twc_TwitterInfoPost
//************************************************************************************
function twc_TwitterInfoPost($req_key, $tweet)
{
    global $twc_url, $twc_page;

    if( !class_exists( 'WP_Http' ) ) {
        include_once( ABSPATH . WPINC. '/class-http.php' );
    }
    $request = new WP_Http;
    $url = $twc_url.'/'.$twc_page.'&twc_req_key='.urlencode($req_key).'&tweet='.urlencode($tweet);
    $result = $request->request( $url );
    
    if (isset($result->errors)) {
        $results = "Error contacting Twit Connect: ".$result->errors."\n";
        wp_die($results);
    } else {
	    $results = $result['body'];
   }    
   
    return $results;
}


//************************************************************************************
//* twc_oAuth_Start
//************************************************************************************
function twc_oAuth_Start()
{
    
    $loc = $_GET['loc'];

    $uri = explode('#',$loc);
    $url = $uri[0];


    $twc_consumer_key = get_option('twc_consumer_key');
    $twc_consumer_secret = get_option('twc_consumer_secret');
    
    /* Create TwitterOAuth object with app key/secret */
    $to = new TwitterOAuth($twc_consumer_key, $twc_consumer_secret);
    $to->format = 'xml';
    
    
    /* Request tokens from twitter */
    $tok = $to->getRequestToken($url);

    /* Save tokens for later */
    unset($_SESSION['oauth_request_token']);
    unset($_SESSION['oauth_request_token_secret']);
    $_SESSION['oauth_request_token'] = $token = $tok['oauth_token'];
    $_SESSION['oauth_request_token_secret'] = $tok['oauth_token_secret'];

    echo '<script type="text/javascript">location.href = "'.$to->getAuthorizeURL($token).'";</script>';
}

//************************************************************************************
//* twc_oAuth_Confirm
//************************************************************************************
function twc_oAuth_Confirm()
{
    $twc_consumer_key = get_option('twc_consumer_key');
    $twc_consumer_secret = get_option('twc_consumer_secret');
    
    /* Create TwitterOAuth object with app key/secret and token key/secret from default phase */
    $to = new TwitterOAuth($twc_consumer_key, $twc_consumer_secret, $_SESSION['oauth_request_token'], $_SESSION['oauth_request_token_secret']);
    $to->format = 'xml';
    /* Request access tokens from twitter */
    $tok = $to->getAccessToken();

    /* Save the access tokens. Normally these would be saved in a database for future use. */
    $_SESSION['oauth_access_token'] = $tok['oauth_token'];
    $_SESSION['oauth_access_token_secret'] = $tok['oauth_token_secret'];

    $to = new TwitterOAuth($twc_consumer_key, $twc_consumer_secret, $_SESSION['oauth_access_token'], $_SESSION['oauth_access_token_secret']);
    $to->format = 'xml';
    /* Run request on twitter API as user. */
    $xml = $to->get('account/verify_credentials');
    
    $twitterInfo = new SimpleXMLElement($xml);
 
    $id = $twitterInfo->id;
    $screen_name = $twitterInfo->screen_name;
    $name = $twitterInfo->name;
    $url = $twitterInfo->url;
    twc_Login($id.'|'.$screen_name.'|'.$name.'|'.$url);
}

//************************************************************************************
//* twc_comment_post
//************************************************************************************
function twc_comment_post($comment_ID)
{
    global $twc_local;
    
    if(!isset($_REQUEST["twc_tweet_this"]))
    {
        return;
    }

    $twc_consumer_key = get_option('twc_consumer_key');
    $twc_consumer_secret = get_option('twc_consumer_secret');
    
    $comment = get_comment($comment_ID); 
    $post_title = strip_tags(get_the_title( $comment->comment_post_ID ));
    $blog_title = get_bloginfo('name');
    
    $permalink = '';
    //Use the comment link if it is approved, otherwise use the post link.
    if($comment->comment_approved == 1)
    {
        $permalink = get_comment_link($comment);
    }
    else
    {
        $permalink = get_permalink($comment->comment_post_ID);
    }   

    $shortlink = '';

    if(!empty($permalink))
    {    
        //Shorten the link.
        if( !class_exists( 'WP_Http' ) ) {
            include_once( ABSPATH . WPINC. '/class-http.php' );
        }
        $request = new WP_Http;
        $url = 'http://is.gd/api.php?longurl='.urlencode($permalink);
        $result = $request->request( $url );
        
        if (isset($result->errors)) {
            $results = "Your comment was submitted, but it could not be sent to Twitter.  There was an error shortening the url: ".$result->errors."\n";
            wp_die($results);
        } else {
	        $shortlink = $result['body'];
       }    
    }
    
    if(!empty($shortlink))
    {
        //Get the template for the tweet.
        $tweet = get_option("twc_tweet_this_text");
        
        //Determine characters available for post and blog title.
        $temp_tweet = $tweet;
        $temp_tweet = str_replace('%%post_title%%', '', $temp_tweet);
        $temp_tweet = str_replace('%%blog_title%%', '', $temp_tweet);
        $temp_tweet = str_replace('%%shortlink%%', '', $temp_tweet);

        $tweet_len = strlen($temp_tweet);
        if(strlen($post_title) + strlen($blog_title) + strlen($shortlink) + $tweet_len > 140)
        {
            //Shorten the blog title.
            $ctr = strlen($blog_title) - 1;
            $shorter = false;
            while(strlen($blog_title) > 10 && 140 < strlen($post_title) + strlen($blog_title) + 3 + strlen($shortlink) + $tweet_len)
            {
                $blog_title = substr($blog_title,0,$ctr--);  
                $shorter = true;
            }
            if($shorter)
            {
                $blog_title.='...';
            }
            $ctr = strlen($post_title) - 1;
            $shorter = false;
            while(strlen($post_title) > 10 && 140 < strlen($post_title) + 3 + strlen($blog_title) + strlen($shortlink) + $tweet_len)
            {
                $post_title = substr($post_title,0,$ctr--);  
                $shorter = true;
            }
            if($shorter)
            {
                $post_title.='...';
            }
        } 
        $temp_tweet = $tweet;
        $temp_tweet = str_replace('%%post_title%%',$post_title, $temp_tweet);
        $temp_tweet = str_replace('%%blog_title%%',$blog_title, $temp_tweet);
        $temp_tweet = str_replace('%%shortlink%%',$shortlink, $temp_tweet);
        
        $tweet = $temp_tweet;
        if(strlen($tweet) <= 140)
        {
            if($twc_local == 'Y')
            {
                /* Create TwitterOAuth with app key/secret and user access key/secret */
                $to = new TwitterOAuth($twc_consumer_key, $twc_consumer_secret, $_SESSION['oauth_access_token'], $_SESSION['oauth_access_token_secret']);
                /* Run request on twitter API as user. */
                $to->format = 'xml';
                $content = $to->post('statuses/update', array('status' => $tweet));
            }
            else
            {
                $content = twc_TwitterInfoPost($_SESSION['twc_req_key'], $tweet);
            }
            if(strpos($content, 'status') === false && strpos($content, $tweet) === false)
            {
                wp_die('Your comment was submitted, but it could not be posted to Twitter.  '.$content);
            }
        }
    }
}

//************************************************************************************
//* twc_Login
//************************************************************************************
function twc_Login($pdvUserinfo) {
  global $wpdb, $twc_use_twitter_profile, $twc_user_login_suffix, $twc_email_default;

  $userinfo = explode('|',$pdvUserinfo);
  if(count($userinfo) < 4)
  {
      wp_die("An error occurred while trying to contact Twit Connect.");
  }
  
  //User login
  $user_login_n_suffix = $userinfo[1].$twc_user_login_suffix;
  
  if($user_login_n_suffix == $twc_user_login_suffix)
  {
      wp_die("An error occurred while trying to retrieve your Twitter username.  Please try again.");  
  }

  //Use the url from the Twitter profile.
  $user_url = $userinfo[3];
  
  $twc_use_twitter_profile = get_option('twc_use_twitter_profile');

  if($twc_use_twitter_profile == 'Y')
  {
      //Use the Twitter profile.
      $user_url = 'http://twitter.com/'.$userinfo[1];
  }
  
  $twc_email_default = str_replace('%%username%%', $userinfo[1], $twc_email_default);

  $userdata = array(
    'user_pass' => wp_generate_password(),
    'user_login' => $user_login_n_suffix,
    'display_name' => $userinfo[2],
    'user_url' => $user_url,
    'user_email' => $twc_email_default
  );

  if(!function_exists('wp_insert_user'))
  {
      include_once( ABSPATH . WPINC . '/registration.php' );
  } 
  
  $wpuid = twc_twitteruser_to_wpuser($userinfo[0]);
  
  if(!$wpuid)
  {
      if (!username_exists($user_login_n_suffix))
      {
        $wpuid = wp_insert_user($userdata);
        if($wpuid)
        {
            update_usermeta($wpuid, 'twcid', "$userinfo[0]");
        }
      }
      else
      {
        wp_die('User name '.$user_login_n_suffix.' cannot be added.  It already exists.');
      }
  }
  else
  {
    $user_obj = get_userdata($wpuid);
    
    if($user_obj->display_name != $userinfo[2] || $user_obj->user_url != $user_url)
    {
        $userdata = array(
        'ID' => $wpuid,
        'display_name' => $userinfo[2],
        'user_url' => $user_url,
        );
        wp_update_user( $userdata );
    }
    if($user_obj->user_login != $user_login_n_suffix)
    {
        if (!username_exists($user_login_n_suffix))
        {
            $q = sprintf( "UPDATE %s SET user_login='%s' WHERE ID=%d", 
                $wpdb->users, $user_login_n_suffix, (int) $wpuid );
		    if (false !== $wpdb->query($q)){
		        update_usermeta( $wpuid, 'nickname', $user_login_n_suffix );
		    }
		}
        else
        {
          wp_die('User name '.$user_login_n_suffix.' cannot be added.  It already exists.');
        }
    }
  }
  
  if($wpuid) {
        wp_set_auth_cookie($wpuid, true, false);
        wp_set_current_user($wpuid);
  }
}

//************************************************************************************
//* twc_get_user_by_meta
//************************************************************************************
function twc_get_user_by_meta($meta_key, $meta_value) {
  global $wpdb;
  $sql = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '%s' AND meta_value = '%s'";
  return $wpdb->get_var($wpdb->prepare($sql, $meta_key, $meta_value));
}

//************************************************************************************
//* twc_twitteruser_to_wpuser
//************************************************************************************
function twc_twitteruser_to_wpuser($twcid) {
  return twc_get_user_by_meta('twcid', $twcid);
}

//*****************************************************************************
//* twc_config_page - WordPress admin page
//*****************************************************************************
function twc_config_page()
{
	add_submenu_page("options-general.php", "Twit Connect",
		"Twit Connect", 10, __FILE__, "twitconnect_configuration");
}

//*****************************************************************************
//* twc_get_domain - WordPress site domain
//*****************************************************************************
function twc_get_domain()
{
    // get host name from URL
    $siteurl = str_replace('https://','http://',get_option("home"));
    preg_match('@^(?:http://)?([^/]+)@i', $siteurl, $matches);
    $host = $matches[1];

    // get last two segments of host name
    preg_match('/[^.]+\.[^.]+$/', $host, $matches);
    return $matches[0];
}

//*****************************************************************************
//* twc_get_buttons - Retrieve buttons from the image directory
//*****************************************************************************
function twc_get_buttons()
{
    global $twc_btn_images;
    
    $path = WP_PLUGIN_DIR.'/twitconnect/images/';
    $uri = WP_PLUGIN_URL.'/twitconnect/images/';
    $handle=opendir($path);

    while (($file = readdir($handle))!==false) {
        $ext = end(explode(".", $file));
        if($ext == 'png' || $ext == 'gif' || $ext == 'jpg'){
            array_push($twc_btn_images, $uri.$file);
        }
    }
    closedir($handle);
    
    return $twc_btn_images;
}



//*****************************************************************************
//* twc_the_content
//*****************************************************************************
function twc_the_content($text)
{
   global $twc_tweet_button, $post_ID;
    
   $tweet_pattern = '/(\[tweet\](.*?)\[\/tweet\])/is';
   
    # Check for in-post [tweet] [/tweet]
    if (preg_match_all ($tweet_pattern, $text, $matches)) {
        for ($m=0; $m<count($matches[0]); $m++) {
            $tweet = twc_TweetQuote($matches[2][$m]);
            $text = str_replace($matches[0][$m],$tweet,$text);
        } 
    }
    
    if($twc_tweet_button == 'Y')
    {
        $permalink = get_permalink($post_ID);
	    $post_title = strip_tags(get_the_title( $post_ID ));
	    $blog_title = get_bloginfo('name');

        $text .= '<div><a href="http://twitter.com/share" class="twitter-share-button"
                data-url="'.$permalink.'"
                data-text="'.$post_title.' ('.$blog_title.')"
                data-count="vertical">Tweet</a></div>';
	}

	return $text;

}

//*****************************************************************************
//* twc_TweetQuote - Format Tweet Text
//*****************************************************************************
function twc_TweetQuote($text)
{
	$twc_tweet_quote_template = get_option('twc_tweet_quote_template');	
    $start = 0;
    $end = 1;
    $screen_name = "";
    $tweet = "";
    $new_text = "";
    $tweets = explode('�', $text);
    foreach($tweets as $tweet)
    {
        $new_tweet = "";    
        $end = strpos($tweet, ':');
        $start = $end;
        while($start > 0 && ($end - $start) <= 16   )
        {
            $start--;
            if(substr($tweet, $start ,1) == ' ' || substr($tweet, $start ,1) == ']' || substr($tweet, $start ,1) == '>')
            {
                $start++;
                break;
            }
            
        }
        if($start >= 0 && ($end - $start) <= 16)
        {
            $tweet = strip_tags(substr($tweet, $start));
            $start = 0;
            $end = strpos($tweet, ":");
            $screen_name = substr($tweet, $start, $end - $start);
            $tweet = str_replace($screen_name.":","", $tweet);
            $tweet = str_replace('(expand)', '', $tweet);
            $tweet = twc_linkify_twitter_status($tweet);
            $tweet = str_replace("\n",'<span class="timestamp">', $tweet);
            $tweet = $tweet.'</span>';
            $new_tweet = $twc_tweet_quote_template;
            $new_tweet = str_replace('%%username%%', $screen_name, $new_tweet);
            $new_tweet = str_replace('%%tweet%%', $tweet, $new_tweet);
        }
        if(strlen($screen_name) > 0)
        {
            $new_text .= $new_tweet;
        }
    }
    return $new_text;
}

//*****************************************************************************
//* twc_linkify_twitter_status
//*****************************************************************************
function twc_linkify_twitter_status($status_text)
{
  // linkify URLs
  $status_text = preg_replace(
    '/(https?:\/\/\S+)/',
    '<a href="\1">\1</a>',
    $status_text
  );

  // linkify twitter users
  $status_text = preg_replace(
    '/(^|\s)@(\w+)/',
    '\1@<a href="http://twitter.com/\2">\2</a>',
    $status_text
  );

  // linkify tags
  $status_text = preg_replace(
    '/(^|\s)#(\w+)/',
    '\1#<a href="http://search.twitter.com/search?q=%23\2">\2</a>',
    $status_text
  );

  return $status_text;
}


//*****************************************************************************
//* twitconnect_configuration - WordPress admin page processing
//*****************************************************************************
function twitconnect_configuration()
{
        global $wpdb;
        
$twc_tweet_this_text_dflt = <<<KEEPME3
I just left a comment on %%post_title%% at %%blog_title%% - %%shortlink%%
KEEPME3;

//Acknowledgment - code from Twitter's Blackbird Pie.
$twc_tweet_quote_template_dflt = <<<KEEPME4
<!-- Begin Tweet --> 
<div class='bbpBox'>
<p class='bbpTweet'>%%tweet%%
<span class='metadata'>
<span class='author'><a href='http://twitter.com/%%username%%'><img src='http://api.twitter.com/1/users/profile_image/%%username%%' alt="%%username%%" /></a>
<strong><a href='http://twitter.com/%%username%%'>%%username%%</a></strong></span></span></p>
</div> 
<!-- End Tweet -->
KEEPME4;


		// Save Options
		if (isset($_POST["twc_save"])) {
			    // ...the options are updated.
			    update_option('twc_consumer_key', stripslashes($_POST["twc_consumer_key"]) );
			    update_option('twc_consumer_secret', stripslashes($_POST["twc_consumer_secret"]) );
	            update_option('twc_local', $_POST["twc_local"]);			    
	            update_option('twc_at_anywhere', $_POST["twc_at_anywhere"]);
	            update_option('twc_at_anywhere_hovercards', $_POST["twc_at_anywhere_hovercards"]);	            
	            update_option('twc_at_anywhere_followbutton', $_POST["twc_at_anywhere_followbutton"]);	            	            	            
	            update_option('twc_at_anywhere_tweetbox', $_POST["twc_at_anywhere_tweetbox"]);	 
	            update_option('twc_tweet_button', $_POST["twc_tweet_button"]);	           	            
        	    update_option('twc_btn_choice', $_POST["twc_btn_choice"]);
	            update_option('twc_template', stripslashes($_POST["twc_template"]));
				update_option('twc_tweet_quote_template', stripslashes($_POST["twc_tweet_quote_template"]));
	            update_option('twc_login_text', stripslashes($_POST["twc_login_text"]));
	            update_option('twc_use_twitter_profile', $_POST["twc_use_twitter_profile"]);
	            update_option('twc_add_to_login_page', $_POST["twc_add_to_login_page"]);  
	            if($_POST["twc_add_to_comment_page"] != 'Y')
	            {
	                $_POST["twc_add_to_comment_page"] = 'N';
	            }
	            update_option('twc_add_to_comment_page', $_POST["twc_add_to_comment_page"]);            	            
	            update_option('twc_user_login_suffix', $_POST["twc_user_login_suffix"]);
	            update_option('twc_email_default', $_POST["twc_email_default"]);            
	            update_option('twc_redirect', $_POST["twc_redirect"]);
	            update_option('twc_tweet_this', $_POST["twc_tweet_this"]);
	            update_option('twc_tweet_this_text', stripslashes($_POST["twc_tweet_this_text"]));
	            update_option('twc_before_comment', $_POST["twc_before_comment"]);
	            update_option('twc_before_login', $_POST["twc_before_login"]);
	            update_option('twc_profile_images', $_POST["twc_profile_images"]);
	            update_option('twc_comment_redirect', $_POST["twc_comment_redirect"]);
		}
		
		// Get the Data
		$twc_consumer_key = get_option('twc_consumer_key');
		$twc_consumer_secret = get_option('twc_consumer_secret');
		$twc_template = get_option('twc_template');
		if(empty($twc_template))
		{
		    $twc_template = $twc_template_dflt;
		}
		$twc_tweet_quote_template = get_option('twc_tweet_quote_template');		
		if(empty($twc_tweet_quote_template))
		{
		    $twc_tweet_quote_template = $twc_tweet_quote_template_dflt;
		}
		$twc_login_text = get_option('twc_login_text');
		if(empty($twc_login_text))
		{
		    //$twc_login_text = $twc_login_text_dflt;
		}
		$twc_tweet_this = get_option('twc_tweet_this');
		$twc_tweet_this_text = get_option('twc_tweet_this_text');
		if(empty($twc_tweet_this_text))
		{
		    $twc_tweet_this_text = $twc_tweet_this_text_dflt;
        }

		$twc_before_comment = get_option('twc_before_comment');
		$twc_before_login = get_option('twc_before_login');
       
        $twc_btn_choice = get_option('twc_btn_choice');
        $twc_local = get_option('twc_local');
        $twc_at_anywhere = get_option('twc_at_anywhere');
        $twc_at_anywhere_hovercards = get_option('twc_at_anywhere_hovercards');        
        $twc_at_anywhere_followbutton = get_option('twc_at_anywhere_followbutton');                
        $twc_at_anywhere_tweetbox = get_option('twc_at_anywhere_tweetbox');                        
        $twc_tweet_button = get_option('twc_tweet_button');                        
        
        $twc_user_login_suffix = get_option('twc_user_login_suffix');                                
        if(empty($twc_user_login_suffix))
        {
            $twc_user_login_suffix = '@twitter';
        }
        $twc_email_default = get_option('twc_email_default');                                
        if(empty($twc_email_default))
        {
            //Get Site Domain
            $sitedomain = twc_get_domain();
            $twc_email_default = 'changeme.%%username%%@'.$sitedomain;
        }
		//Nomail Update
		if (isset($_POST["twc_nomail_update"])) {
            $q = sprintf( "UPDATE %s SET user_email=replace('%s','username',replace(user_nicename,'twitter','')) WHERE user_email='nomail@nomail.com'", 
                $wpdb->users, str_replace('%%','',$twc_email_default) );
		    $wpdb->query($q);
		}

		$twc_profile_images = get_option('twc_profile_images');
  		//Default image service
		if(empty($twc_profile_images))
		    {
	        if(function_exists('spi_profile_image_get'))
	        {
		        $twc_profile_images =  rtrim(get_option("home"), "/").'/?spiurl_user=%%username%%';
	        }
	        else
	        {
	            $twc_profile_images = 'http://api.twitter.com/1/users/profile_image/%%username%%'; 
	        }
	        update_option('twc_profile_images', $twc_profile_images);

	    }


		if($_POST['twc_profile_images_chk'] == "self"  && ($twc_profile_images == 'http://purl.org/net/spiurl/%%username%%' || $twc_profile_images == 'http://api.twitter.com/1/users/profile_image/%%username%%'))
		{
		    $twc_profile_images =  rtrim(get_option("home"), "/").'/?spiurl_user=%%username%%';
		    update_option('twc_profile_images', $twc_profile_images );
		}
		if($_POST['twc_profile_images_chk'] == "spiurl" && $twc_profile_images != 'http://api.twitter.com/1/users/profile_image/%%username%%')
		{
		    $twc_profile_images = 'http://api.twitter.com/1/users/profile_image/%%username%%';
		    update_option('twc_profile_images',$twc_profile_images);
		}

        
        $twc_redirect = get_option('twc_redirect');                                        
        if(empty($twc_redirect))
        {
            $twc_redirect = 'wp-admin/index.php';
            update_option('twc_redirect',$twc_redirect);
        }
        $twc_use_twitter_profile = get_option('twc_use_twitter_profile');
        $twc_use_twitter_profile = $twc_use_twitter_profile == 'Y' ?
    	"checked='true'" : "";
    	
        $twc_add_to_login_page = get_option('twc_add_to_login_page');
        $twc_add_to_login_page = $twc_add_to_login_page == 'Y' ?
    	"checked='true'" : "";
    	
        $twc_add_to_comment_page = get_option('twc_add_to_comment_page');
        $twc_add_to_comment_page = $twc_add_to_comment_page == 'Y' ?
    	"checked='true'" : "";    	

        $twc_local = $twc_local == 'Y' ?
	    "checked='true'" : "";
		
        $twc_at_anywhere = $twc_at_anywhere == 'Y' ?
	    "checked='true'" : "";
		
        $twc_at_anywhere_hovercards = $twc_at_anywhere_hovercards == 'Y' ?
	    "checked='true'" : "";
		
        $twc_at_anywhere_tweetbox = $twc_at_anywhere_tweetbox == 'Y' ?
	    "checked='true'" : "";

        $twc_tweet_button = $twc_tweet_button == 'Y' ?
	    "checked='true'" : "";
		
        $twc_tweet_this = $twc_tweet_this == 'Y' ?
	    "checked='true'" : "";
		
	$twc_profile_images_chk1 = $twc_profile_images == 'http://api.twitter.com/1/users/profile_image/%%username%%' ?
            "checked='true'" : "";
	$twc_profile_images_chk2 = $twc_profile_images != 'http://api.twitter.com/1/users/profile_image/%%username%%' ?
            "checked='true'" : "";
			

?>
<script type="text/javascript">
	jQuery(function() {
		jQuery("#twc_tabs").tabs();
	});
</script>
    <h3>Twit Connect Configuration</h3>
    <form action='' method='post' id='twc_conf'>
<div id="twc_tabs"> 
	<ul> 
		<li><a href="#twc_tab1">Connection Type</a></li> 
		<li><a href="#twc_tab2">@anywhere</a></li> 
		<li><a href="#twc_tab3">General Settings</a></li> 
		<li><a href="#twc_tab4">Comment Configuration</a></li> 
		<li><a href="#twc_tab5">Login Configuration</a></li> 		
	</ul>     

<div id="twc_tab1">	
      <table cellspacing="20" width="80%">
        <tr>
        <td valign="top">Self-Hosted Application</td>
        <td>
<?php if(!version_compare(PHP_VERSION, '5.0.0', '<')) : ?>        
        <table>
        <tr><td valign="top" width="5%">
          <input type='checkbox' name='twc_local' value='Y' 
            <?php echo $twc_local ?>/></td><td valign="top">
            <small>Check this box to use your own Consumer Key and Consumer Secret.</small>
            <br/><small>For this option, you must register a new application at <a href="http://dev.twitter.com/apps/new">Twitter.com</a>.  Enter the url for your blog in the Application Website and Callback URL.</small>
            <br/><small>Help in filling out the registration can be found on the <a href="http://www.voiceoftech.com/swhitley/?page_id=706">Twit Connect</a> page.</small>
            </td></tr></table>
          </td>
        </tr>
        <tr>
          <td width="20%" valign="top">Consumer Key</td>
          <td>
            <input type='text' name='twc_consumer_key' value='<?php echo $twc_consumer_key ?>' size="50" />
                <br/><small>
                  (Optional) Your application consumer key from Twitter.com.
                </small>
          </td>
        </tr>
        <tr>
          <td width="20%" valign="top">Consumer Secret</td>
          <td>
            <input type='text' name='twc_consumer_secret' value='<?php echo $twc_consumer_secret ?>' size="50" />
                <br/><small>
                  (Optional) Your application consumer secret from Twitter.com.
                </small>
          </td>
        </tr>
        </table>
<?php else: ?>                
PHP Version 5 or greater is required for the self-hosted option.  Click the General Settings tab to continue.
<?php endif; ?>        
        </div>
        <div id="twc_tab2">
<?php if(!version_compare(PHP_VERSION, '5.0.0', '<')) : ?>  
        <table cellspacing="20" width="80%">
        <tr><td colspan="2"><strong>@anywhere</strong> - Enable Twitter's <a href="http://dev.twitter.com/anywhere/begin" target="_blank">@anywhere</a> javascript framework.</td></tr>        
        <tr>
          <td width="20%" valign="top">Activate @anywhere</td>
          <td>
        <table>
        <tr><td valign="top" width="5%">
          <input type='checkbox' name='twc_at_anywhere' value='Y' 
            <?php echo $twc_at_anywhere ?>/></td><td valign="top">
            <small>Adds the @anywhere javascript library to your blog header.  Requires the Self-Hosted Application settings (See the `Connection Type` tab).</small>
            </td></tr></table>
          </td>
        </tr>
        <tr>
          <td width="20%" valign="top">Hovercards</td>
          <td>
        <table>
        <tr><td valign="top" width="5%">
          <input type='checkbox' name='twc_at_anywhere_hovercards' value='Y' 
            <?php echo $twc_at_anywhere_hovercards ?>/></td><td valign="top">
            <small>Automatically add links to '@username' and enable hovercards for each link.</small>
            </td></tr></table>
          </td>
        </tr>
        <tr>
          <td width="20%" valign="top">Follow Button Username</td>
          <td>
                     <input type='text' name='twc_at_anywhere_followbutton' value='<?php echo $twc_at_anywhere_followbutton ?>' size="50" /><br/>
                <small>
                  Enter a Twitter username in the box above.  Next, add an element to your theme where you want a Twitter follow button to appear.  The id for the
                  element must be in this format:  'follow-on-twitter-{username}'.<br/>Example:  &lt;span id="follow-on-twitter-ev">&lt;/span>
                </small>
          </td>
        </tr>
        <tr>
          <td width="20%" valign="top">Tweetbox</td>
          <td>
        <table>
        <tr><td valign="top" width="5%">
          <input type='checkbox' name='twc_at_anywhere_tweetbox' value='Y' 
            <?php echo $twc_at_anywhere_tweetbox ?>/></td><td valign="top">
            <small>Add a Twitter tweet box below the comment form.</small>
            </td></tr></table>
          </td>
        </tr>
        <tr>
          <td width="20%" valign="top">Tweet Button</td>
          <td>
        <table>
        <tr><td valign="top" width="5%">
          <input type='checkbox' name='twc_tweet_button' value='Y' 
            <?php echo $twc_tweet_button ?>/></td><td valign="top">
            <small>Add an official Twitter Tweet Button to each post.</small>
            </td></tr></table>
          </td>
        </tr>
        
        </table>
<?php else: ?>                
PHP Version 5 or greater is required.
<?php endif; ?>        
        </div>
        <div id="twc_tab3">
        <table cellspacing="20" width="80%">
        <tr>
          <td width="20%" valign="top">Twitter Login Suffix</td>
          <td>
            <input type='text' name='twc_user_login_suffix' value='<?php echo $twc_user_login_suffix ?>' size="20" /> [Once set, do not change.]
                 <br/><small>
                  (Recommended) Add a suffix to all Twitter logins to keep them separate<br/>from other logins.
                  <br/><br/>Example: Enter <strong>@twitter</strong> into the box above.  The next Twitter account<br/>
                  created on your blog will be {user name}@twitter.
                </small>
          </td>
        </tr>
        <tr>
          <td width="20%" valign="top">Default E-mail Address</td>
          <td>
            <input type='text' name='twc_email_default' value='<?php echo $twc_email_default ?>' size="50" />
                 <br/><small>
                  Enter a default e-mail address for new users.  %%username%% will be replaced with the user's Twitter name.  Use a valid e-mail domain to avoid issues with spam filtering plugins (such as Akismet).
                </small>
          <?php
            $sql = "SELECT user_email FROM $wpdb->users WHERE user_email = 'nomail@nomail.com' limit 1";
            $nomail_exists = $wpdb->get_var($wpdb->prepare($sql));
            if($nomail_exists == 'nomail@nomail.com'):
          ?>
                <br/><br/>
                <h4>One-time Conversion</h4>
                <input class="button-primary" type="submit" name='twc_nomail_update' value='Convert' /><br/>
                <small>Convert all nomail@nomail.com addresses to your new default e-mail format.</small><br/><br/>
          <?php
            endif;
          ?>
          </td>
        </tr>
        <tr>
          <td width="20%" valign="top">Twitter Profile Images</td>
          <td>
            <table>
            <tr><td><input type="radio" name="twc_profile_images_chk" <?php echo $twc_profile_images_chk1 ?> value="spiurl" /> Twitter Static Image Links<br/> 
		    <input type="radio" name="twc_profile_images_chk" <?php echo $twc_profile_images_chk2 ?> value="self" /> Self-Hosted (<a href="http://wordpress.org/extend/plugins/spiurl/" target="_blank">SPIURL</a> Plugin)<br/>
	            <input type='text' name='twc_profile_images' value='<?php echo $twc_profile_images ?>' size="75" />
			<br/><small>
                  Enter the link to the service that provides your Twitter profile images.
		  <br/>%%username%% will be replaced with the user's Twitter name.<br/>
                  Leave this field blank to accept the default location.
		</small>
	    </td></tr>
            </table>
          </td>
        </tr>
        <tr>
        <td valign="top">Select a Button</td>
        <td>
        <table>
        <?php 
            if(count($twc_btn_images) == 0)
            {
                $twc_btn_images = twc_get_buttons();
            }
        foreach($twc_btn_images as $twc_btn_image): ?>
            <tr><td width="5%">
            <input type='radio' name='twc_btn_choice' value='<?php echo $twc_btn_image ?>' 
            <?php echo $twc_btn_choice == $twc_btn_image ? ' checked="checked" ' : ' '; ?>/></td>
            <td><img src="<?php echo $twc_btn_image ?>" alt="" /></td></tr>
        <?php endforeach; ?>
            </table>
          </td>
        </tr>
        <tr>
        <td valign="top">Javascript Position<br/>(Optional)</td>
        <td>
        <table>
        <tr><td colspan="3">This is an alternative to modifying your theme.  Locate the id of an html element on a page and enter it into the appropriate box below.  The Twit Connect text and button will appear before that element.
        <p><small>Example: Enter <strong>commentform</strong> in the <strong>Comment Page</strong> box to place the button at the top of the comment section.</small></p>
        </td></tr>
        <tr valign="top">
            <td width="100">Comment Page</td><td width="10"></td><td>Login Page</td>
            </tr>
            <tr valign="top">
            <td><input type='text' name='twc_before_comment' value='<?php echo $twc_before_comment ?>' size="20" /><br/>
            </td>
            <td></td>
            <td><input type='text' name='twc_before_login' value='<?php echo $twc_before_login ?>' size="20" />
            </td>
            </tr>
            </table>
          </td>
        </tr>
       <tr>
          <td valign="top">Tweet Quote Template</td>
          <td>
            <textarea name='twc_tweet_quote_template' rows="5" cols="50"><?php echo $twc_tweet_quote_template; ?></textarea>
            <br/>
            <small>Template for Tweet Quotes.<br/>Copy tweets from search.twitter.com and place them between [tweet][/tweet] tags.
            <br/>It's always best to use the post editor's HTML view (not Visual) when using these codes. 
            <br/>%%username%% will be replaced with the Twitter username.  %%tweet%% will be replaced with the tweet.</small>
          </td>
        </tr>
        </table>
        </div>
        <div id="twc_tab4">
        <table cellspacing="20" width="80%">
        <tr>
        <td valign="top">Author Link</td>
        <td>
        <table><tr valign="top"><td width="5%">
          <input type='checkbox' name='twc_use_twitter_profile' value='Y' 
            <?php echo $twc_use_twitter_profile ?>/></td><td>
            <small>Check this box if you would like the author link to point to the author's Twitter profile (http://twitter.com/{username}).</small>
            </td></tr></table>
          </td>
        </tr>
   <tr>
          <td valign="top">Comment Page Text</td>
          <td>
            <textarea name='twc_template' rows="5" cols="50"><?php echo $twc_template; ?></textarea>
            <br/>
            <small>The text that appears above the Twit Connect button on the comment page.  Do not remove class="twc_connect".</small>
          </td>
        </tr>
   <tr>
          <td valign="top">Add to Comment Page</td>
          <td>
          <table><tr valign="top"><td width="5%">
          <input type='checkbox' name='twc_add_to_comment_page' value='Y' 
            <?php echo $twc_add_to_comment_page ?>/></td><td>
            <small>Check this box if you would like the Twit Connect button to appear on each comment page.</small>
            </td></tr></table>
          </td>
        </tr>
   <tr>
   <tr>
          <td valign="top">Redirect to Profile Page</td>
          <td>
          <table><tr valign="top"><td width="5%">
          <input type='checkbox' name='twc_comment_redirect' value='Y' 
            <?php echo get_option('twc_comment_redirect') == 'Y' ? ' checked="checked" ' : ' '; ?>/></td><td>
            <small>If the e-mail address has not been changed, redirect the user to the profile page following a login.</small>
            </td></tr></table>
          </td>
        </tr>
   <tr>
   
          <td valign="top">Tweet This Comment</td>
          <td>
            <input type='checkbox' name='twc_tweet_this' value='Y' 
            <?php echo $twc_tweet_this ?> />
            <input type='text' name='twc_tweet_this_text' value='<?php echo $twc_tweet_this_text ?>' size="70" />
            <br/>
            <small>Display a checkbox that allows visitors to automatically tweet a link when they submit a comment.  Replacement variables: %%post_title%%, %%blog_title%%, and %%shortlink%%.</small>
            <div style="border:solid 1px #CCCCCC;background-color:#F1F1F1;margin:5px;padding:5px;">The <strong>Tweet This Comment</strong> option requires Read & Write Access when you setup your Twitter Application.</div>
          </td>
        </tr>
      </table>
      </div>
   <div id="twc_tab5">
   <table cellspacing="20" width="80%">
   <tr>
          <td valign="top">Add to Login Page</td>
          <td>
          <table><tr valign="top"><td width="5%">
            <input type='checkbox' name='twc_add_to_login_page' value='Y' 
            <?php echo $twc_add_to_login_page ?>/></td><td>
            <small>Check this box if you would like the Twit Connect button to appear on the WordPress login page.</small>
            </td></tr>
            </table>
          </td>
   </tr>
   <tr>
          <td valign="top">Login Page Text</td>
          <td>
            <textarea name='twc_login_text' rows="5" cols="50"><?php echo $twc_login_text; ?></textarea>
            <br/>
            <small>The text that appears above the Twit Connect button on the login page.    Do not remove class="twc_connect".</small>
          </td>
   </tr>
   <tr>
          <td valign="top">Redirect After Login</td>
          <td>
            <input type='text' name='twc_redirect' value='<?php echo $twc_redirect ?>' size="50" />
            <br/>
            <small>The user will be taken to this address after a successful login.  This is only applied to the Login Page.</small>
          </td>
   </tr>
   </table>
   </div>
   </div>
      <p class="submit">
        <input class="button-primary" type='submit' name='twc_save' value='Save Settings' />
      </p>
    </form>
<?php
			
}

?>
