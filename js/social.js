

function fbLikeCallback(url, html_element) {
	
	postEvent('FB Like',url,null);  
}

function fbUnlikeCallback(url, html_element) {
	
	postEvent('FB Unlike',url,null);  
}

function fbShareCallback(url, html_element) {
	
	postEvent('FB Share',url,null);  
}