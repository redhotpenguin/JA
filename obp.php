<?php
/*
This script will help us test if output buffering works correctly on Dotcloud.

Desired results:
	#print a json response with no delay.
	#write 'victory' to 'test.htm' 5 seconds after the script is runned
*/


// this simulates a  buddypress function that echoes a comment
function bp_dtheme_blog_comments($comment, $args, $comment_depth){
	echo 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer eleifend, libero id tristique blandit, tortor neque rhoncus arcu, pellentesque dignissim mi sem ut nulla. Duis porttitor, magna quis lacinia fermentum, sapien nisl consectetur est, quis aliquam felis eros nec tellus. Integer eget urna neque. In hac habitasse platea dictumst. Sed quis enim sit amet massa vehicula placerat in sit amet lacus. Sed consectetur purus at enim pellentesque feugiat. Integer gravida tellus vitae dolor pulvinar quis aliquam augue condimentum. Nam hendrerit, ligula eget tempor gravida, augue diam pulvinar nisl, sit amet commodo sem tortor et purus. Donec elementum fringilla ipsum, a malesuada urna molestie vel. Suspendisse non tellus sed arcu mollis bibendum. Mauris justo quam, euismod mattis accumsan quis, rhoncus a dolor. Fusce porttitor, ligula non ultrices adipiscing, tellus quam sollicitudin sapien, nec tincidunt lectus metus eu mauris. Sed vel lobortis orci. Proin volutpat consequat lectus, sed vulputate mauris vestibulum non. Praesent facilisis, sem a mattis aliquam, turpis justo bibendum ligula, non mollis velit nunc et elit.

Donec fringilla, tortor vitae facilisis tempus, felis augue interdum magna, in vestibulum turpis turpis vitae arcu. Ut vitae molestie urna. Morbi ante sem, consectetur non eleifend sit amet, cursus non urna. Cras a est enim, sit amet sodales nisl. Proin sit amet ipsum turpis, vitae bibendum purus. Phasellus adipiscing tellus at ante dignissim dapibus. Quisque gravida nunc at augue elementum ac vulputate mauris mollis. Nam enim metus, tempor et tincidunt a, mattis ut elit.

Curabitur dui quam, luctus at pretium quis, lacinia vitae nulla. Suspendisse tempus risus quis lorem consequat ac pellentesque leo hendrerit. Cras in pretium turpis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas arcu turpis, laoreet vel varius nec, ultrices vel arcu. Mauris egestas urna augue. Phasellus et mi quis nisl molestie commodo sed vitae sem. Phasellus ac bibendum massa. Aliquam porttitor tincidunt urna ac gravida.

Aenean blandit tincidunt orci ut gravida. Aenean fringilla, risus vel tempor vulputate, enim neque posuere justo, vitae interdum lorem elit non eros. Cras nec velit velit, id gravida ligula. Proin viverra orci vel nisl vestibulum condimentum. Praesent pharetra porta quam vel lacinia. Donec ullamcorper leo enim, nec dapibus mi. Duis condimentum ipsum ac tortor porttitor et bibendum leo dictum. Duis porta eleifend nunc et molestie. Vivamus porta, leo quis dictum bibendum, ipsum risus tempor dolor, nec auctor lacus urna laoreet odio. Fusce fermentum purus id nulla placerat facilisis. Integer sed velit non urna bibendum tempor sit amet ac mi.

Donec luctus, ipsum eu congue elementum, justo metus molestie nisl, sed elementum dui lectus a diam. Aliquam non arcu orci, in faucibus leo. Vestibulum et lorem non velit congue dignissim. Fusce tristique, ipsum a malesuada egestas, eros risus laoreet augue, eu consequat arcu arcu eu magna. Quisque aliquet sollicitudin aliquet. Sed ligula ligula, dignissim et vestibulum at, vehicula in dui. Cras blandit, felis et lobortis iaculis, ligula velit sagittis elit, nec molestie justo velit vitae purus. Nullam ut magna nunc. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur a placerat nisl. Aenean in felis sed est mollis placerat. Cras id congue quam.'.date("Y-m-d H:i:s"); 
}


// the follow piece of code simulates a comment after it has been posted and about to be rendered

//capture bp_dtheme_blog_comments() output and store it in $html_content
ob_start();
bp_dtheme_blog_comments(1, 2, 3);
$html_comment = ob_get_contents(); 
ob_clean(); // clean the output buffer

// encode the html into a json response
$json = json_encode( array( "cpid" => 141, "comment" => $html_comment)  );

echo $json; // print the json response to the output buffer

$buffer_size = ob_get_length();

//set response headers
header('Content-type: application/json');
header("Content-Length: $buffer_size");
//header('Connection: close');

// flush the output buffer and turn off output buffering
ob_end_flush();
ob_flush();
flush();

// simulates Subscribe to Comments's slowness
sleep(5);
error_log('victory');
	
?>












