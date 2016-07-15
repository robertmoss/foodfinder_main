<?php

// this service sets the user's current location (longitude & latitude) on the Session object
// it can be used to persist a location for later

include_once dirname(__FILE__) . '/../core/partials/pageCheck.php';
include_once dirname(__FILE__) . '/../core/classes/utility.php';


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
    $address = "not set";
	if (isset($_SESSION['latitude'])) {
		$latitude = $_SESSION['latitude'];
	}
	if (isset($_SESSION['longitude'])) {
		$longitude = $_SESSION['longitude'];
	}
    if (isset($_SESSION['currentAddress'])) {
        $address = $_SESSION['currentAddress'];
    }
	$currentLocation =  array('latitude' => $latitude, 'longitude' => $longitude,'address' => $address);
	header('Content-Type: application/json');
	echo '{"currentLocation": ' . json_encode($currentLocation) . '}';
}
else
	{
		echo "Unsupported HTTP method.";
		header(' ', true, 500);
	}

