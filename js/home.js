window.onload = function() {
	$(".editable").hover(function(){
		showElement('floatingButtons');
		panel = document.getElementById('floatingButtons');
		$(this).append(panel);
		button = document.getElementById('editButton');
		$(button).off("click");
		$(button).click(function(e) {
			var element = $(this).parent().parent().find("p");
			editPage(element[0].innerHTML);
			e.stopPropagation();
		});
		button = document.getElementById('deleteButton');
		$(button).off("click");
		$(button).click(function(e) {
			var element = $(this).parent().parent().find("p");
			deletePage(element[0].innerHTML);
			e.stopPropagation();
		});
	}, function() {
		hideElement('floatingButtons');
	}
	
	);
	
	$('.sortable').sortable({
		stop: function( event, ui ) {
			processSort(event,ui);
		}
	});
};

function addPage() {
	showElement('btnPageSave');
	document.getElementById('btnPageCancel').innerText="Cancel";
	editEntity(0,'page',finalizeForm);
}

function editPage(id) {
	showElement('btnPageSave');
	document.getElementById('btnPageCancel').innerText="Cancel";
	editEntity(id,'page',finalizeForm);
}

function deletePage(id) {
	deleteEntity('page',id,null,postDelete);
}

function postDelete(success,message) {
	if (!success) {
		alert('Unable to delete page: ' +  message);
	}
	else {
		location.reload();
	}
}

function finalizeForm() {
	// supress the linkededities field and hardwire to 'home'
	hideElement('linkedentitiesPageCollections');
	
	var opt = document.createElement("option");
	var select = document.getElementById("addpageCollections");
	var destinationSelect = document.getElementById("pageCollectionSelect");
	var found=false;
	for (i=0;i<destinationSelect.options.length;i++) {
		if (destinationSelect.options[i].text.toLowerCase()=="home") {
			found=true;
			break;
		}
	}
	if (!found) {
		for (i=0;i<select.options.length;i++) {
				if (select.options[i].text.toLowerCase()=="home") {
					opt.text = select.options[i].text;
					opt.value =  select.options[i].value;
					opt.selected=true;
					destinationSelect.add(opt);
					break;
				}
		}
	}
}

function savePage() {
	saveEntity('page',postSave);
}

function postSave(success) {
	if (success) {
		hideElement('btnPageSave');
		document.getElementById('btnPageCancel').innerText="Close";
		$('#pageEditModal').modal('hide');
		location.reload();
	}
}



function enablePageDrop(event) {
	event.preventDefault();
	id=event.target.id;
}

function processSort(event,ui) {
	var pages = document.getElementById("pageContainer").children;
	var changes=[];
	for (var i=0;i<pages.length;i++) {
		var elements = $('#' + pages[i].id).find("p");
		var originalSequence = pages[i].id.substring(4);
		if((i+1)!=originalSequence) {
			// need to update this page's sequence. Push into array
			
			// [page id,new sequence,element]
			newSet = [elements[0].innerHTML,(i+1),pages[i]];
			changes.push(newSet);
		}
	}
	for (var i=0;i<changes.length;i++) {
		postSortChange(changes[i][0],changes[i][1]);
		changes[i][2].id="page"+changes[i][1];
	}
	
}

function postSortChange(pageid,sort)
{
	var request = new XMLHttpRequest();
	request.open('POST','core/service/pageCollectionSort.php',true);
	request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	request.send('collection=home&pageid='+pageid+'&sort='+sort);
}
