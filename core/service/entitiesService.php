<?php

// this is the multi-type core service page for querying a list of entities based upon filter criteria

include dirname(__FILE__) . '/../partials/pageCheck.php';
include_once Config::$root_path . '/classes/application.php';
include_once dirname(__FILE__) . '/../classes/database.php';
include_once dirname(__FILE__) . '/../classes/utility.php';
include_once dirname(__FILE__) . '/../classes/service.php';
include_once dirname(__FILE__) . '/../partials/checkAPIKey.php';


// retrieve input parameters
if (isset($_GET["type"])) {
		$type=$_GET["type"];
	}
else {
		echo 'Type parameter is required.';
		header(' ', true, 400);
		die();	
		}

$search= Utility::getRequestVariable('search', '');

$numToReturn = Utility::getRequestVariable('return', 10);
if ($numToReturn>100) {
		$numToReturn=100; // let's not get crazy, people.
}
$offset = Utility::getRequestVariable('offset', 0);

    $coretypes = array('user','tenant');
    if(!in_array($type,$coretypes,false) && !in_array($type, Application::$knowntypes,false)) {
        // unrecognized type requested can't do much from here.
        Service::returnError('Unknown type: ' . $type,400,'entityService?type=' .$type);
    }
    
    $classpath = Config::$root_path . '/classes/'; 
    if(in_array($type,$coretypes,false)) {
        // core types will be in core path as configured in config.php
        $classpath = Config::$core_path . '/classes/';
    }
	
// include appropriate dataEntity class & then instantiate it
include_once  $classpath . $type . '.php';
$classname = ucfirst($type); 	// class names start with uppercase
$class = new $classname($userID,$tenantID);	

if ($_SERVER['REQUEST_METHOD']=="GET") {
	
	$totalEntities = $class->getEntityCount($_GET);	

	try {
		// we pass the entire _GET collection in so object classes can extract relevant filters
		$entities = $class->getEntities($_GET,$numToReturn,$offset);
		
		//$set = json_encode($entity);
		$set = '{"count": ' . $totalEntities; 
		$set .= ', "' . strtolower($class->getPluralName()) . '": ' . json_encode($entities) . '}';
	}
	catch (Exception $ex) {
		$message= 'Unable to retrieve ' . $type . ': ' . $ex->getMessage();
		Service::returnError($message);
	}

	header("Access-Control-Allow-Origin: *");	
	header('Content-Type: application/json');

	echo $set;
	}
else
	{
		echo "Unsupported HTTP method.";
	}