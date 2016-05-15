function changePassword() {

	submitForm('frmChangePassword','password-message','password-message-text',false,null,function(success) {
		if (success) {
			 setMessage('Password changed successfully. Please login using the new password.','password-message','password-message-text',true);
			 var $userid = document.getElementById('txtPasswordUserId').value;
			 document.getElementById('frmChangePassword').reset();
			 document.getElementById('txtPasswordUserId').value=$userid;
			 hideElement('openError');
			 hideElement('passwordPanel');
			 showElement('loginButton');
			 
		}
	});	
}