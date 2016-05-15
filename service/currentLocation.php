<?php

// this service sets the user's current location (longitude & latitude) on the Session object
// it can be used to persist a location for later

include_once dirname(__FILE__) . '/../partials/pageCheck.php';
include_once dirname(__FILE__) . '/../classes/core/utility.php';


$tenantID = $_SESSION['tenantID'];
if ($_SERVER['REQUEST_METHOD']=="POST")
	{
	$_SESSION['latitude'] = $_POST['latitude'];
	$_SESSION['longitude'] = $_POST['longitude'];
    if (key_exists('address', $_POST)) {
        $_SESSION['currentAddress'] = $_POST['address'];
    }
    else {
        $_SESSION['currentAddress'] = '';
    }
    
    $currentLocation = $_POST['latitude'] . ', ' . $_POST['longitude'];
    header('Content-Type: application/json');
    echo '{"currentLocation": ' . json_encode($currentLocation) . '}';
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

