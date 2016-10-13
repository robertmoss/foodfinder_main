$(document).ready(function() {
    
    var stateList = getElementValue('stateList');
    var states = [];
    if (stateList && stateList.length>0) {
    	states = stateList.split(",");
    	stateObject = {};
    	stateHoverObject = {};
    	for (i=0;i<states.length;i++) {
			stateObject[states[i]] = {fill: 'teal'};
			stateHoverObject[states[i]] = {fill: 'orange'};			    		
    	}
    } 
    
    $('#map').usmap({
		  // The click action
		  click: function(event, data) {
		  	var activeStates = getElementValue('stateList').split(",");
		  	if (activeStates.indexOf(data.name)>=0) {
		  		// only naviate to state page if state is included in Tenant Property list of active regions
			    var url = "region.php?region=" + data.name;
			    window.location = url;
			    } 
		  },
		   stateStyles: {fill: '#eeeeee'},
		   stateHoverStyles: {fill: '#cccccc'},
		   stateSpecificStyles: stateObject,
		   stateSpecificHoverStyles: stateHoverObject,
		   stateHoverAnimation: 200, 
		   showLabels: true
});
    
});
  
