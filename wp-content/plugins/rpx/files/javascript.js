
function hideRPX(rpxelement){
  document.getElementById(rpxelement).style.display = 'none';
}

function hidePassMsg(){
  document.getElementById('reg_passmail').style.display = 'none';
}

function setRPXuser(userlogin){
  document.getElementById('user_login').value=userlogin;
  setTimeout(focusEmail,200);
}

function focusEmail(){
  document.getElementById('user_email').focus();
}

function showRPX(rpxelement, commentID){
	document.getElementById(rpxelement).style.display = 'block';
	if( commentID )
		rpx_setCookie('anchor_to_comment', commentID, 1);
	
}

function rpx_setCookie( c_name,value,exdays ){
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; ");
	document.cookie=c_name + "=" + c_value;
}

