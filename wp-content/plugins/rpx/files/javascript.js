function showRPX(rpxelement){
  document.getElementById(rpxelement).style.display = 'block';
}

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
