<?php

// a service that allows clients to request and receive lists of locations on a specified route

include dirname(__FILE__) . '/../partials/pageCheck.php';
include_once dirname(__FILE__) . '/../classes/core/database.php';
include_once dirname(__FILE__) . '/../classes/core/utility.php';

if ($_SERVER['REQUEST_METHOD']=="GET") {

	$origin = Utility::getRequestVariable("origin", "");
	$destination = Utility::getRequestVariable("destination", "");
	$maxDetour = Utility::getRequestVariable("maxDetour", "25"); // default is 25 miles
	$numToReturn = Utility::getRequestVariable("return", "10"); // default is 10
	$categories = Utility::getRequestVariable("categories", '');
	$errMessage = '';
	
	$o = explode(',',$origin);
	if (!isset($o[1])) {
		$errMessage = 'Invalid origin coordinates.';
	}
	else {
		$originLong  = $o[1];
		$originLat  = $o[0];
	}
	
	$o = explode(',',$destination);
	if (!isset($o[1])) {
		$errMessage = 'Invalid destination coordinates.';
	}
	else {
		$destLong  = $o[1];
		$destLat  = $o[0];
	}
	
	$filter = '';
	if (strlen($categories)>0) {	
		// may be a little overkill, but want to ensure nothing but integers get passed into category id list
		$idlist = explode("|",$categories,10);
		$separator = "";
		foreach ($idlist as $id) {
			if (is_numeric($id)) {
				$filter .= $separator . $id ;
				$separator = ",";
			}
		}
	}
	Utility::debug('filter is: ' . $filter,2);
	
	if ($errMessage=='') {
		// build query
		if (strlen($filter)>0) {
			$query = "call getLocationsOnRouteByCategoryIdList(";
		}
		else {
			$query = "call getLocationsOnRoute(";
		}
		$query .= Database::queryNumber($originLat);
		$query .= ', ' . Database::queryNumber($originLong);
		$query .= ', ' . Database::queryNumber($destLat);
		$query .= ', ' . Database::queryNumber($destLong);
		$query .= ', ' . Database::queryNumber($maxDetour);
		$query .= ', ' . Database::queryNumber($numToReturn);
		$query .= ', ' . Database::queryNumber($tenantID);
		$query .= ', ' . Database::queryNumber($userID);
		if (strlen($filter)>0) {
			$query .= ', ' . Database::queryString($filter);
		}
		$query .= ')';
		
		$data = Database::executeQuery($query);
		$location = '';
	
		$rows = array();
		while ($r = mysqli_fetch_assoc($data))
			{
			$rows[] = $r;
			}
		
		$set = "{\"locations\":" . json_encode($rows) . "}";

		header("Access-Control-Allow-Origin: *");	
		header('Content-Type: application/json');
		echo $set;
		
	}
	else {
		{
		header(' ', true, 403);
		echo $errMessage;
		die();
		}
	}
	
	
}
else
	{
	header(' ', true, 403);
	echo "Unsupported HTTP method.";
	}
	