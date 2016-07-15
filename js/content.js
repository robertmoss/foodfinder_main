var currentContentKey='';

window.onload = function() {
	$(".content").hover(function(){
		showElement('floatingButtons');
		panel = document.getElementById('floatingButtons');
		$(this).append(panel);
		button = document.getElementById('editButton');
		$(button).off("click");
		$(button).click(function(e) {
			var element = $(this).parent().parent();
			editContent(element);
			e.stopPropagation();
		});
	}, function() {
		hideElement('floatingButtons');
	}
	
	);

};

function editContent(element) {
	var content_id = element[0].id;
	content_id=content_id.substring(8);
	currentContentKey = element[0].childNodes[0].value;
	
	showElement('btnTenantContentSave');
	document.getElementById('btnTenantContentCancel').innerText="Cancel";
	editEntity(content_id,'tenantContent',onFormLoad);
}

function saveTenantContent() {
	saveEntity('tenantContent',onSave);	
}

function onSave(success) {
	if (success) {
		hideElement('btnTenantContentSave');
		document.getElementById('btnTenantContentCancel').innerText="Close";
		$('#tenantContentEditModal').modal('hide');
		location.reload();
	}
}
function onFormLoad() {
	
	document.getElementById('txtTenantContentName').value=currentContentKey;
	hideElement('field_name');
	document.getElementById('txtTenantContentLanguage').value='en_US';
	hideElement('field_language');	
	
}
