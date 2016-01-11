function changePassword() {

	submitForm('frmChangePassword','password-message','password-message-text',false,null,function(success) {
		if (success) {
			 setMessage('Password changed successfully.','password-message','password-message-text',true);
			 var $userid = document.getElementById('txtPasswordUserId').value;
			 document.getElementById('frmChangePassword').reset();
			 document.getElementById('txtPasswordUserId').value=$userid;
		}
	});	
}