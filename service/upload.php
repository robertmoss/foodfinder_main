<?php

include dirname(__FILE__) . '/../partials/pageCheck.php';
include_once dirname(__FILE__) . '/../classes/core/database.php';
include_once dirname(__FILE__) . '/../classes/core/utility.php';
include_once dirname(__FILE__) . '/../classes/googlePlaces.php';

$target_dir = $_SERVER['DOCUMENT_ROOT'] . "/foodfinder";
$target_dir .= "/uploads/";
$target_file = $target_dir . basename($_FILES["importFile"]["name"]);
$uploadOk = 1;
$fileExtension = pathinfo($target_file,PATHINFO_EXTENSION);
$workingFile = $_FILES["importFile"]["tmp_name"];
$count = 0;

Utility::debug('Importing file ' . $workingFile, 5);

Utility::debug("file type:" . $fileExtension,9);
 

// Check file size - right now, limiting to 5MB
$maxsize = 5000000;

$size = $_FILES["importFile"]["size"];
Utility::debug("file size=" . $size,5);
if ($size> $maxsize) {
	Utility::debug('File import rejected: file too large (' . $size . ')', 5);
    echo "Cannot upload file. File size exceeds maximum limit of " . $maxsize . ". ";
    header(' ', true, 400);
	die();
}

// Allow certain file formats
if ($fileExtension == "kml") {
    Utility::debug("Processing kml file.",7);
	
    $xml=simplexml_load_file($workingFile) or die("Error: Cannot parse file.");
	// use name of file being imported. This will let client query for progress
	$batchname = $_FILES["importFile"]["name"];
	$itemcount = count($xml->Document[0]->Placemark);
    $batchid = Utility::startBatch($batchname, $itemcount, $tenantID);
	
	// copy temp file to target folder
	copy($workingFile,$target_file);
    
    // will use curl to aynch execute the batch job
    $ch = curl_init();
	$url = 'http://' . $_SERVER['SERVER_NAME'] . "/foodfinder/service/processKML.php";
	$url .= "?source=" . urlencode($target_file);
	$url .= "&batchid=" . $batchid;
	$url .= "&tenantid=" . $tenantID;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	
	Utility::debug("Calling curl for url " . $url,7);
	
	curl_exec($ch);
	curl_close($ch);

	 
} 

header('Content-Type: application/json');
$response = '{"count":' . json_encode($itemcount);
$response .=  ', "batchid":' . $batchid . '}';
echo $response;
Utility::debug('File import batch initiated for ' . $workingFile . '. BatchID=' . $batchid, 5);

 
