window.onload = function()
{
	loadProfile();
};

function loadProfile() {
	showWorkingPanel('Loading profile...');
	var id = getElementValue('txtPasswordUserId');
	var serviceURL = "service/user.php?id=" + id + "&detail=yes";
	var template = getUserProfileTemplate();
	getAndRenderJSON(serviceURL, template, 'profileView',null,function(count) {
		if (!count || count<1) {
			showWorkingPanelResults('Unable to load profile','error');
		}
		else {
			hideWorkingPanel();
		}
	});
}

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

function editProfile() {
	$('#profileEdit').collapse('show');
	$('#profileView').collapse('hide');
	hideElement('btnEditProfile');
	showElement('btnCancelProfileEdit');
	showElement('btnSaveProfile');

}

function cancelProfileEdit() {
	$('#profileEdit').collapse('hide');
	$('#profileView').collapse('show');
	showElement('btnEditProfile');
	hideElement('btnCancelProfileEdit');
	hideElement('btnSaveProfile');
}

function saveProfile() {
	submitForm('frmProfile','profileMessage','profileMessage',false,null,function(success) {
		if (success) {
			cancelProfileEdit();
			loadProfile();
			hideElement('profileMessage');
			}
		});
}
