/**
 * @author Robert Moss
 */

$(document).ready(function() {
	loadAssignmentList();
});

function searchEntities() {

//	setElementText('entitySearchResults','Test!');
	
	var criteria = document.getElementById('txtEntitySearch').value;
	var txtEntity = document.getElementById('txtEntityListEntity');
	var entityType = txtEntity.value; 

	if (criteria && criteria.length>0) {
		var serviceURL = getCoreServiceUrl() + "/entitiesService.php?type=" + entityType + "&return=5&name=" + criteria;
		 
		var template;
		if (entityType=='location') {
			template = '{{#locations}}<p>{{name}} ({{city}},{{state}}) <button type="button" class="btn btn-default btn-xs" onclick="addEntityListItem(\'{{linkname}}\',{{id}});">Add</button></p>{{/locations}}';
		}
		else {
			template = '{{#products}}<p>{{name}} ({{author}}) <button type="button" class="btn btn-default btn-xs" onclick="addEntityListItem(\'{{name}}\',{{id}});">Add</button></p>{{/products}}';
		}
		showElement('entitySearchResults');
		retrieveLocations(serviceURL,template,'entitySearchResults','searching . . .',afterSearch);
	}	
	else {
		setElementText('entitySearchResults','Please enter text to search for.');
	}	
	
}

function afterSearch(count) {
	if (count==0) {
		setElementText('entitySearchResults','No matching items found.');
	}
}

function addEntityListItem(name,id) {
	var currentValue = getElementValue('txtEntityListItems');
	if (currentValue && currentValue.length>0) {
		currentValue += ',' + id;
	}
	else {
		currentValue=id;
	}
	setElementValue('txtEntityListItems',currentValue);

	var newMarkup = '<p>' + name + '</p>';
	var newMarkup = '<p id="listItem' + id + '"><button type="button" class="btn btn-default btn-xs" onclick="removeListItem(' +id+ ')"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button> ';
	newMarkup += name + '</p>';

	$("#entityListContainer").append(newMarkup);
	
	
}

function afterFeatureFormLoad() {
	// not sure we need to do anything here
}

function afterProductFormLoad() {
	// not sure we need to do anything here
}

function afterProductCollectionFormLoad() {
	// not sure we need to do anything here
}

function afterAssignmentFormLoad() {
	
}

function afterAssignmentListLoad() {
	// set color coding based upon Status field
	$('#assignmentTable tr').each(function(i,row) {
		var row=$(row);
		var status=row[0].childNodes[3].textContent;
		classValue='';
		if (status=='Published'||status=='Complete') {
			classValue='complete';	
		}
		else if (status=='Assigned' || status=='Unassigned') {
			var dueDate=new Date(row[0].childNodes[5].textContent);
			var currentDate = new Date();
			if (dueDate.getTime()<currentDate.getTime()) {
				classValue='late';
			}
		}
		if (classValue.length>0) {
			$(row).addClass(classValue);
		}
	});
}


function afterEntityListFormLoad(status) {
	// wire up sortable list
	$('.sortable').sortable({
		stop: function( event, ui ) {
			handleSort(event,ui);
		}});
}

function handleSort(event,ui) {
	resetEntityList();	
}

function removeListItem(id) {
	$("#listItem" + id).remove();
	resetEntityList();
}

function resetEntityList() {
	var parentDiv = document.getElementById('entityListContainer');
	var idList="";
	var separator="";
	
	// cycle through newly sorted <p>s and rebuilt id list
	for(var i=0;i<parentDiv.children.length;i++) {
		idList += separator + parentDiv.children[i].id.substring(8); 
		separator=",";
	}
	setElementValue('txtEntityListItems',idList);	
}

function txtEntitySearchKeyPress(e) {
	if (e.keyCode==13) {
		searchEntities();
		return false;
	}
}

function lookupProductOnAmazon() {
	// putting this in authoring.js for now; if needed on other pages may need to pull out into its
	// own product script
	
	var $productName = getElementValue('txtProductName');
	if (!$productName) {
		alert('Please enter a product name to search for.');
	}
	else {
		alert('Looking up ' + $productName);
	}
}
