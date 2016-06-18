window.onload = function()
{
	if ($(window).width()>=1024) {
		// show search panel for large screens (hidden for smaller)	
		showElement('searchform2');
		hideElement('showSearchBtn');	
	}
	
	
	
	initializeMap('resultSpan');
	hideElement('list-loader');     
	
};

/*$(window).scroll(function(){
		//log ('Window.scroll: lastScroll=' + lastScroll +', scrollTop=' + $(window).scrollTop() + ', height=' + $(window).height() + ', document height=' +  $(document).height());	    
			    
	    // make sure (from last Scroll) user is scrolling down, not up
	    if ($(window).scrollTop()>lastScroll && ($(window).scrollTop() + $(window).height() >= $(document).height() - 200)) {
	  		// load more content
	  		if (!offlineMode&&currentLatLong) {
	  			//log('Loading more (locationIndex=' + locationIndex + ')');
		  		showElement('list-loader');
	  			locationIndex+=numToLoad;
	  			loadLocations(currentLatLong.lat(),currentLatLong.lng(),'resultSpan',numToLoad,locationIndex);
	  		}
	  	}
	  	lastScroll = $(window).scrollTop();
	});
*/

$(function () {
  $('[data-toggle="tooltip"]').tooltip();
});

$(document).on('hidden.bs.modal', '.modal', function () {
    $('.modal:visible').length && $(document.body).addClass('modal-open');
});


// global map, markers, & geocoder since we will need to access across async functions
var map;
var geocoder;
var currentLatLong;
var locationIndex=0;
var numToLoad=10;
var infoWindow;
var lastMarker;
var lastScroll=0;
var offlineMode=false;
var zoomSetBy='none';

function initializeMap(anchor)
{
	showElement('loading');
	
	numToLoad = getElementValue('numToDisplay');
	zoom = parseInt(getElementValue('txtZoom'));
	if (!zoom || zoom==0) {
		zoom = 13;
	}
	else {
		zoomSetBy='user';
	}
	
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
		hideElement('list-loader');
		setMessage('Unable to initialize map. Verify you have an active Internet connection and try again.', 'message', 'message_text', false);
		loadLocations(33.856453, -79.80855799999999, anchor);
		return true;
	}

	// default to Mt. Pleasant SC
	currentLatLong = new google.maps.LatLng(33.856453, -79.80855799999999);
	
	var async = false;
	var mapOptions = {
		center: currentLatLong,
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
    	
	// set up autocomplete on address boxes
	if (google.maps.places) {
		auto_origin = new google.maps.places.Autocomplete(document.getElementById('txtAddress'),{ types: ['geocode']});
	}

	var userSetLatitude = document.getElementById('txtCurrentLatitude').value;
	var userSetLongitude = document.getElementById('txtCurrentLongitude').value;
	if (userSetLatitude!=0 && userSetLongitude !=0) {
		// user has previously set location via address search. Use that instead of default or actual location
		currentLatLong = new google.maps.LatLng(userSetLatitude, userSetLongitude);
	}
	else if(navigator.geolocation) {
		// see if we can get HTML5 geolocation from browser
		async = true;
		navigator.geolocation.getCurrentPosition(function(position) {
			var pos = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
			currentLatLong = pos;
			// save this so we don't geodetect on each page load
			postCurrentLocation(currentLatLong.lat(),currentLatLong.lng());
			map.setCenter(pos);
			var marker = dropMarker(map,pos,"Current Location","img/icons/arrow.png",0);
			map.setCenter(marker.getPosition());
			var contentString = '<div class="mapInfoWindow">Your current location.</div>';
			addInfoWindow(marker,contentString);
			loadLocations(pos.lat(),pos.lng(),anchor);
			}, function(err) {
				// error 
				hideElement('loading');
				hideElement('list-loader');
				setMessage('Unable to detect location from your browser.', 'message', 'message_text', false);
				log('geolocation.getCurrentPosition failed: ' + err.message);
			});	
	}
	
	map = new google.maps.Map(document.getElementById("mapcanvas"), mapOptions);

	if (!async) {
		// use either default or user set location
		map.setCenter(currentLatLong);
		var marker = dropMarker(map,currentLatLong,"Current Location (1)","img/icons/arrow.png",0);
		map.setCenter(marker.getPosition());
		var contentString = '<div class="mapInfoWindow">Your current location.</div>';
		addInfoWindow(marker,contentString);		
		loadLocations(currentLatLong.lat(),currentLatLong.lng(),anchor);
	}
	map.addListener('zoom_changed',mapZoomChanged);
}

function mapZoomChanged() {
	if (zoomSetBy=='script') {
		zoomSetBy='none';
	}
	else {
		// if we didn't set it, must be the user
		zoomSetBy = 'user';
	}
}

function setCurrentAddress(address,anchor)
{
	
	if (address && address.length>0)
	{
		showElement('loading');
		geocoder.geocode({'address': address}, function(results,status) {
			if (status ==  google.maps.GeocoderStatus.OK) {
				currentLatLong = results[0].geometry.location;
				var currentLat = currentLatLong.lat();
				var currentLng = currentLatLong.lng();
				map.setCenter(currentLatLong);
				marker = dropMarker(map,currentLatLong,"Your Location","img/icons/arrow.png",0);
				var contentString = '<div class="mapInfoWindow">Your selected location.</div>';
				addInfoWindow(marker,contentString);
				
				postCurrentLocation(currentLat,currentLng,address);
				
				loadLocations(currentLat,currentLng,anchor);										
								
				}
			else
				{
				hideElement('loading');
				setMessage('Couldn\'t find that location or address.', 'message', 'message_text', false);
				showElement('message');
				if ($('#showSearchBtn').hasClass('hidden')) {
					// we are in large screen mode
					}
				else {
					showElement('searchform2');
					showElement('hideSearchBtn');
					hideElement('showSearchBtn');
				}
				}
		});
		return true;	
	}
	else
	{
		//alert("Please enter a location or address.");
		setMessage('Please enter a location or address.', 'message', 'message_text', false);
		return false;
	}
}

function detectLocation(anchor) {
	log('Detecting location...');
	showElement('loading');
	if(navigator.geolocation) {
		// see if we can get HTML5 geolocation from browser
		asynch = true;
		navigator.geolocation.getCurrentPosition(function(position) {
			log('Location detected.');
			clearMarkers();
			var pos = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
			map.setCenter(pos);
			postCurrentLocation(pos.lat(),pos.lng(),'Your current location');
			marker = dropMarker(map,pos,"Current Location","img/icons/arrow.png",0);
			var contentString = '<div class="mapInfoWindow">Your current location.</div>';
			addInfoWindow(marker,contentString);
			currentLatLong = pos;
			loadLocations(pos.lat(),pos.lng(),anchor);
			document.getElementById('txtAddress').value='Your current location';
			hideElement('loading');				
			},
			function(err) {
				setMessage('Unable to get current location: ' + err.message, 'message', 'message_text', false);
				hideElement('loading');
			});
			
	}
}
		
function loadLocations(currentLat, currentLng, anchor, ret, start) {
	
		log('Loading locations (start=' + start + ')');
		if (!ret) {
			ret = numToLoad;
		}			
		
		if (!start) {
			start = 0;
		}

		var serviceURL = "service/service_proto.php";
		var working = "Retrieving results . . .";
		var filter=getCategoryFilter();
					
		xmlhttp=new XMLHttpRequest();
		xmlhttp.onreadystatechange=function() {
		  if (xmlhttp.readyState==4) {
		  	hideElement('loading');
		  	if (xmlhttp.status==200) {
			    var view = JSON.parse(xmlhttp.responseText);
				var farthestPointToShow;
				
				// clean up and enrich data
				for(var i=0; i<view.locations.length; i++) {
					var location=view.locations[i];
					
					//defaults for display
					if (!location.imageurl||location.imageurl=='') {
						view.locations[i].imageurl = 'img/placeholder.jpg';
					}
					view.locations[i].linkname = escapeSingleQuotes(view.locations[i].name);
					var shortdesc = location.shortdescription;
					var maxlen = 240; // characters
					if (shortdesc.length>maxlen) {
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
				
				// populate map and wire events on location DIVs
				if (!offlineMode) {
					for(var i=0; i<view.locations.length; i++) {
						var location=view.locations[i];
						var pos = new google.maps.LatLng(location.latitude, location.longitude);
						if (i<5) {
							// include closest five points on map
							farthestPointToShow = pos;
						}
						var icon=getIconForLocation(location);
						
						var locid=location.id;
						var locname=location.name;
						var marker=dropMarker(map,pos,locname,icon,locid);
						if (location.uservisits>0 && document.getElementById('chkMarkVisited').checked) {
							marker.icon = 'img/icons/visited.png';
							}
						
						setInfoWindow(map,marker,getInfoWindowContent(location),location);
						
						// this whole context thing is necessary due to javascript scope issue
						// without it, all loadLoaction functions bound as listener to marker would
						// invoke the locid and locname from the last iteration through this loop
						var context = {
							l: locid,
							n: locname,
							callback: function() {
								loadLocation(this.l,this.n);
							}
						};
						//google.maps.event.addListener(marker,'click',context.callback.bind(context));
						divid = 'loc' + locid;
						//console.log(divid);
						target = document.getElementById(divid);
						/*$('#' + divid).hover(function(e) {
							centerMap(e.currentTarget.id);
							});*/
					}
				}
				
				hideElement('list-loader');
				if (zoomSetBy!='user' && view.locations.length>0) {
					// only set map size if user hasn't changed the zoom level & we have at least one location
					zoomSetBy='script';
					resizeMap(map,farthestPointToShow);
				}
				displayLocationSummary(locationIndex);
				if (view.locations.length>0) {
					centerMap(locations[locationIndex].id);
				}
				//log('Locations loaded.');
				}
			else {
				setMessage('Unable to retrieve locations: the location service is not available.', 'message', 'message_text', false);
				}
		    }
		 };
		serviceURL += "?";
		serviceURL += "center_lat=" + currentLat;
		serviceURL += "&center_lng="+ currentLng;
		serviceURL += "&return=" + ret;
		serviceURL += "&start=" + start;
		if (filter.length>0) {
			serviceURL += "&categories=" + filter;
		}
		xmlhttp.open("GET",serviceURL,true);
		xmlhttp.send();
		}

function centerMap(id) {
	//log('centering triggered for ' + id);
	//id=id.substring(3);
	var marker = markers[id];
	if (marker && (!lastMarker || lastMarker.title!=marker.title)) {
		map.setCenter(marker.getPosition());
		if (lastMarker) {
			// for now, don't change color -- need to figure out what to do since we key restaurant by color
			//lastMarker.setIcon('http://maps.google.com/mapfiles/ms/icons/red-dot.png');
		}
		//marker.setIcon('http://maps.google.com/mapfiles/ms/icons/green-dot.png');
		
		// trigger info window.
		google.maps.event.trigger(marker,'mouseover');
		
		lastMarker=marker;
	}
	
}
		
function retrieveResults(addressID,anchor)
{
	clearMarkers();
	locations = [];
	locationIndex=0;
	hideElement('message');
	if ($('#hideSearchBtn').hasClass('hidden')) {
		// we are in large screen mode
	}
	else {
		hideElement('searchform2');
		hideElement('hideSearchBtn');
		showElement('showSearchBtn');
	}
	var address=document.getElementById(addressID).value;
	setCurrentAddress(address,anchor);
	
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
			
			// set location summary & current location index
			var template = getLocationSummaryTemplate();
			renderTemplate(template,location,'resultSpan',false);
			for (var i=0;i<locations.length;i++) {
				if (locations[i].id == location.id) {
					locationIndex=i;
					break;
				}
			}		
			});
}


function applyNewSettings() {
	
	// reload location list
	clearMarkers();
	locations = [];
	locationIndex=0;
	var pos = currentLatLong;
	marker = dropMarker(map,pos,"Current Location","img/icons/arrow.png",0);
	numToLoad = document.getElementById('numToDisplay').value;
	loadLocations(pos.lat(),pos.lng(),'mapcontent');
	
}

function loadPrevLocation() {
	if (locationIndex>0) {
		locationIndex--;
		log('loading location ' + locationIndex);
		displayLocationSummary(locationIndex);
		centerMap(locations[locationIndex].id);
	}
}

function loadNextLocation() {
	locationIndex++;
	log('loading location ' + locationIndex);
	if (locationIndex>=(locations.length)) {
		loadLocations(currentLatLong.lat(),currentLatLong.lng(),'resultSpan',numToLoad,locationIndex+1);
	}
	else {
		displayLocationSummary(locationIndex);
		centerMap(locations[locationIndex].id);
		}	
}

function displayLocationSummary(index) {
	if (locations[index]) {
		var template = getLocationSummaryTemplate();
		renderTemplate(template,locations[index],'resultSpan',false);
	}
	else {
		setElementHTML('resultSpan','<div class="thumbnail loc-panel"><p>No location found.</p></div>');
	}
		
}

function showSearchForm() {
	showElement('searchform2');
}


function expandMap() {
	var targetHeight = $(window).height()-$('#topPart').height()-$('#listNav').height()-$('#footer').height();
	if (targetHeight<200) {
		targetHeight = 200;
	}
	$('#mapwrapper').height(targetHeight);
	$('#mapcanvas').height(targetHeight);
	hideElement('expandMap');
	showElement('shrinkMap');

}

function shrinkMap() {
	$('#mapwrapper').height(200);
	$('#mapcanvas').height(200);
	showElement('expandMap');
	hideElement('shrinkMap');
}