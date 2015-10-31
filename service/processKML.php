<?php
/*
 * Processes KML file to import locations from a Google Map
 * Uses Google Places API to get location data
 */
include_once dirname(__FILE__) . '/../classes/core/database.php';
include_once dirname(__FILE__) . '/../classes/core/utility.php';
include_once dirname(__FILE__) . '/../classes/googlePlaces.php';

Utility::debug('processKML.php: processing KML file',5);

$source=Utility::getRequestVariable('source', '');
$batchid=Utility::getRequestVariable('batchid', 0);
$tenantid=Utility::getRequestVariable('tenantid', 0);

ignore_user_abort();

Utility::debug('Source: ' . $source . ', batch: ' . $batchid,5); 
try {
	$xml=simplexml_load_file($source);
	if (!$xml) {
		Utility::debug('Unable to load xml file.' . $xml,2);
	}
	else {
	 	Utility::debug('Xml file loaded:' . $xml,5);
	}
}
catch (Exception $e) {
	Utility::debug('Unable to load xml file: ' . $e->getMessage() ,1);
	die();
}

$itemscomplete = 0;
$count=0;
$places = new GooglePlaces();
$exceptions;
$exceptionCount=0;
$canceled = false;
foreach ($xml->Document[0]->Placemark as $placemark) {
    	$count++;
    	Utility::debug("Creating location " . $placemark->name,9);

		// Validate data
		$errMessage = '';
		if (strlen($placemark->name)<=0) {
				$errMessage .= 'Name is required. ';
			}
			// to do: add other validations
			
		if (strlen($errMessage)>0) {
			Utility::debug("Unable to save location/place #: " . $count . ". " . $errMessage,9);
			$exceptions[]=$errMessage;
			$exceptionCount++;
			}
		else {
			$coords = explode(",",$placemark->Point[0]->coordinates);	
		
			// try to augment by calling Google Places
				
			$result = $places->checkGooglePlaces($placemark->name, $coords[0], $coords[1]);
			$address='';
			$city = '';
			$state = '';
			$zip = '';
	
			if ($result && isset($result['result'])) {
				foreach($result['result']['address_components'] as $component) {
					switch ($component["types"][0]) {
						case "street_number":
							$address = $component["long_name"] . $address; 
							break;
						case "route":
							$address .= " " . $component["long_name"]; 
							break;
						case "locality":
							$city = $component["long_name"]; 
							break; 
						case "administrative_area_level_1":
							$state = $component["short_name"]; 
							break;
						case "postal_code":
							$zip = $component["long_name"]; 
							break; 
						 
						}
					}
				$placeid = $result['result']['place_id']; 
				$phone = Utility::getArrayValue($result['result'], 'formatted_phone_number');
				$url = Utility::getArrayValue($result['result'], 'website');
				}
			
				$query = "call addLocation(" . Database::queryString($placemark->name);
				$query .= "," . Database::queryString($address);
				$query .= "," . Database::queryString($city);
				$query .= "," . Database::queryString($state);
				$query .= "," . Database::queryString($phone);
				$query .= "," . Database::queryString($url);
				$query .= "," . Database::queryString($placemark->imageurl);
				$query .= "," . Database::queryNumber($coords[1]);
				$query .= "," . Database::queryNumber($coords[0]);
				$query .= ", null"; //. Database::queryNumber($placemark->categoryid); -- this isn't working for some reason.
				$query .= "," . Database::queryString($placemark->description);
				$query .= "," . Database::queryString($placemark->googleReference);
				$query .= "," . Database::queryString($placeid);
				$query .= "," . Database::queryNumber($tenantid);
				$query .= ')';
				
				$errMessage = ".";
				$result=null;
				try {
					$result = Database::executeQuery($query);
					}
				catch(Exception $e) {
					if ($debug>0) {
						// don't reveal errors unless in debug mode	
						$errMessage = $e->getMessage();
						}
					}
			
				if (!$result) {
					Utility::debug("Unable to save location/place #: " . $count . ". " . $errMessage,5);
					$exceptions[]=$errMessage;
					$exceptionCount++;
				}
				else {
					$newID=0;
					while ($r = mysqli_fetch_array($result))
						{
						$newID=$r[0];
						}
					$response = '{"id":' . json_encode($newID) . "}";
					Utility::debug('Location added: ID=' . $newID, 5);
				}		
 			}
		if (!Utility::updateBatch($batchid, $count, $tenantid)) {
			Utility::debug('Unable to update batch status. Assuming canceled batch and halting processing.', 3);
			$canceled = true;
			break;
			}
		}

if (!$canceled) {Utility::finshBatch($batchid, $itemscomplete, $tenantid);}
Utility::debug('Batch process complete.',5);