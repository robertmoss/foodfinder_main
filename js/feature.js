var firstID;
var lastID;

function afterFeatureEdit(success) {
	$('#featureEditModal').modal('hide');
	if (success) {
		location.reload();
	}
}

function loadFeatureLocations() {
	
	console.log('Loading locations.');
	showElement('workingPanel');
	// for now, assume we have just one location template (the default feature one)
	var serviceUrl = "core/service/entitiesService.php?type=location&sequence=true";
	var criteria = getElementValue('locationCriteria');
	if (criteria.length>0) {
		serviceUrl+='&' + criteria;
	}

	getJSON(serviceUrl,'locationAnchor',null,locationsLoaded,locationsLoadError);
}

function locationsLoaded(locations) {

	var ret = getElementValue('maxLocations');
	var numberEntries = getElementValue('numberEntries');

	var template = getDefaultFeatureTemplate(ret<=20,numberEntries>0);
	renderTemplate(template,locations,'locationAnchor');
	//console.log('Locations loaded: ' + key);

	if (locations.count==0) {
		setElementText('locationAnchor','No locations found.');
	}
	else {
		//set first location as active
		$(".locationCarouselItem").first().addClass("active");
		$(".locationCarouselIndicator").first().addClass("active");
		$(".locationInfo").first().removeClass("hidden");
		
		firstID=locations.locations[0].id;
		lastID=locations.locations[locations.count-1].id;
		
		// wire up carousel events
		$('#location-carousel').on('slide.bs.carousel', function (e) {
			// hide all location info panes
		  	$(".locationInfo").addClass("hidden");
		  	
		  	if (e.direction=="right" && e.relatedTarget.id==('location')+lastID)  {
		  		hideElement('locationAnchor');
		  		showElement('openingContent');
		  		showElement('subhead');
		  		showElement('coverImage');
	  	}
		  	else if (e.direction=="left" && e.relatedTarget.id==('location')+firstID)  {
		  		hideElement('locationAnchor');
		  		showElement('closingContent');
		  	}
		  	else {
			  	// show selected location info pane
			  	$("#" + e.relatedTarget.id + "Info").removeClass("hidden");
			  }
		});
		
	}
	hideElement('workingPanel');
}

function locationsLoadError(msg) {
	hideElement('workingPanel');
	setElementText('locationAnchor','Unable to load locations: ' + msg);
}

function launchSlideshow() {
	showElement('locationAnchor');
	hideElement('openingContent');
	hideElement('subhead');
	hideElement('closingContent');
	hideElement('coverImage');
	window.scrollTo(0,0);
	loadFeatureLocations();
}

function getDefaultFeatureTemplate(showIndicators,numberEntries) {
	
	//var template='{{#locations}}<hr/><h3>{{name}}</h3><p>{{address}}<br/>{{city}} {{state}}</p>{{#shortdesc}}<p>{{shortdesc}}</p>{{/shortdesc}}{{/locations}}';
	
	var listText = document.getElementById('txtList').value;
	listText += '&location={{id}}';
	var buttonLink = '<div><a class="social icon" href="finder.php?' + listText + '" target="_blank" title="View on Map" aria-label="View on Map"><button class="btn btn-default"><span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span> View on Map</button></a></div>';
	
	var template = '<div id="location-carousel" class="carousel slide" data-ride="carousel" data-interval="false">';
	
	if (showIndicators) {
		template += '<!-- Indicators -->';
  		template += '<ol class="carousel-indicators">';
    	template += '{{#locations}}   <li class="locationCarouselIndicator" data-target="#location-carousel" data-slide-to="{{sequence_zero}}"></li>{{/locations}}';
  		template += '</ol>';
  		}	
	template += '<div class="carousel-inner" role="listbox">';
	template += '{{#locations}}<div id="location{{id}}" class="locationCarouselItem item">';
	template += '<img src="{{imageurl}}{{^imageurl}}img/placeholder.jpg{{/imageurl}}" alt="{{name}}" />';
    template += '<div class="carousel-caption"><h3>';
    if (numberEntries) {
    	template+='#{{sequence}}. ';
    }
    template += '{{name}}</h3><p>{{city}} {{state}}</p></div>';
	template += ' </div>{{/locations}}';
	
	template += '<!-- Controls -->';
  	template += '<a class="left carousel-control" href="#location-carousel" role="button" data-slide="prev">';
    template += '   <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>';
    template += '   <span class="sr-only">Previous</span>';
  	template += '</a>';
  	template += '<a class="right carousel-control" href="#location-carousel" role="button" data-slide="next">';
    template += '   <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>';
    template += '   <span class="sr-only">Next</span>';
  	template += '</a>';
	template += '</div>';
	template += '</div>{{#locations}}<div id="location{{id}}Info" class="locationInfo hidden"><h3>';
	if (numberEntries) {
    	template+='#{{sequence}}. ';
    }
	template += '{{name}}</h3>';
	template += '<p>{{shortdesc}}</p>';
	template += buttonLink;
	template += '<p><address>{{address}}<br/>{{city}}, {{state}}<br/><a href="tel:{{clickablephone}}">{{phone}}</a><br/><a href="{{url}}" target="_blank">{{displayurl}}</a></address></div>{{/locations}}';
	
	return template;
	
}
