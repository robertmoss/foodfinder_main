var map;
var service; 

window.onload = function() {
	editMode = document.getElementById('editMode').value;
	if (editMode=='view')
	{
		locationID = document.getElementById('id').value;
		loadLocation(locationID, 'location_anchor');
		
		$('#locationForm').validator({disable: true});
		
	}
};



function geocodeAddress()
{
	var address = document.getElementById('txtAddress').value;
	address += ' ' + document.getElementById('txtCity').value;
	address += ' ' + document.getElementById('txtState').value;
	if (address && address.length>0)
	{
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode({'address': address}, function(results,status) {
			if (status ==  google.maps.GeocoderStatus.OK) {
				var currentPos = results[0].geometry.location;										
				document.getElementById('txtLatitude').value=currentPos.lat();
				document.getElementById('txtLongitude').value=currentPos.lng();
				}
			else
				{
					alert('Couldn\'t find that location or address.');
				}
		});
		return true;	
	}
	else
	{
		alert("Please enter an address.");
		return false;
	}
}

function setMode(mode)
{
	if (mode=='edit')
		{
		var newURL = document.URL + '&mode=edit';
		window.location = newURL;
		}
	else if(mode=='view') {
		var id = document.getElementById("id").value;
		var newURL;
		if (id==0) {
			newURL = "index.php";
		}
		else {
			var newURL = window.location.href.split('?')[0] + "?id=" + id;
		}
		window.location = newURL;		
	}

}

function addNew() {
	var newURL = window.location.href.split('?')[0] + "?id=0&mode=edit";
	window.location = newURL;
}

function loadImagePreview() {
	var imageURL = document.getElementById("imageURL").value;
	document.getElementById("imagepreview").src=imageURL;
}

function visitURL() {
	var URL = document.getElementById("txtURL").value;
	window.open(URL,'_blank');
}

function checkGooglePlaces() {
	var name = document.getElementById('txtName').value;
	var latitude = document.getElementById('txtCurrentLatitude').value;
	var longitude = document.getElementById('txtCurrentLongitude').value;
	if (!name || name.length==0) {
		alert('Please enter a name to search for.');
	}
	else {
		var URL = 'https://maps.googleapis.com/maps/api/place/textsearch/json?key=AIzaSyB9Zbt86U4kbMR534s7_gtQbx-0tMdL0QA';
		//URL += '&location=' + latitude + ',' + longitude + '&radius=50000'
		URL += '&query=' + name + '&types=food&sensor=false';
		//window.location = URL;
		
		/*var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange=function() {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200) {
		  	var response = JSON.parse(xmlhttp.responseText);
		  	for(var i=0; i<response.results.length; i++) {
		  		var location = response.results[i];
		  		alert(location);
		  		}
			}
		};
		xmlhttp.open("GET",URL,true);
		xmlhttp.send();
		*/
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
	
	document.getElementById('txtName').value = place.name;
    var addressInfo = place.formatted_address.split(','); 
	document.getElementById('txtAddress').value = addressInfo[0].trim();
    document.getElementById('txtCity').value = addressInfo[1].trim();
    document.getElementById('txtState').value = addressInfo[2].trim();
    
    document.getElementById('txtPhone').value = place.formatted_phone_number;
    document.getElementById('txtURL').value = place.website;
    
    document.getElementById('txtLatitude').value = place.geometry.location.lat();
    document.getElementById('txtLongitude').value = place.geometry.location.lng();
    
    document.getElementById('txtGoogleReference').value = place.reference;
}

function loadLocation(locationID, anchor) {
	
	showElement('loading');
	
	//var template = "{{#location}}"; 
	var template ='<div class="figure"><img src="{{imageurl}}"/></div>';
	template += '<div  class="contact_info"><h1>{{name}}</h1>';
	template += '<p class="category">Category: {{category}}</p>';
	template += '<p>{{address}}</p><p>{{city}}, {{state}}</p>';
	template +=	'<p>{{phone}}</p>';
	template +=	'{{#url}}<p><a href="{{url}}" target="_none" >{{url}}</a></p>{{/url}}';
	template += '</div>';
	template += '<div class="description"><p>{{shortdescription}}</p></div>';
	template += '<div class="links"><h2>Links</h2>';
	template += '{{#links}}<div class="row">';
	template += '<div class="link"><p><a href="{{link}}" target="_blank">{{title}}</a>{{#publication}} ({{publication}}){{/publication}}</p></div>';
	template += '</div>{{/links}}';
	template += '{{^links}}<p>No links have been added yet.</p>{{/links}}';
	template += '</div>';
	template += '{{#endorsements}}';
	template += '<div class="endorsements"><h2>Endorsed by:</h2><div class="row">';
	template += '<div class="figure"><img src="{{#imgurl}}{{imgurl}}{{/imgurl}}{{^imgurl}}img/icons/unknown.png{{/imgurl}}" alt="endorser"></div>';
	template += '<div class="endorser">{{endorserName}}</div><div class="date">{{date}}</div>';
	template += '<div class="comments">{{comments}}</div></div></div>';
	template += '{{/endorsements}}';
	//template += "{{/location}}";	
	
	var serviceURL = 'service/entityService.php?type=location';
	var working = 'Retrieving location . . .';
	
	if (working.length>0)
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
		  
	xmlhttp.onreadystatechange=function() { if (xmlhttp.readyState==4) {
		  	hideElement('loading');
		  	if (xmlhttp.status==200) {
			    var view = JSON.parse(xmlhttp.responseText);
				renderTemplate(template,view,anchor);
				}
			else {
				document.getElementById(anchor).innerHTML = "Unable to retrieve location.";
				}
		    }
		  }; 
	
	serviceURL += "&id=" + locationID;
	xmlhttp.open("GET",serviceURL,true);
	xmlhttp.send();	
}

function addEndorsement() {
	showElement('addEndorsement');
}


function deleteEndorsement(id) {
	
	var request = new XMLHttpRequest();
	var serviceURL = "service/endorsement.php";
	
	request.open('DELETE',serviceURL,true);
	request.setRequestHeader('Content-Type','application/json; charset=UTF8');
	request.onreadystatechange=function() {
		 if (request.readyState==4) {
		 	if (request.status==200) {
		 		url = window.location.toString();
				if (url.indexOf("#")>0)
					{
						url=url.split("#")[0];
					}
		 		window.location.reload();

		  	} 
		  else {
		  	// do what? error message?
		  	}
		  } 
	};
	var data = {};
	data['id'] = id;
	request.send(JSON.stringify(data));
}

function addLink() {
	showElement('addLink');
}


function deleteLink(id) {
	
	var request = new XMLHttpRequest();
	var serviceURL = "service/link.php";
	
	request.open('DELETE',serviceURL,true);
	request.setRequestHeader('Content-Type','application/json; charset=UTF8');
	request.onreadystatechange=function() {
		 if (request.readyState==4) {
		 	if (request.status==200) {
		 		url = window.location.toString();
				if (url.indexOf("#")>0)
					{
						url=url.split("#")[0];
					}
		 		window.location.reload();
		  	} 
		  else {
		  	// do what? error message?
		  	}
		  } 
	};
	var data = {};
	data['id'] = id;
	request.send(JSON.stringify(data));
}