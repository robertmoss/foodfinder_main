window.onload = function()
{
	setHoverEvents(); // call to content.js function to enable content control events
	initializeMap('resultSpan');
	hideElement('list-loader');
	
};

var map;
var infoWindow;
var lastMarker;

function initializeMap(anchor)
{
	showElement('loading');
	
	var offlineMode=false;
	numToLoad = getElementValue('numToDisplay');
	zoom = parseInt(getElementValue('mapSettingZoom'));

	if (!zoom || zoom==0) {
		zoom = 13;
	}
	centerString = getElementValue('mapSettingCenter');
	if (!centerString) {
		centerLatLong = new google.maps.LatLng(33.856453, -79.80855799999999);
	}
	else {
		center = centerString.split(",");
		centerLatLong = new google.maps.LatLng(center[0],center[1]);
	}
	var async = false;
	var mapOptions = {
		center: centerLatLong,
		zoom: zoom,
		mapTypeControl: false,
    	panControlOptions: {
    	    position: google.maps.ControlPosition.TOP_RIGHT,
	        style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
	      },
	      zoomControlOptions: {
    	    position: google.maps.ControlPosition.TOP_RIGHT,
	        style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
	       }
	        
    	};
    	
	map = new google.maps.Map(document.getElementById("mapcanvas"), mapOptions);
	map.setCenter(centerLatLong);
	loadLocations(centerLatLong.lat(),centerLatLong.lng(),anchor);
	//map.addListener('zoom_changed',mapZoomChanged);
}

function loadLocations(latitude,longitude,anchor) {
	
		var working = "Retrieving results . . .";
		var filter = getElementValue('mapFilterString');
		var serviceURL = getCoreServiceUrl() + "/entitiesService.php?type=location";

		var listId = getElementValue('txtList');
		if (listId>0) {
			serviceURL += "&list=" + listId;
		}
		if (filter.length>0) {
			serviceURL += "&" + filter;
		}
				
		xmlhttp=new XMLHttpRequest();
		xmlhttp.onreadystatechange=function() {
		  if (xmlhttp.readyState==4) {
		  	if (xmlhttp.status==200) {
			    var view = JSON.parse(xmlhttp.responseText);
				
				// clean up and enrich data
				if (view.locations.length==0) {
					// none found
					locationIndex--;
				}
				
				for(var i=0; i<view.locations.length; i++) {
					var location=view.locations[i];

					//defaults for display
					if (!location.imageurl||location.imageurl=='') {
						view.locations[i].imageurl = 'img/placeholder.jpg';
					}
					view.locations[i].linkname = escapeSingleQuotes(view.locations[i].name);
					var shortdesc = location.shortdescription;
					if (!shortdesc) {
						shortdesc = location.shortdesc;
					}
					var maxlen = 240; // characters
					if (shortdesc && shortdesc.length>maxlen) {
						view.locations[i].shortdescription = shortdesc.substring(0,maxlen) + "... ";
						view.locations[i].more = true;	
						}
					}
				
				// save for later use	
				if (locations) {
					locations = locations.concat(view.locations); 
				}
				else {
					locations = view.locations;
				}
				
				// populate map
				var bounds = new google.maps.LatLngBounds(); 
				for(var i=0; i<view.locations.length; i++) {
					var location=view.locations[i];
					var pos = new google.maps.LatLng(location.latitude, location.longitude);
					var icon=getIconForLocation(location);
					var locid=location.id;
					var locname=location.name;
					var marker=dropMarker(map,pos,locname,icon,locid);
					bounds.extend(marker.getPosition());
					if (location.uservisits>0 && document.getElementById('chkMarkVisited').checked) {
						marker.icon = 'img/icons/visited.png';
						}
					
					setInfoWindow(map,marker,getInfoWindowContent(location),location);						
					}
				map.fitBounds(bounds);
				} // end status = 200
			else {
				setMessage('Unable to retrieve locations: the location service is not available.', 'message', 'message_text', false);
				}
		   hideElement('loading');
		   } // if readyState=4
		   
		 }; // end onreadystatechange function
		
		xmlhttp.open("GET",serviceURL,true);
		xmlhttp.send();
		hideElement('loading');
	}     


function setInfoWindow(map, marker, contentString, location) {
		
		var id=location.id;
		google.maps.event.addListener(marker,'mouseover',function() {
			if (infoWindow) {
				// close window if open
				infoWindow.close();
			}
			if (lastMarker) {
				// need to figure out what to do with setting color, since we now key restaurants by category
				//lastMarker.setIcon('http://maps.google.com/mapfiles/ms/icons/red-dot.png');
				}
			//marker.setIcon('http://maps.google.com/mapfiles/ms/icons/green-dot.png');
			lastMarker=marker;		
			infoWindow = new google.maps.InfoWindow({content: contentString});
			infoWindow.open(map,marker);			
			});
}


function mapSettings() {
	document.getElementById('txtMapSettingZoom').value = document.getElementById('mapSettingZoom').value; 
	document.getElementById('txtMapSettingCenter').value = document.getElementById('mapSettingCenter').value;
	document.getElementById('txtMapFilterString').value = document.getElementById('mapFilterString').value;
	hideElement('mapSettings-message');
	$('#mapSettingsModal').modal();
}

function useCurrent() {
	document.getElementById('txtMapSettingZoom').value = map.zoom;
	var latLangString = map.center.lat() + ',' + map.center.lng();
	document.getElementById('txtMapSettingCenter').value = latLangString;
}

function saveMapSettings() {
	
	// write settings to hidden controls
	document.getElementById('mapSettingZoom').value = document.getElementById('txtMapSettingZoom').value; 
	document.getElementById('mapSettingCenter').value = document.getElementById('txtMapSettingCenter').value;
	document.getElementById('mapFilterString').value = document.getElementById('txtMapFilterString').value;

	var bagName = getElementValue('mapSettingPropertyBagName');

	var properties = {
		mapSettingCenter: document.getElementById('txtMapSettingCenter').value,
		mapSettingZoom: document.getElementById('txtMapSettingZoom').value,
		mapFilterString: document.getElementById('txtMapFilterString').value
	};
	
	savePropertyBag(bagName,properties,'mapSettings-message','mapSettings-message_text',bagSaved);	 
}

function bagSaved(success) {
	if (success) {
	 	$('#mapSettingsModal').modal('hide');
	 	initializeMap();	
	}
}
