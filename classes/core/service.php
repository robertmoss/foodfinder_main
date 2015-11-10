<?php
/*
 * Service class exposes functions related to RESTful API services
 */

class Service{
		
	public static function returnError($errorMessage,$errorCode=400) {
	// used to end service and return message to user
		header(' ', true, $errorCode);
		echo $errorMessage;
		die();
	}
	
	public static function returnJSON($json) {
		header('Content-Type: application/json');
		echo $json;
	}
	
}
