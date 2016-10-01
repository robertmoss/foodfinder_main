<?php
$thisPage = "service_proto";
include dirname(__FILE__) . '/../core/partials/pageCheck.php';
include_once dirname(__FILE__) . '/../core/classes/database.php';
include_once dirname(__FILE__) . '/../core/classes/utility.php';
//session_start();

Utility::debug('Executing service_proto.php', 1);

// retrive required parameters
$center_lat = $_GET["center_lat"];
$center_long = $_GET["center_lng"];
$return = Utility::getRequestVariable("return", 10);
$start = Utility::getRequestVariable("start", 0);
$categories = Utility::getRequestVariable("categories", '');
$tenantID = $_SESSION['tenantID'];
$listId = Utility::getRequestVariable('list', 0);
if ($listId==0) {
    $listId = Utility::getRequestVariable('entityList', 0);
}
Utility::debug('Executing service_proto.php with return=' . $return . " list=" . $listId , 5);

// connect to database
//$con=mysqli_connect(Database::$server,Database::$user,Database::$password,Database::$database);
$con=mysqli_connect(Config::$server, Config::$user, Config::$password, Config::$database);

if (!$con) {
	header(' ', true, 500);	
	echo 'Service unavailable.';
	die();
}
else {
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

   if ($listId>0) {
        // a list was requested here. Different handling than regular entity set
          $query = 'call getLocationsByEntityListIdEx(' . $listId . ',' . $tenantID . ',' . $start . ',' . $return . ')';    
    }
   elseif (strlen($filter>0)) {
        $query = "call getLocationsByLatLngAndCategoryIdList(" . $tenantID . "," . $userID . ",". $center_lat . "," . $center_long . "," . $return . "," . $start . "," . Database::queryString($filter) . ")";
    	}
    else {
        $query = "call getLocationsByLatLng(" . $tenantID . "," . $userID . ",". $center_lat . "," . $center_long . "," . $return . "," . $start . ")";
    	}
    
	Utility::debug('Executing query: ' . $query , 5);
	$data = mysqli_query($con,$query) or die(mysqli_error());
	$rows = array();
	
	while ($r = mysqli_fetch_assoc($data))
		{
		$rows[] = Utility::addDisplayElements($r);
		}
   
	$set = "{\"locations\":" . json_encode($rows) . "}";
	
	header('Content-Type: application/json');
	header('Access-Control-Allow-Origin: *');
	echo $set;
}

