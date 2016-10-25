/**
 * @author Robert Moss
 */

// PRODUCT - product related functions
function loadProduct(id) {
	
	setElementHTML('productBody','<div class="ajaxLoading">Loading product . . .</div>');
	var serviceURL = "core/service/entityService.php?type=product";
	serviceURL += "&id=" + id;
	
	$("#productModal").modal({
 	   backdrop: 'static',
    	keyboard: false
		});
	getJSON(serviceURL,null,null,loadProductCallback,loadProductError);
	return false;
}

function loadProductCallback(product) {
	setElementHTML('productHeader',product.title);
	var template = getProductTemplate();
	renderTemplate(template,product,'productBody',false);
}

function loadProductError() {
	setElementText('productBody','Unable to load product information: ' + message);
}

function getProductTemplate() {
	var template = '<div id=\"productid\" class=\"hidden\">{{id}}</div>';
		template += '<div id=\"productname\" class=\"hidden\">{{name}}</div>';
		template += '<div class=\"productModalImage\">{{#imageUrl}}<img src=\"{{imageUrl}}\"/>{{/imageUrl}}</div>';
		template += '<div>';
		template += '	{{#author}}<p class="author">By {{author}}</p>{{/author}}';
		template += '	<div><p>{{description}}</p></div>';
		template += '   {{#url}}<p><a href="{{url}}" target="_blank">Learn More</a></p>{{/url}}';
		template += '</div>';
	
	return template;
}
