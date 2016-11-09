
window.onload = function() {
	loadProducts();
};

function loadProducts() {
	
	setElementText('workingPanelMessage','Loading collection . . .');
	showElement('workingPanel');
	// for now, assume we have just one location template (the default feature one)
	var serviceUrl = "core/service/entitiesService.php?type=product&sequence=true";
	var criteria = getElementValue('queryParams');
	if (criteria.length>0) {
		serviceUrl+='&' + criteria;
	}

	getJSON(serviceUrl,null,null,collectionLoaded,collectionLoadError);
}

function collectionLoaded(collection) {

	renderTemplate(getCollectionTemplate(),collection,'collectionAnchor');
	hideElement('workingPanel');
}

function collectionLoadError() {
	
}

function getCollectionTemplate() {
	var template = '<div class="collection">{{#products}}';
    template += '<div class="collectionItem">';	
	template += '<div class="bookCover"><img src="{{imageUrl}}"/></div>';
	template += '<h2><a href="{{url}}" target="_blank" onclick="logClick({{id}});">{{title}}</a></h2>';
	template += '<p class="author">By {{author}}</p>';
	template += '{{#price}}<p class="price">${{price}}</p>{{/price}}';
	template += '<p class="description">{{description}}</p>';
	template += '<p><a href="{{url}}" target="_blank" onclick="logClick({{id}});">Buy Now</a></p>';
	template += '</div>';
	template += '{{/products}}</div>';
	
	return template;
	
}

function afterCollectionEdit() {
	location.reload();
}


