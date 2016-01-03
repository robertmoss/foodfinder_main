window.onload = function()
{
	initializeMap('resultSpan');
	$('#formPanel').on('hide.bs.collapse',function() {
		toggleForm('show'); 
		});
	$('#formPanel').on('show.bs.collapse',function() {
		toggleForm('hide'); 
		});
		
	$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
		// having repaint issues when switch tabs, so force resize when mapwrapper tab activated
		if (e.target && e.target.hash=='#mapwrapper') {
			if (map) {
				google.maps.event.trigger(map,'resize');
			}
		}
		});	
};

// global map, markers, & geocoder since we will need to access across async functions
var map;
var geocoder;
var directionsDisplay;
var currentLatLong;
var auto_origin;
var auto_dest;
var currentMarker;
var waypoints = [];
var waypoints_info = [];
var routeOrigin;
var routeDestination;
var infoWindow;
var offlineMode = false;


function initializeMap(anchor)
{
	var bOffline = false;
	showElement('loading'); 
	try {
		geocoder = new google.maps.Geocoder();
	}
	catch(ex) {
		log('unable to create google Geocoder - likely offline');
		// swtich to offline mode
		offlineMode = true;
	}
	
	if (offlineMode) {
		hideElement('loading');
		// do anything else?
		testOffline();
		return true;
	}
	
	// default to Mt. Pleasant SC
	currentLatLong = new google.maps.LatLng(33.856453, -79.80855799999999);
	var async = false;
	var mapOptions = {
		center: currentLatLong,
		zoom: 13,
		mapTypeControl: false
	};
	map = new google.maps.Map(document.getElementById("mapcanvas"), mapOptions);
	
	var userSetLatitude = document.getElementById('txtCurrentLatitude').value;
	var userSetLongitude = document.getElementById('txtCurrentLongitude').value;
	if (userSetLatitude!=0 && userSetLongitude !=0) {
		// user has previously set location via address search. Use that instead of default or actual location
		currentLatLong = new google.maps.LatLng(userSetLatitude, userSetLongitude);
	}
	/* -- for now, don't auto locate. Let user click button to use current loc.
	 else if(navigator.geolocation) {
		// see if we can get HTML5 geolocation from browser
		async = true;
		navigator.geolocation.getCurrentPosition(function(position) {
			var pos = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
			map.setCenter(pos);
			currentMarker = dropMarker(map,pos,"Current Location","img/icons/arrow.png");
			var contentString = '<div class="mapInfoWindow">Your current location.</div>';
			addInfoWindow(currentMarker,contentString);
			setElementValue('txtOrigin','Current Location');
			setElementValue('txtCurrentLatitude',pos.lat());
			setElementValue('txtCurrentLongitude',pos.lng());
			hideElement('loading');
			}, function(err) {
				log('unable to get current position:' + err.message);
				hideElement('loading');
			});	
	}*/
	
	if (!async) {
		map.setCenter(currentLatLong);
		//currentMarker = dropMarker(map,currentLatLong,"Default starting location");
		//var contentString = '<div class="mapInfoWindow">Default starting location</div>';
		//addInfoWindow(currentMarker,contentString);		
		hideElement('loading');
	}
	
	// set up directions display
	directionsDisplay = new google.maps.DirectionsRenderer();
	directionsDisplay.setMap(map);
	
	// set up autocomplete on address boxes
	auto_origin = new google.maps.places.Autocomplete(document.getElementById('txtOrigin'),{ types: ['geocode']});
	google.maps.event.addListener(auto_origin, 'place_changed', function() {
    	populateAddress('txtOrigin');});
    auto_dest = new google.maps.places.Autocomplete(document.getElementById('txtDestination'),{ types: ['geocode']});
	google.maps.event.addListener(auto_dest, 'place_changed', function() {
    	populateAddress('txtDestination');});	
    	
}

function populateAddress(control) {
	var place = auto_origin.getPlace();
	// don't really have to do anything if you just want auto-pop		
}

function detectLocation(anchor) {
	showElement('loading'); 
	if(navigator.geolocation) {
		// see if we can get HTML5 geolocation from browser
		asynch = true;
		navigator.geolocation.getCurrentPosition(function(position) {
			var pos = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
			map.setCenter(pos);
			//postCurrentLocation(pos.lat(),pos.lng());
			
			// remove current marker if it exists
			if (currentMarker) {
				currentMarker.setMap(null);
			}
			currentMarker = dropMarker(map,pos,"Current Location 3","img/icons/arrow.png");
			var contentString = '<div class="mapInfoWindow">Your current location 3.</div>';
			addInfoWindow(currentMarker,contentString);
			setElementValue('txtOrigin','Current Location');
			setElementValue('txtCurrentLatitude',pos.lat());
			setElementValue('txtCurrentLongitude',pos.lng());
			hideElement('loading'); 
			},
			function(err) {
				setMessage('Unable to get current location: ' + err.message, 'message', 'message_text', false);
				hideElement('loading'); 
			});			
	}
}

function getRoute() {	

		hideElement('message');
		showElement('loading'); 

		var origin = getElementValue('txtOrigin');		
		var destination = getElementValue('txtDestination');
		var detour = getElementValue('txtDetour');
		var filter=getCategoryFilter();
		var apiKey = 'AIzaSyB9Zbt86U4kbMR534s7_gtQbx-0tMdL0QA';
		
		// validate inputs
		if (origin=='') {
			setMessage('Please specify an origin.');
			return false;
		}
		if (destination=='') {
			setMessage('Please specify a destination.');
			return false;
		}
		if (detour==''||detour<1) {
			setMessage('A detour distance must be specified.');
			return false;
		}
		
		if (origin=='Current Location') {
			// use longiture & latitude for current location as set on hidden fields
			origin = getElementValue('txtCurrentLatitude') + "," + getElementValue('txtCurrentLongitude');
		}
		
		
		var directionsService = new google.maps.DirectionsService();
		var request = {
			origin: origin,
			destination: destination,
			travelMode: google.maps.TravelMode.DRIVING // in future consider setting dynamically?
		};
		directionsService.route(request,function(result,status) {
			if (status==google.maps.DirectionsStatus.OK) {
				directionsDisplay.setOptions({suppressMarkers: false, preserveViewport: false});
				directionsDisplay.setDirections(result);
				setMapHeader(result.routes[0]);
				var route = result.routes[0].legs[0];
				var originLat = route.start_location.lat();
				var originLong = route.start_location.lng();
				var destLat = route.end_location.lat();
				var destLong = route.end_location.lng();
				
				// build array of points to represent the route (for more fine grained location matching)
				var points = [];
				points.push({lat: originLat,lng: originLong});
				for (var i=0;i<route.steps.length;i++) {
					var point = {lat: route.steps[i].end_location.lat(), lng: route.steps[i].end_location.lng()};
					points.push(point);
				}
				
				processDirections(result.routes[0]);

				// sock these away in case we need to reRender map
				routeOrigin = originLat + ',' + originLong;
				routeDestination = destLat + ',' + destLong;
	
				//processRoute(originLat,originLong,destLat,destLong,detour,filter);
				
				processRouteByPoints(points,detour,filter); 
			}
			else {
				setMessage('Unable to retrieve route:' + status);
				hideElement('loading'); 
				}
		});
		
		// set form elements
		$('#formPanel').collapse('hide');		

		
	}

function testOffline() {
	// dummy up values to test service
	var detour = 25;
	var originLat = 35.621086;
	var originLong = -77.417259;
	var destLat = 35.992722;
	var destLong = -78.903938;
	
 	processRoute(originLat,originLong,destLat,destLong,detour);
}

function processRoute(originLat,originLong,destLat,destLong,detour,filter) {
	

	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	
	xmlhttp.onreadystatechange=function() {
		  if (xmlhttp.readyState==4) {
		  	if (xmlhttp.status==200) {
			    set = JSON.parse(xmlhttp.responseText);
			    locations = set.locations; // save for later use
				renderRoute(set);
		    }
		    else {
		    	setMessage('Unable to retrieve route:' + xmlhttp.responseText);
		    }
			hideElement('loading');

		   }
		 };
		 
		serviceURL = "service/route.php?";
		serviceURL += "origin=" + originLat +',' + originLong;
		serviceURL += "&destination=" + destLat +',' + destLong;
		serviceURL += "&maxDetour=" + detour;
		serviceURL += "&return=" + 50;
		if (filter) {
			serviceURL += "&categories=" + filter;
		}
		xmlhttp.open("GET",serviceURL,true);
		xmlhttp.send();
			
}

function processRouteByPoints(points,detour,filter) {

	// need to update to pluck return from user config dialog
	var data = {maxDetour: detour, categories: filter, return: 100, points: points};
	var serviceUrl = "service/route.php";

	var request = new XMLHttpRequest();
	request.open("POST",serviceUrl,true);
	request.setRequestHeader('Content-Type','application/json; charset=UTF8');
	request.onreadystatechange=function() {
		 if (request.readyState==4) {
		 	if (request.status==200) {
		 		if (request.responseText.length>0) {
			 		var set = JSON.parse(request.responseText);
			 		locations = set.locations; // save for later use
					renderRoute(set);
				   }
		    	}
		   	else {
		   		// do something with the error
				setMessage('Unable to retrieve route:' + request.responseText);
		   	}
			hideElement('loading');
		  }  
		};
	request.send(JSON.stringify(data));
						
}

function renderRoute(set) {
	
	// first, render list panel
	var template = getLocationListTemplate();
	
	renderTemplate(template,set,'locationList');
	
	// next, add markers to map
	clearCollections();
	//var contentString = '<p>Location to be set dynamically</p>';
	//var infoWindow = new google.maps.InfoWindow({content: contentString});
	var lastLat=0;
	var lastLong=0;
	for(var i=0; i<set.locations.length; i++) {
		var location=set.locations[i];
		if (location.latitude==lastLat && location.longitude==lastLong) {
			// in case there are dupes in database, don't add to map
			log('Duplicate location detected:' + location.id + '-' + location.name);
		}
		else {
			if (!offlineMode) {
				var pos = new google.maps.LatLng(location.latitude, location.longitude);
				var icon=getIconForLocation(location);
				var marker=dropMarker(map,pos,location.name,icon);
				markers.push(marker);
				setInfoWindow(marker, location);
				}	
			}
		lastLat=location.latitude;
		lastLong=location.longitude;						
		}
	//resizeMap(map,farthestPointToShow);
	
}

function setInfoWindow(marker, loc) {

		var flag = '';
		if (loc.status=="Closed"||loc.status=="Temporarily Closed") {
			flag += '<div class="flag warning"><span class="flag warning"><span class="glyphicon glyphicon-alert" aria-hidden="true"></span> ' + loc.status +  '</span></div>';
		}
		if (loc.top_category) {
			flag += '<div><span class="flag">' + loc.top_category +  '</span></div>';
		}
		var contentString = '<div class="mapInfoWindow">' + 
			'<div class="name"><a href="#locationModal" onclick="loadLocation(' + loc.id + ');">' + loc.name + '</a></div>' +
			flag +
			'<div class="location">' + loc.city + ' ' + loc.state + '</div>';

		if (loc.selected>0) {
			contentString +='<div class="select"><input type="button" value="Remove from Route" onclick="removeFromRoute(' + loc.id +');"></div>';
		}
		else {
			contentString +='<div class="select"><input type="button" value="Add to Route" onclick="addToRoute(' + loc.id +');"></div>';
		}
		contentString +='</div>';

		addInfoWindowOnClick(map,marker,contentString);
	
}

function processDirections(route) {
		var legs=route.legs;
		var markup = '<div>';
		
		for (var i=0;i<legs.length;i++) {

			//markup+='<div class=\"directions-start\">'+legs[i].start_address + '</div>';
			
			markup+='<div class=\"panel panel-default\">';
			markup+='  <div class=\"panel-heading\" role=\"tab\" id=\"heading' + (i+1) + '\">';
			markup+='    <h4 class=\"panel-title\">';
			markup+='       <a role=\"button\" data-toggle=\"collapse\" data-parent="#directions-accordion" href="#collapse' + (i+1) + '\" aria-expanded=\"true\" aria-controls=\"collapse' + (i+1) + '\">';
			markup+=         getWaypointName(i-1,route.waypoint_order, legs[i].start_address) + ' to ' + getWaypointName(i, route.waypoint_order,legs[i].end_address);
			markup+='      </a>';       
			markup+='    </h4>';
			markup+='    <p><b>Distance:</b> ' +legs[i].distance.text + '<br/> <b>Duration:</b> ' + legs[i].duration.text + '</p>';
			markup+='  </div>';
			markup+='  <div id=\"collapse' + (i+1) + '\" class=\"panel-collapse collapse';
			/*if (i==0) {
				markup += ' in'; // expand only first set
				}*/ 
			markup+= '\" data-parent=\"directions-accordion\">';
			markup+='    <div class=\"panel-body\">';
			
			for (var j=0;j<legs[i].steps.length;j++) {
				var step=legs[i].steps[j];
				markup+='<p>';
				var glyph='';
				switch(step.maneuver) {
					case 'turn-left':
					case 'roundabout-left':
						glyph='glyphicon glyphicon-circle-arrow-left';
						break;
					case 'turn-right':
					case 'roundabout-right':
						glyph='glyphicon glyphicon-circle-arrow-right';
						break;
				}
				if (glyph.length>0) {
					markup+='<span class=\"' + glyph + '\"></span> ';
				}
				markup+= step.instructions + ' (' + step.distance.text +'-' + step.duration.text +	 ')</p>';
			}
			//markup+='<div class=\"directions-end\">'+legs[i].end_address + '</div>';
			markup+='    </div>'; //end panel-body
			markup+='  </div>'; //end panel-collapse
			markup+='</div>';
		}
		
		 markup += '</div>';
		setElementHTML('directionsZone',markup);
}

function getWaypointName(index, waypointorder,defaultName) {
	// challenge is that waypoints can come back in different order than they are added to collection
	// use route's waypointorder collection to map

	var name=defaultName;
	var currentWaypoint = -1;
	if (waypointorder && waypointorder.length>0) {
		currentWaypoint = waypointorder[index];
	}

	if (currentWaypoint>=0) {
		name=waypoints_info[currentWaypoint].name;
	} 
	
	return name;
}

function clearCollections() {
	clearMarkers();
	waypoints = [];
	waypoints_info = [];
}

function setWindow(location,infoWindow,marker) {
	
	// build content string for infoWindow
	var contentString = '<b>' + location.name + '</b>';
	infoWindow.content = contentString;
	infoWindow.open(map,marker);
}

function addToRoute(id) {
	
	var index=findLocationIndex(id);	
	if (index<0) {
		return false;
	}
	else {
		// add a waypoint for selected location
		waypoints.push({
					location: locations[index].latitude +',' + locations[index].longitude,
					stopover: true
				}
			);
		locations[index].waypointIndex = waypoints.length - 1; 
		locations[index].selected=1;
		waypoints_info.push({
			name: locations[index].name,
			latitude: locations[index].latitude,
			longitude: locations[index].longitude 	
		});
		var icon = "img/icons/green-dot.png";
		setMarkerIcon(index,icon);
		reRenderMap();
	}
	infoWindow.close();
}

function removeFromRoute(id) {
	
	var index=findLocationIndex(id);	
	if (index<0) {
		return false;
	}
	else {
		// remove waypoint for selected location
		// not reliable to grab off lat/long
		/* for(var i=0;i<waypoints.length;i++) {
			var arr = waypoints[i].location.split(',');
			var pos = new google.maps.LatLng(arr[0], arr[1]);
			var pos2 = new google.maps.LatLng(locations[index].latitude, locations[index].longitude);
			if (pos2.equals(pos)) {
				waypoints.splice(i,1);
				break;
				}
			}
		*/	
		waypoints.splice(locations[index].waypointIndex,1);
		waypoints_info.splice(locations[index].waypointIndex,1);
			
		var icon = "img/icons/ltblue-dot.png";
		locations[index].selected=0;
		setMarkerIcon(index,icon);
		reRenderMap();
		infoWindow.close();
	}
}

function findLocationIndex(id) {

	var index=-1;
	for(var i=0;i<locations.length;i++) {
		if (locations[i].id==id) {
			index=i;
			break;
		}
	}
	return index;
}

function setMarkerIcon(index,icon) {
		// find marker and change its color
		var marker;
		for(var i=0;i<markers.length;i++) {
			var pos = markers[i].getPosition();
			var pos2 = new google.maps.LatLng(locations[index].latitude, locations[index].longitude);
			if (pos.equals(pos2)) {
				// we got the marker
				marker = markers[i];
				break;
			}
		}
		if (marker)
			{
			marker.setIcon(icon);
			var loc = locations[index];
			setInfoWindow(marker, loc);
			}
}

function setMapHeader(route) {
	var distance=0;
	var duration=0;
	var origin = getElementValue('txtOrigin');		
	var destination = getElementValue('txtDestination');

	for(var i=0;i<route.legs.length;i++) {
		 distance += route.legs[i].distance.value/1609.34; // convert distance from meters to miles
		 duration += route.legs[i].duration.value;
	}
	var markup = 'From <span class=\"origin\">' + origin + '</span> to <span class=\"destination\">' + destination + '</span>.'; 
	markup += ' Total distance: ' + distance.toFixed(1) + ' miles';
	markup += ' Total duration: ' + formatDuration(duration) + ' ';
	setElementHTML('tripSummaryText',markup);
}

function reRenderMap () {
	
	var directionsService = new google.maps.DirectionsService();
	var request = {
		origin: routeOrigin,
		destination: routeDestination,
		travelMode: google.maps.TravelMode.DRIVING, // in future consider setting dynamically?
		waypoints: waypoints,
		optimizeWaypoints: true
		};
		directionsService.route(request,function(result,status) {
			if (status==google.maps.DirectionsStatus.OK) {
				directionsDisplay.setOptions({suppressMarkers: true, preserveViewport: true});
				directionsDisplay.setDirections(result);
				var route = result.routes[0];
				setMapHeader(route);
				processDirections(route);
			}
			
		});
}

function getLocationListTemplate() {
	var template = "{{#locations}}"; 
		template += "<div class=\"panel\"><div class=\"inner\"><div class=\"figure\"><img src=\"{{imageurl}}\"/></div><div class=\"bd\">";
		template += "<div class=\"prime\"><h2>{{name}}</h2></div>";
		template += "<div class=\"secondary\"><p>{{address}}</p><p>{{city}}, {{state}}</p><p>{{phone}}</p><p><a href=\"{{url}}\" target=\"_blank\">{{url}}</a></p></div></div>";
		template += "<div class=\"description\"><span class=\"description\">{{shortdescription}}</span></div></div></div>";
		template += "{{/locations}}";
		
	return template;
}


function hideRouteSummary() {
			
		showElement('formPanel');
		hideElement('routeSummary');
		
		// scroll to form panes
		location.href = "#";
		location.href = "#searchform";
}

function showRouteSummary() {
			
		hideElement('formPanel');
		showElement('routeSummary');
		
		// scroll to form panes
		location.href = "#";
		location.href = "#searchform";
}


function closeLocation() {
	hideElement('locationModal');
	$('body').css('overflow','auto');
}	

function toggleForm(mode) {
	var link = document.getElementById("toggleLink");
	if (mode == 'show') { 
		link.innerHTML = "<span class=\"glyphicon glyphicon-chevron-down\" aria-hidden=\"true\"></span> Show";
	}
	else {
		link.innerHTML = "<span class=\"glyphicon glyphicon-chevron-up\" aria-hidden=\"true\"></span> Hide";
	}
	return true;

}

function editLocation() {
	var id = document.getElementById('locationid').innerHTML;
	var url = 'entityPage.php?type=location&id=' + id + '&mode=edit';
	window.open(url,'locationedit');
	
}

function formatDuration(duration) {
	var hours = Math.floor(duration/3600);
	var minutes = Math.round((duration/60) % 60);
	return hours + ' hr ' + minutes + ' min';
}

function applyNewSettings() {
	getRoute();
}
