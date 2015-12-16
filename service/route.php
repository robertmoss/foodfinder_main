<?php

// a service that allows clients to request and receive lists of locations on a specified route

include dirname(__FILE__) . '/../partials/pageCheck.php';
include_once dirname(__FILE__) . '/../classes/core/database.php';
include_once dirname(__FILE__) . '/../classes/core/utility.php';
include_once dirname(__FILE__) . '/../classes/core/service.php';

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
	
	$filter = getFilter($categories);
	
	if ($errMessage=='') {
            
		$data = fetchData($originLat,$originLong,$destLat,$destLong,$maxDetour,$numToReturn,$filter,$tenantID,$userID);
        
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
elseif ($_SERVER['REQUEST_METHOD']=="POST") {
    
    Log::debug('Route Service: Detailed route request posted.', 5);
    
    // takes posted data of points and renders more accurate point by point route
    $json = file_get_contents('php://input');
    $data = json_decode($json);
    
    $maxDetour=25;
    $numToReturn=50;
    if (array_key_exists('maxDetour', $data)) {
          $maxDetour = $data->{'maxDetour'};   
    }
    if (array_key_exists('return', $data)) {
          $numToReturn = $data->{'return'};   
    }
    $filter='';
    if (array_key_exists('categories',$data)) {
        $filter = getFilter($data->{'categories'});
    }
  
    if (!array_key_exists('points',$data)) {
        Service::returnError('An array of points must be posted to retrieve a route.');
    }
    $points = $data->{'points'};
    $rows = array();
    for($i=1;$i<count($points);$i++) {
        Log::debug('Processing point #' . $i . ' ' . $points[$i]->lat . ', ' . $points[$i]->lng,1);
        $data = fetchData($points[$i-1]->lat, $points[$i-1]->lng,$points[$i]->lat, $points[$i]->lng,$maxDetour,$numToReturn,$filter,$tenantID,$userID);
        while ($r = mysqli_fetch_assoc($data))
            {
            if (!alreadyInSet($rows,$r)) {
                $rows[] = $r;
            }
            }
    }
    Log::debug('Before trimming, row count=' . count($rows), 1);
       
    $set = "{\"locations\":" . json_encode($rows) . "}";

    header("Access-Control-Allow-Origin: *");   
    header('Content-Type: application/json');
    echo $set;
     Log::debug('Route Service request complete.', 5);
}
else
	{
	header(' ', true, 403);
	echo "Unsupported HTTP method.";
	}
 
 
 function fetchData($originLat, $originLong,$destLat,$destLong,$maxDetour,$numToReturn,$filter,$tenantID,$userID) {
         
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
        
        return $data;
 }
 
 function alreadyInSet($rows,$r) {
     $target = $r["id"];
     $inSet = false;    
     for($i=0;$i<count($rows);$i++) {
         if ($rows[$i]["id"]==$target) {
             //Log::debug('Trimming dupe row id=' . $target . ' (' . $rows[$i]["name"] . ')' , 1);
             $inSet=true;
             break;
         }
     }
     return $inSet;
 }
 
 function getFilter($categories) {
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
    return $filter;
 }
	