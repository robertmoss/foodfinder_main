<?php

// this service sets the user's current location (longitude & latitude) on the Session object
// it can be used to persist a location for later

include dirname(__FILE__) . '/../partials/pageCheck.php';
include_once dirname(__FILE__) . '/../classes/database.php';
include_once dirname(__FILE__) . '/../classes/utility.php';

$tenantID = $_SESSION['tenantID'];
if ($_SERVER['REQUEST_METHOD']=="POST")
	{
	$_SESSION['latitude'] = $_POST['latitude'];
	$_SESSION['longitude'] = $_POST['longitude'];
	} 
elseif ($_SERVER['REQUEST_METHOD']=="GET") {
	$latitude = 0;
	$longitude = 0;
	if (isset($_SESSION['latitude'])) {
		$latitude = $_SESSION['latitude'];
	}
	if (isset($_SESSION['longitude'])) {
		$longitude = $_SESSION['longitude'];
	}
	$currentLocation =  array('latitude' => $latitude, 'longitude' => $longitude);
	header('Content-Type: application/json');
	echo '{"currentLocation": ' . json_encode($currentLocation) . '}';
}
else
	{
		echo "Unsupported HTTP method.";
		header(' ', true, 500);
	}

