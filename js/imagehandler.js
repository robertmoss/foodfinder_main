/*
 * This library includes functions to handle the uploading, processing, and management of images
 * 
 * 
 */

$("#uploadForm").submit(function(event) {
	alert('submitting form.');
	/*showWorkingPanel('Uploading files . . .');
	$(this).ajaxSubmit();
	return false;*/
});

function uploadForm() {
	showWorkingPanel('Uploading files . . .');
	var options = {
		success: uploadSuccess,
		error: uploadError
	};
	$("#uploadForm").ajaxSubmit(options);
	return false;
}

function uploadSuccess() {
	showWorkingPanelResults('Upload succeeded.','success');
	locationid = $("#id").val();
	loadMediaForLocation(locationid);
	$("#uploadForm").trigger("reset");
}

function uploadError(response) {
	showWorkingPanelResults('Upload failed: ' + response.responseText,'error');
}
