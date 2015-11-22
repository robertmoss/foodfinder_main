/*
 * Companion scripts for workingPanel.php partial. Include whenever you want to have a working panel on your page
 */

/*
 * message: HTML of message to be displayed in working panel
 * result: 'success','info','warning',or 'error'
 * parentElement (optional): move the workingPanel to be a child of the specified parent; useful for small screen layouts when panel might not be visible
 *							 or when you want to position the working panel in a particular spot
 * overlap: set to true if you want the working panel to sit on top of parent panel; false if you want it to follow inline 
 */
function showWorkingPanel(message,parentElement,overlap) {
	
	$('#workingPanel').removeClass();
	$('#workingPanel').addClass("workingPanel alert alert-info");
	if (message) {
		setElementHTML('workingPanelMessage',message);
	}
	if (parentElement) {
		$('#workingPanel').appendTo('#' + parentElement);
		if (overlap) {
			$('#workingPanel').addClass("overlap");
		}
		
	}
	showElement('workingPanelIcon');
	showElement('workingPanel');
}

function showWorkingPanelResults(message,result,parentElement,overlap) {
	var classString="workingPanel alert alert-dismissable";
	switch(result) {
		case 'success':
			classString+=" alert-success";
			break;
		case 'warning':
			classString+=" alert-warning";
			break;
		case 'error':
		case 'danger':
			classString+=" alert-danger";
			break;
		default:
			classString+=" alert-info";		
	}
	$('#workingPanel').removeClass();
	$('#workingPanel').addClass(classString);
	if (message) {
		setElementHTML('workingPanelMessage',message);
	}
	if (parentElement) {
		$('#workingPanel').appendTo('#' + parentElement);
		if (overlap) {
			$('#workingPanel').addClass("overlap");
		}
	}
	hideElement('workingPanelIcon');
	showElement('workingPanel');
}

