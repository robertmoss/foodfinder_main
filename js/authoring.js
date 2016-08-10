/**
 * @author Robert Moss
 */

$(document).ready(function() {
	loadFeatureList();
});

function searchEntities() {

//	setElementText('entitySearchResults','Test!');
	
	var criteria = document.getElementById('txtEntitySearch').value;

	if (criteria && criteria.length>0) {
		var serviceURL = getCoreServiceUrl() + "/entitiesService.php?type=location&return=5&name=" + criteria;
		var template = '{{#locations}}<p>{{name}} ({{city}},{{state}}) <button type="button" class="btn btn-default btn-xs" onclick="addEntityListItem(\'{{linkname}}\',{{id}});">Add</button></p>{{/locations}}';
		showElement('entitySearchResults');
		retrieveLocations(serviceURL,template,'entitySearchResults','searching . . .',afterSearch);
	}	
	else {
		setElementText('entitySearchResults','Please enter text to search for.');
	}	
	
}

function afterSearch(count) {
	if (count==0) {
		setElementText('entitySearchResults','No matching locations found.');
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
	$("#entityListContainer").append(newMarkup);

	
}

function afterEntityListFormLoad(status) {
	// wire up sortable list
	$('.sortable').sortable({
		stop: function( event, ui ) {
			handleSort(event,ui);
		}});
}

function handleSort(event,ui) {
	alert('sorted!');	
}

function txtEntitySearchKeyPress(e) {
	if (e.keyCode==13) {
		searchEntities();
		return false;
	}
}
