$("#searchForm").submit(function(event) {
	alert('submit');
	retrieveResults();
	event.preventDefault();
});

function retrieveResults() {
	
	var criteria = document.getElementById('txtSearch').value;

	if (criteria && criteria.length>0) {
		var serviceURL = "service/entitiesService.php?type=location&name=" + criteria;
		var template = getLocationListTemplate();
		showElement('results');
		getAndRenderJSON(serviceURL,template,'results','retrieving locations',callback);
	}	
	else {
		alert('Invalid criteria.');
	}	
	
}

function callback(count) {
	if (count==0) {
		setElementHTML('results','No matching locations found.');
	}
}
