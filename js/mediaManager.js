/**
 * @author Robert Moss
 * MediaManager.js - contains functions for managing images and other media
 */

$(document).ready(function() {
	var count = getElementValue('mediaItemCount');
	var currentOffset = getElementValue('currentOffset');
	var entitiesPerPage = 20;
	var numPage = Math.ceil(count/entitiesPerPage);
	var currentPage = 1;
	if (currentOffset>0) {
		currentPage = Math.ceil(currentOffset/entitiesPerPage)+1;
	}
	var settings = {
		total: numPage,
		maxVisible: 10,
		page: currentPage
	};
	$('#mediaPageSelectorTop').bootpag(settings).on("page",function(event,num) {
		var offset = (num-1) * entitiesPerPage;
		var url = 'mediaManager.php?offset=' + offset;
		window.location = url; 
		});
	$('#mediaPageSelectorBottom').bootpag(settings).on("page",function(event,num) {
		var offset = (num-1) * entitiesPerPage;
		var url = 'mediaManager.php?offset=' + offset;
		window.location = url; 
		});
});

function showMedia(id,url,name) {
	showWorkingPanel('Loading media information . . .');
	setElementText('mediaHeader',name);
	hideElement('media-message');
	document.getElementById('mediaImage').src=url;
	$('#mediaModal').modal({
		keyboard:true
	});

	var serviceURL = getCoreServiceUrl() + "/formService.php?type=media";
	serviceURL += "&id=" + id;
	
	getAndRenderHTML(serviceURL,'mediaFormAnchor','',afterEditFormLoaded);

}

function showAddMedia() {
	showWorkingPanel('Loading media information . . .');
	setElementText('mediaHeader','New Media Item');
	hideElement('media-message');
	$('#mediaModal').modal({
		keyboard:true
	});

	var serviceURL = getCoreServiceUrl() + "/formService.php?type=media";
	serviceURL += "&id=0";
	
	getAndRenderHTML(serviceURL,'mediaFormAnchor','',afterEditFormLoaded);

}

function afterEditFormLoaded(status) {
	if (status==200) {
		hideWorkingPanel();
	}
	else {
		
	}
}

function saveMedia() {
	submitForm('mediaForm','media-message','media-message-text',false,null,afterMediaSave);
}

function afterMediaSave(success) {
	if (success) {
		$('#mediaModal').modal('hide');
		location.reload();
	}
}

function deleteMedia() {
	$id = getElementValue('mediaid');
	if (window.confirm('Are you sure you want to delete this media item? Deletions are permanent and cannot be recovered.')) {
		showWorkingPanel('Deleting media . . .');
		deleteEntity('media',$id,null,afterMediaDelete);
	}
}

function afterMediaDelete(success,text) {
	if (success) {
		location.reload();
	}
	else {
		showWorkingPanelResults('Unable to delete media: ' + text,'error');
	}
}

function showUploadModal() {
	hideElement('media-upload-message');
	$('#mediaUploadModal').modal({
		keyboard:true
	});
}

function uploadMedia() {
	setMessage('Uploading files . . . ','media-upload-message','media-upload-text',true);
	showElement('media-upload-message');
	var options = {
		success: uploadSuccess,
		error: uploadError
	};
	$("#uploadMediaForm").ajaxSubmit(options);
	return false;
}

function uploadSuccess() {
	$('#mediaUploadModal').modal('hide');
	location.reload();
}

function uploadError(message) {
	setMessage('Unable to upload file: ' + message.responseText,'media-upload-message','media-upload-text',false);
}

