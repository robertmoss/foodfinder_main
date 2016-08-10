<?php

include dirname(__FILE__) . '/../core/partials/pageCheck.php';
include_once dirname(__FILE__) . '/../core/classes/database.php';
include_once dirname(__FILE__) . '/../core/classes/utility.php';

//session_start();

$tenantID = $_SESSION['tenantID'];
if ($_SERVER['REQUEST_METHOD']=="GET") {
	
	// retrive required parameters
	$locationID = $_GET["id"];

	$query = "call getLocationById(" . $locationID . "," . $tenantID . ")";
	$data = Database::executeQuery($query);
	
	//$rows = array();
	$location = '';
	
	while ($r = mysqli_fetch_assoc($data))
		{
		//$rows[] =$r;
		$location = $r;
		}
		
	$location = Utility::addDisplayElements($location);
	
	// add categories, endorsements & links, if they exist.
	$query = "call getCategoriesByLocationID(" . $locationID . "," . $tenantID . ")";
	$data = Database::executeQuery($query);
	if ($data->num_rows>0) {
		$categories = array();
		while ($r = mysqli_fetch_assoc($data)) {
			$categories[] = $r;
		}
		if (count($categories>0)) {
			$location['categories'] = $links;
		}
	}
	
	$query = "call getEndorsementsByLocationID(" . $locationID . "," . $tenantID . ")";
	$data = Database::executeQuery($query);
	if ($data->num_rows>0) {
		$endorsements = array();
		while ($r = mysqli_fetch_assoc($data)) {
			$endorsements[] = $r;
		}
		if (count($endorsements>0)) {
			$location['endorsements'] = $endorsements;
		}
	}
	
	$query = "call getLinksByLocationID(" . $locationID . "," . $tenantID . ")";
	$data = Database::executeQuery($query);
	if ($data->num_rows>0) {
		$links = array();
		while ($r = mysqli_fetch_assoc($data)) {
			$links[] = $r;
		}
		if (count($links>0)) {
			$location['links'] = $links;
		}
	}
	
	//$set = "{\"location\":" . json_encode($rows) . "}";
	$set = json_encode($location);

	header("Access-Control-Allow-Origin: *");	
	header('Content-Type: application/json');

	echo $set;
	}
elseif ($_SERVER['REQUEST_METHOD']=="POST")
	{
		//echo 'request received.';
		$json = file_get_contents('php://input');
		$data = json_decode($json);
		if (property_exists($data,'action')) {
			// this POST is using one of our utility/cheater functions
			$err="";
			if ($data->{'action'}=='setVisited') {
				Utility::debug('Setting visited status of location', 5);
				if (!property_exists($data,'visited')||!property_exists($data,'id')) {
					$err = "invalid parameters for setVisited operation. id and visited required.";
				}
				elseif ($userID==0) {
					$err = "User must be authenticated to set a visit record.";
				}
				else {
					$visited = ($data->{'visited'}) ? 1 : 0;
					$query = 'call setLocationVisited(' . $visited . ',' . $data->{'id'} . ',' . $tenantID . ',' . $userID . ');';
					try {
						$result = Database::executeQuery($query);
					}
					catch(Exception $e) {
					if ($debug>0) {
					// don't reveal errors unless in debug mode	
						$err = $e->getMessage();
						}
					else {
						$err = 'Unable to set visit';
						}
					}
				}
			}
		
			if (strlen($err)>0) {
				echo 'Unable to perform operation: ' . $err;
				header(' ', true, 403);
			}
			else {
				$response = '{"result": "success"}';
				header('Content-Type: application/json');
				echo $response; 
			}
			die();
		}
		else {
			// assume a regular POST for adding or updating a location record
		}
				
		$id = $data->{'id'};
		$errMessage = '';
		if ($id==0) {
			// this is a new record: insert
			
			// to do: add data validations
			$errMessage = '';
			if (strlen($data->{'name'})<=0) {
				$errMessage .= 'Name is required. ';
			}
			
			if (strlen($errMessage)>0) {
				echo 'Unable to save location: ' . $errMessage;
				header(' ', true, 400);
				die();
			}
			
			Utility::debug('Adding location', 5);
			
			$query = "call addLocation(" . Database::queryString($data->{'name'});
			$query .= "," . Database::queryString($data->{'address'});
			$query .= "," . Database::queryString($data->{'city'});
			$query .= "," . Database::queryString($data->{'state'});
			$query .= "," . Database::queryString($data->{'phone'});
			$query .= "," . Database::queryString($data->{'url'});
			$query .= "," . Database::queryString($data->{'imageurl'});
			$query .= "," . Database::queryNumber($data->{'latitude'});
			$query .= "," . Database::queryNumber($data->{'longitude'});
			$query .= "," . Database::queryString($data->{'shortdescription'});
			$query .= "," . Database::queryString($data->{'googleReference'});
			$query .= "," . Database::queryString($data->{'googlePlacesId'});
			$query .= "," . Database::queryNumber($tenantID);
			$query .= ')';
			
			$errMessage = ".";
			$result=null;
			try {
				$result = Database::executeQuery($query);
			}
			catch(Exception $e)
			{
				if ($debug>0) {
					// don't reveal errors unless in debug mode	
					$errMessage = $e->getMessage();
				}
			}
			
			if (!$result) {
				echo 'Unable to save location' . $errMessage;
				header(' ', true, 500);
			}
			else 
			{
				$newID=0;
				while ($r = mysqli_fetch_array($result))
					{
					$newID=$r[0];
					}
				$response = '{"id":' . json_encode($newID) . "}";
				Utility::debug('Location added: ID=' . $newID, 5);
				header('Content-Type: application/json');
				echo $response; 
			}
			
		}
		else {
				
			// this is an existing record: update
			
			// to do: add more data validations
			
			Utility::debug('Updating location', 5);
			
			$query = "call updateLocation(" . Database::queryString($data->{'id'});
			$query .= "," . Database::queryString($data->{'name'});
			$query .= "," . Database::queryString($data->{'address'});
			$query .= "," . Database::queryString($data->{'city'});
			$query .= "," . Database::queryString($data->{'state'});
			$query .= "," . Database::queryString($data->{'phone'});
			$query .= "," . Database::queryString($data->{'url'});
			$query .= "," . Database::queryString($data->{'imageurl'});
			$query .= "," . Database::queryNumber($data->{'latitude'});
			$query .= "," . Database::queryNumber($data->{'longitude'});
			$query .= "," . Database::queryString($data->{'shortdescription'});
			$query .= "," . Database::queryString($data->{'googleReference'});
			$query .= "," . Database::queryString($data->{'googlePlacesId'});
			$query .= "," . Database::queryNumber($data->{'tenantid'});
			$query .= ')';
			
			try {
				$result = Database::executeQuery($query);
			}
			catch(Exception $e)
			{
				$result=false;
				if ($debug>0) {
					// don't reveal errors unless in debug mode	
					$errMessage = $e->getMessage();
				}
				else {
					$errMessage = 'Unknown error.';
				}
				
			}
			
			if (!$result) {
				header(' ', true, 500);
				echo 'Unable to save location. ' . $errMessage;
			}
			else 
			{
				Utility::debug('Location updated.' , 5);
				$newID=$data->{'id'};
				$response = '{"id":' . json_encode($newID) . "}";
				header('Content-Type: application/json');
				echo $response; 
			}
			
			
		}
		//echo $json["name"];
		//header(' ', true, 400);
	} 
else
	{
		echo "Unsupported HTTP method.";
	}

