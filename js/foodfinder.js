var locations = [];


function getTenantID()
{
	return document.getElementById("txtTenantID").value;
}


function postCurrentLocation(latitude,longitude,address)
{
	var request = new XMLHttpRequest();
	request.open('POST','service/currentLocation.php',true);
	request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	request.send('latitude='+latitude+'&longitude='+longitude+'&address='+address);
}

function getLocationListTemplate() {
	var template = "<div class=\"row\">{{#locations}}"; 
		template += "<div id=\"loc{{id}}\" class=\"col-md-4\">";
		template += '<div id=\"loc{{id}}_visited\" class="ribbon-wrapper-green {{^visited}}hidden{{/visited}}"><div class="ribbon-green">Visited</div></div>';
			template += "<div class=\"thumbnail loc-panel\">";
				template += "<div class=\"loc-image\">";
					template += "<img src=\"{{imageurl}}\"/>";
				template += "</div>";
				template += "<div class=\"caption\">";
					//template +="<h3 class=\"non-mobile\"><a id=\"link_loc{{id}}\" href=\"#loc{{id}}\" onclick=\"loadLocation({{id}},\'{{linkname}}\',{{#visited}}true{{/visited}}{{^visited}}false{{/visited}});\">{{name}}</a></h3>";
					//template +="<h3 class=\"non-mobile\"><a id=\"link_loc{{id}}\" href=\"#loc{{id}}\" onclick=\"loadLocation({{id}});\">{{name}}</a></h3>";
					//template +="<h3 class=\"mobile-only\"><a href=\"entityPage.php?type=location&mode=view&id={{id}}\">{{name}}</a></h3>";
					template +="<h3><a id=\"link_loc{{id}}\" href=\"#loc{{id}}\" onclick=\"loadLocation({{id}});\">{{name}}</a></h3>";
					template +="{{#distance}}<h3><span class=\"label label-success\">{{distance}} mi</span></h3>{{/distance}}";
					template += "<address>{{address}}<br/>{{city}}, {{state}}<br/><a href=\"tel:{{clickablephone}}\">{{phone}}</a><br/><a href=\"{{url}}\" target=\"_blank\">{{displayurl}}</a></address>";
					template += "<p>{{shortdescription}}{{#more}}<a href=\"location.php?id={{id}}\">more</a>{{/more}}</p>";
				template += "</div>";
			template += "</div>";
		template += "</div>";
		template += "{{/locations}}";
		template += "{{^locations}}<p>No matching locations were found.</p>{{/locations}}";
		template +="</div>";
							
		
	return template;
}

function getLocationTemplate() {
	var template = '<div class=\"bd\">';
		template += '<div id=\"locationid\" class=\"hidden\">{{id}}</div>';
		template += '<div id=\"locationname\" class=\"hidden\">{{name}}</div>';
		template += '<div id=\"loc{{id}}_visited_2\" class="ribbon-wrapper-green {{^visited}}hidden{{/visited}}"><div class="ribbon-green">Visited</div></div>';
		template += '<div class=\"locationModalImage\">{{#imageurl}}<img src=\"{{imageurl}}\"/>{{/imageurl}}</div>';
		template += '{{#categories.length}}<h3>{{#categories}}<span class="label label-info">{{name}}</span> {{/categories}}</h3>{{/categories.length}}';
		template += '<address>';
		template +=  	'{{#address}}{{address}}{{/address}}<br/>';
		template +=     '{{city}}, {{state}}<br/><a href=\"tel:{{clickablephone}}\">{{phone}}</a><br/>';
		template +=     '{{#url}}<a href=\"{{url}}\" target=\"_blank\">{{displayurl}}</a>{{/url}}';
		template += '</address>';
		template += '<div class=\"panel panel-default\">';
		template += '	<div class=\"panel-body\">{{shortdesc}}</div>';
		template += '</div>';
		template += '<p><span class=\"list-label\">Status:</span> {{status}}</p>';
		template += '{{#properties}}<p><span class=\"list-label\">{{key}}:</span> {{value}}</p>{{/properties}}';
		template += '{{#links.length}}<div class=\"panel panel-info\">';
		template += '	<div class=\"panel-heading\">Read More</div>';
		template += '	<div class=\"panel-body\">{{#links}}<p><a href=\"{{url}}\" target=\"_blank\">{{name}}</a></p>{{/links}}</div>';
		template += '</div>{{/links.length}}';
		//template += '{{#shared}}<div class="alert alert-warning" role="alert"><p>Shared by tenant {{owningtenant}}</p>{{/shared}}</div>';
		/*template += '<div class=\"panel panel-info\">';
		template += '	<div class=\"panel-body\"><p>Image list will go here</p></div>';
		template += '</div>';*/
		template += "</div></div>";
	
	return template;
}

function getLocationSummaryTemplate() {
		var template = "<div id=\"loc{{id}}\" class=\"listItem\">";
			template += "<div class=\"thumbnail loc-panel\">";
				template +="<h3><a id=\"link_loc{{id}}\" href=\"#loc{{id}}\" onclick=\"loadLocation({{id}});\">{{name}}</a>&nbsp;";
				template +="{{#distance}}<span class=\"label label-success right\">{{distance}} mi</span>{{/distance}}</h3>";
				template += "<p>{{shortdescription}}{{#more}}<a href=\"#loc{{id}}\" onclick=\"loadLocation({{id}},\'{{linkname}}\',{{#visited}}true{{/visited}}{{^visited}}false{{/visited}});\">more</a>{{/more}}</p>";
				template += "<div class=\"loc-image\">";
					template += "<img src=\"{{imageurl}}\"/>";
							template += '<div id=\"loc{{id}}_visited\" class="ribbon-wrapper-green {{^visited}}hidden{{/visited}}"><div class="ribbon-green">Visited</div></div>';
				template += "</div>";
				template += "<div class=\"caption\">";
					//template +="<h3 class=\"non-mobile\"><a id=\"link_loc{{id}}\" href=\"#loc{{id}}\" onclick=\"loadLocation({{id}},\'{{linkname}}\',{{#visited}}true{{/visited}}{{^visited}}false{{/visited}});\">{{name}}</a></h3>";
					//template +="<h3 class=\"non-mobile\"><a id=\"link_loc{{id}}\" href=\"#loc{{id}}\" onclick=\"loadLocation({{id}});\">{{name}}</a></h3>";
					//template +="<h3 class=\"mobile-only\"><a href=\"entityPage.php?type=location&mode=view&id={{id}}\">{{name}}</a></h3>";
					template += "<address>{{address}}<br/>{{city}}, {{state}}<br/><a href=\"tel:{{clickablephone}}\">{{phone}}</a><br/><a href=\"{{url}}\" target=\"_blank\">{{displayurl}}</a></address>";
					//template += "<p>{{shortdescription}}{{#more}}<a href=\"#loc{{id}}\" onclick=\"loadLocation({{id}},\'{{linkname}}\',{{#visited}}true{{/visited}}{{^visited}}false{{/visited}});\">more</a>{{/more}}</p>";
				template += "</div>";
			template += "</div>";
		template += "</div>";							
		
	return template;
}

function getImageStripTemplate(editable) {
	var template =  '<div class="row">';
		template +=	'	{{#media}}<div class="col-sm-2">';
		template +=	'		<span><a href="#"><div id="media{{id}}" class="thumbnail"><img src="{{url}}" alt="image"></div></a></span>';
		if (editable) {
			template += '		<div class="hover-controls">';
			template += '			<button type="button" class="close" onclick="deleteMedia({{id}});" data-toggle="tooltip" title="Delete media"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span><span class="sr-only">Delete</span></button>';
			template += '			<button type="button" class="close" onclick="editMedia({{id}});" data-toggle="tooltip" title="Edit info"><span aria-hidden="true" class="glyphicon glyphicon-pencil"></span><span class="sr-only">Edit</span></button>';
			template += '			<button type="button" class="close" onclick="setMediaAsPrimary({{id}});" data-toggle="tooltip" title="Set as primary image"><span aria-hidden="true" class="glyphicon glyphicon-star"></span><span class="sr-only">Set as Primary</span></button>';
			template += '		</div>';
		}
		template += '	</div>{{/media}}';
		template += '</div>';
		
		return template;
}

function getDirectionsTemplate() {
	var template = '<h2>Directions</h2>{{#.}}';
	template += "total distance: {{#distance}}{{text}}{{/distance}}";
	template += "{{#steps}}{{instructions}}{{/steps}}";
	template += "{{/.}}";
	
	return template;
}

function getUserProfileTemplate() {
	var template = '<div><label>Name</label>: {{name}}</div>';
		template += '<div><label>Email</label>: {{email}}</div>';
		template += '<div><label>Twitter Handle</label>: {{twitterHandle}}</div>';
		template += '<div><p>{{bio}}</p></div>';
		template += '<div><label>Sites:</label>{{#tenants}}<p><a href="#">{{tenant}}</a>{{/tenants}}';
	return template;
}
 
function removeChildRow(id) {
	// used in AJAX forms generated by the formService - simply removes a child entity from the onscreen list
	// when form posts, entity service will handle delinking from parent or deleting
	var childRow = document.getElementById(id);
	if (childRow) {
		childRow.parentNode.removeChild(childRow);
	}	 
}




function checkGooglePlaces(entity) {
	var name = document.getElementById('txt' + entity + 'Name').value;
	var latitude = getElementValue('txtCurrentLatitude');
	var longitude = getElementValue('txtCurrentLongitude');
	if (!name || name.length==0) {
		alert('Please enter a name to search for.');
	}
	else {
		var URL = 'https://maps.googleapis.com/maps/api/place/textsearch/json?key=AIzaSyB9Zbt86U4kbMR534s7_gtQbx-0tMdL0QA';
		//URL += '&location=' + latitude + ',' + longitude + '&radius=50000'
		URL += '&query=' + name + '&types=food&sensor=false';
		var curLocation = new google.maps.LatLng(latitude,longitude);
		map = new google.maps.Map(document.getElementById('mapcanvas'), {
      		center: curLocation,
      		zoom: 8
	    	});
		var request = {
    		location: curLocation,
    		radius: '50000',
    		query: name
  			};
		service = new google.maps.places.PlacesService(map);
  		service.textSearch(request, placesCallback);		
	}
}

function placesCallback(results,status) {
	if (status == google.maps.places.PlacesServiceStatus.OK) {
	//showElement('mapwrapper');
	    for (var i = 0; i < results.length && i<3; i++) {
	      var place = results[i];
	      dropMarker(map, place.geometry.location, place.name);
	    }
	    if (results.length>0) {
	    	var place = results[0];
	    	var request = { reference: place.reference};
	    	service.getDetails(request,placesCallbackDetails);
	    	}
	   }
	else
	   {
	    alert('no matches found.');	
	   }	
    
}

function placesCallbackDetails(place,status) {
	
	type = ucfirst(getElementValue('type'));
	
	document.getElementById('txt' + type + 'Name').value = place.name;
    var addressInfo = place.formatted_address.split(','); 
	document.getElementById('txt' + type + 'Address').value = addressInfo[0].trim();
    document.getElementById('txt' + type + 'City').value = addressInfo[1].trim();
    document.getElementById('txt' + type + 'State').value = addressInfo[2].substring(0,3).trim();
    
    document.getElementById('txt' + type + 'Phone').value = place.formatted_phone_number;
    document.getElementById('txt' + type + 'Url').value = place.website;
    
    document.getElementById('txt' + type + 'Latitude').value = place.geometry.location.lat();
    document.getElementById('txt' + type + 'Longitude').value = place.geometry.location.lng();
    
    document.getElementById('txt' + type + 'GoogleReference').value = place.reference;
}

function lookupLatLng(type) {
	
	var address = '';
	
	address+=getElementValue('txt' + type + 'Address');
    address+=' ' + getElementValue('txt' + type + 'City');
    address+=' ' + getElementValue('txt' + type + 'State');
	
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode({'address': address}, function(results,status) {
		if (status==google.maps.GeocoderStatus.OK) {
			 document.getElementById('txt' + type + 'Latitude').value = results[0].geometry.location.lat();
    		 document.getElementById('txt' + type + 'Longitude').value = results[0].geometry.location.lng();
		}
		else {
			alert('Unable to geocode the address (' + address + ').');
		}
	});
	
}

function retrieveLocations(serviceURL,template,anchor,working,callback) {
		if (working && working.length>0)
			{
				document.getElementById(anchor).innerHTML = working;
			}
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function() {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200) {
		    var view = JSON.parse(xmlhttp.responseText);
		    locations = view.locations;
			renderTemplate(template,view,anchor);
			if (callback) {
				callback(view.count);
			}
		   }
		 };
		xmlhttp.open("GET",serviceURL,true);
		xmlhttp.send();
		}


function loadLocation(id) {
	
	setElementHTML('locationBody','<div class="ajaxLoading">Loading location . . .</div>');
	var serviceURL = "core/service/entityService.php?type=location";
	serviceURL += "&id=" + id;
	
	$("#locationModal").modal({
 	   backdrop: 'static',
    	keyboard: false
		});
	getJSON(serviceURL,null,null,loadLocationCallback,loadLocationError);
		
}

function loadLocationCallback(location) {
	
	setElementHTML('locationHeader',location.name);
	var template = getLocationTemplate();
	renderTemplate(template,location,'locationBody',false);
	chkVisit = document.getElementById('chkVisited');
	if (chkVisit) {
		chkVisit.checked=(location.uservisits>0); 
	}
	if (location.editable) {
		showElement('btnViewMaster');
		showElement('btnEditLocation');
	}
	else {
		hideElement('btnViewMaster');
		hideElement('btnEditLocation');
	}	
}

function loadLocationError(message) {
	setElementText('locationBody','Unable to load location information: ' + message);
}

function editLocation() {
	var id = document.getElementById('locationid').innerHTML;
	var name = document.getElementById('locationname').innerHTML;
	var url = 'location.php?id=' + id + '&mode=edit';
	//window.open(url,'locationedit');
	
	hideElement('messageDiv');
	setElementHTML('locationEditHeader',name);
	
	// hide location modal - multiple modals open at same time causes issues
	$("#locationModal").modal('hide');
	
	$("#locationEditModal").modal({
 	   backdrop: 'static',
    	keyboard: false
		});

	var serviceURL = "core/service/formService.php?type=location";
	serviceURL += "&id=" + id;
	
	getAndRenderHTML(serviceURL,'locationEditContainer','',prepareEdit);
}

function prepareEdit(status) {
	hideElement('locationEditLoading');
	document.getElementById('locationEditSaveButton').disabled=false;
}

function cancelEdit() {
	// necessary because bootstrap doesn't like multiple modals opened simultaneously
	$('#locationEditModal').modal('hide');
	$("#locationModal").modal({
 	   backdrop: 'static',
    	keyboard: false
		}); 
}

function saveLocation() {
	submitForm('locationForm','messageDiv','messageSpan',false,'locationid',saveComplete);
}

function saveComplete(success) {
	// invoked as callback from submitForm
	if (success) {
		$("#locationEditModal").modal('hide');
		// reload location modal to get new data
		var id = document.getElementById('locationid').innerHTML;
		var name=document.getElementById('locationname').innerHTML;
		loadLocation(id,name);
	}
}

function setLocationPrimaryImage(url) {
	
}

function getIconForLocation(location) {
	// ensures we return consistent set of icons across pages
	var icon = 'img/icons/ltblue-dot.png';
	// check to see if a tenant-specific default icon has been specified
	iconElement = document.getElementById('defaultIcon');
	if (iconElement) {
		icon = iconElement.value;
	}
	if (location.status=='Closed') {
		icon = 'img/icons/closed.png';
	}
	else if (location.status=='Pending') {
		icon = 'img/icons/pending.png';
	}
	else if (location.icon) {
		icon = location.icon;
		}
	return icon;
}


function getInfoWindowContent(location) {
	
	var shortdesc = location.shortdescription;
	if (!shortdesc) {
		var shortdesc = location.shortdesc;
	}
	if (shortdesc && shortdesc.length>100) {
		shortdesc = shortdesc.substring(0,100) + "...";
		}
	var visited = 'false';
	if (location.visited) {
		visited='true';
	}	
	//var link =  '<a href="#" onclick="loadLocation('+ location.id +',\'' + escapeSingleQuotes(location.name) + '\'' +  ',' + visited + ');">' + location.name + '</a>';
	var link =  '<a href="#" onclick="loadLocation('+ location.id + ');">' + location.name + '</a>';
	var flag = '';
	if (location.status=="Closed"||location.status=="Temporarily Closed") {
		flag += '<div class="flag warning"><span class="flag warning"><span class="glyphicon glyphicon-alert" aria-hidden="true"></span> ' + location.status +  '</span></div>';
	}
	if (location.top_category) {
		flag += '<div><span class="flag">' + location.top_category +  '</span></div>';
	}											
	var contentString = '<div class="mapInfoWindow">' + 
							'<div class="name">' + link + '</div>' +
							flag +
							'<div class="description">' + shortdesc + '</div>' +
						'</div>';
							
	return contentString;
}

function getLocationById(id) {
	var location;
	for (i=0;i<locations.length;i++) {
		if (locations[i].id==id) {
			location=locations[i];
			break;
		}
	}
	return location;
}

function setVisited() {
	
	var value = document.getElementById('chkVisited').checked;
	var locationid = document.getElementById('locationid').innerText;
	
	// call service to update visited status
	updateLocationVisited(locationid,value,visitedUpdated);

}

function updateLocationVisited(locationid,visited,callback) {
	
	var data = {};
	
	data['id'] = locationid;
	data['visited'] = visited;
	data['action'] = 'setVisited'; 

	var url = 'service/location.php';

	var request = new XMLHttpRequest();
	request.open('POST',url,true);
	request.setRequestHeader('Content-Type','application/json; charset=UTF8');
	request.onreadystatechange=function() {
		 if (request.readyState==4) {
		 	if (request.status==200) {
		 		var response = JSON.parse(request.responseText);
			    success=true;	
		    	}
		   	else {
		   		// do anything special if post fails?
		   		success=false;
		   	}
		   	callback(success);
		  }  
		};
	request.send(JSON.stringify(data));	
}

function visitedUpdated(success) {
	var value = document.getElementById('chkVisited').checked;
	var locationid = document.getElementById('locationid').innerText;
	var location = getLocationById(locationid);
	// to do:
	// 1. update page elements to indicate working
	// 1A. map marker
	marker = markers[locationid];
	if (!value) {
			location.uservisits=0;
			marker.setIcon(getIconForLocation(location));
			hideElement('loc' + locationid +'_visited');
			hideElement('loc' + locationid +'_visited_2');
		}
	else {
		location.uservisits=1;
		marker.setIcon('img/icons/visited.png');
		showElement('loc' + locationid +'_visited');
		showElement('loc' + locationid +'_visited_2');
	}
	
}


// CONFIG - configuration functions
function showConfig() {
	$('#configModal').modal();
}

function updateSettings() {
	$('#configModal').modal('hide');
	
	// must implement the "applyNewSettings" function in javascript file for your specific page
	// what each page must do to adapt to settings will differ
	applyNewSettings();	
}

function getCategoryFilter() {
	var categoryFilter='';
	var separator='';
	var list = document.getElementsByClassName('categoryInput');
	for (i=0;i<list.length;i++) {
		if (list[i].checked) {
			categoryFilter+= separator + list[i].value;
			separator="|";
		}
	}

	return categoryFilter;
}

function logIssue() {
	showElement('modalIssueSubmit');
	hideElement('modalIssueMessage');
	setElementText('modalIssueCancel','Cancel');
	document.getElementById('txtIssueTitle').value='';
	document.getElementById('txtIssueDescription').value='';
	$('#modalIssueLog').modal();
}

function submitIssue() {
	submitForm('issueForm','modalIssueMessage','modalIssueMessage',false,'txtIssueId',function(success) {
		if (success) {
			var issueId = document.getElementById('txtIssueId').value;
			var message = 'The issue has been successfully logged and has been assigned number ' + issueId +  '. Thanks for helping improve the FoodFinder platform!';
			setElementText('modalIssueMessage',message);
			hideElement('modalIssueSubmit');
			setElementText('modalIssueCancel','Close');
		}
	});
}
