$(document).ready(function() {
	$('#entityForm').validator({disable: true});
});



function submitSubForm(formID,formDiv,messageDiv,messageSpan,selectID)
{
	if(messageDiv && messageDiv.length>0) {
		hideElement(messageDiv);
	}
	
	var form = document.getElementById(formID);
	var data = {};
	var name = '';
	
	for (var i=0; i<form.length; ++i) {
		var field = form[i];
		if (field.name && field.type!='button') {
			data[field.name] = field.value;
			if (field.name=='name') {
				// sock away for populating select option on main form
				name = field.value;
			}
		}
	}
	
	var request = new XMLHttpRequest();
	request.open(form.method,form.action,true);
	request.setRequestHeader('Content-Type','application/json; charset=UTF8');
	request.onreadystatechange=function() {
		 if (request.readyState==4) {
		 	if (request.status==200) {
		 		var response = JSON.parse(request.responseText);
		 		document.getElementById('id').value = response.id;
		    	if(messageDiv && messageDiv.length>0) {
			    	 setMessage('Record (id=' + response.id + ') saved successfully.',messageDiv,messageSpan,true);
			    	}
			    // add new value to select and set selected
			    var select = document.getElementById(selectID);
			    var option = document.createElement("option");
			    option.value = response.id; 
			    option.text = name;
			    option.selected = true;
			    select.add(option);
			    // close modal form
				hideElement(formDiv);
		    	}
		   	else {
		   		if(messageDiv && messageDiv.length>0) {
		   			setMessage('Save failed: ' + request.responseText,messageDiv,messageSpan,false);
		   		}
		   	}
		  }  
		};
	request.send(JSON.stringify(data));
}

function addSubEntity(divid) {
	showElement(divid);
}

function setMode(mode) {
	var type = document.getElementById('type').value;
	var id = document.getElementById('id').value;
	if (mode=='edit')
		{
		var newURL = 'entityPage.php?type=' + type + '&id=' + id + '&mode=edit';
		window.location = newURL;
		}
	else if(mode=='view') {
		var id = document.getElementById("id").value;
		var newURL;
		if (id==0) {
			newURL = "index.php";
		}
		else {
			var newURL = window.location.href.split('?')[0] + "?type=" + type +"&id=" + id + "&mode=view";
		}
		window.location = newURL;		
	}
}

function saveEntity() {
	
	// check to see whether there is an image upload control on page. If there is, upload image first and get its new URL
	var imageUploader = document.getElementById('uploadImage');
	if (imageUploader) {
		// this is still a work in progress
		
		var form = document.getElementById('uploadImage');
		var fileInput = document.getElementById('fileUpload');
		var file = fileInput.files[0];
		var request = new XMLHttpRequest();
		var boundary = '----WebKitFormBoundaryGdDCLBKdAZ8t8lO3';
		request.open(form.method,form.action,true);
		//request.setRequestHeader('Content-Type','multipart/form-data; boundary=' + boundary);
		//request.setRequestHeader('Content-Type',false);
		request.onreadystatechange=function() {	
			if (request.readyState==4) {
			 	if (request.status==200) {
			 		var response = JSON.parse(request.responseText);
			 		alert(response.url);
			   	}
			   	else {
			   		// error saving entity
			   		alert(request.responseText);
			   		}  
				}
			};		
		request.send(file);
		}
	
	submitForm('entityForm','messageDiv','messageSpan',false,'id',afterSave);

}

function afterSave(success) {
	// invoked as callback from submitForm
	if (success) {
		// need to reload page with new entity
		var id = document.getElementById('id').value;
		var type = document.getElementById('type').value;
		var url = 'entityPage.php?type=' + type + '&id=' + id + '&mode=view';
		window.location = url;

	}
}



