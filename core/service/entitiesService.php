<?php

// this is the multi-type core service page for querying a list of entities based upon filter criteria

include dirname(__FILE__) . '/../partials/pageCheck.php';
include_once dirname(__FILE__) . '/../classes/database.php';
include_once dirname(__FILE__) . '/../classes/utility.php';
include_once dirname(__FILE__) . '/../classes/service.php';
include_once dirname(__FILE__) . '/../classes/classFactory.php';
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

// keeping this old parameter for backwards compatibility; return is preferred
$numToReturn = Utility::getRequestVariable('numToLoad', 0);
if ($numToReturn==0) {
    $numToReturn = Utility::getRequestVariable('return', 10);
}
if ($numToReturn>100) {
		$numToReturn=100; // let's not get crazy, people.
}
$offset = Utility::getRequestVariable('offset', 0);

$listId = Utility::getRequestVariable('list', 0);
if ($listId==0) {
    $listId = Utility::getRequestVariable('entityList', 0);
}

$descending = Utility::getRequestVariable('desc', 'false');

try {
    $class = ClassFactory::getClass($type, $userID, $tenantID);
}
catch(Exception $ex) {
    Service::returnError('Unknown or uncreatable type: ' . $type,400,'entitiesService?type=' .$type);
}

if ($_SERVER['REQUEST_METHOD']=="GET") {
    
    if ($listId>0) {
        // a list was requested here. Different handling than regular entity set
        try {

            $totalEntities = $class->getEntityCountForList($listId);
            $entities = $class->getEntitiesFromList($listId,$numToReturn,$offset);
           
            }
        catch (Exception $ex) {
            $message= 'Unable to retrieve ' . $type . ' set count: ' . $ex->getMessage();
            Service::returnError($message);
        }
        
    }
    else {
    	$totalEntities = $class->getEntityCount($_GET);	
    
    	try {
    		// we pass the entire _GET collection in so object classes can extract relevant filters
    		$entities = $class->getEntities($_GET,$numToReturn,$offset);
   		
        	}
    	catch (Exception $ex) {
    		$message= 'Unable to retrieve ' . $type . ' set: ' . $ex->getMessage();
    		Service::returnError($message);
    	}
    }
    
    $addSequence = (isset($_GET["sequence"])&&(strtolower($_GET["sequence"])=="yes"||strtolower($_GET["sequence"])=="true")); 
    if ($addSequence) {
        for ($i=0;$i<count($entities);$i++) {
            // computer nerds can suck it: you being numbering things with 1, not 0
            $entities[$i]["sequence"]=$i+1;
            // but since nerds insist on making many things sequence with 0 as first element 
            // we'll include this just for them
            $entities[$i]["sequence_zero"]=$i;
            }
        }
    
     if (strtolower($descending)=='true' || strtolower($descending)=='yes') {
        // reverse the sort order
        $newentities = array();
         for ($i=count($entities)-1;$i>=0;$i--) {
             array_push($newentities,$entities[$i]);
         }
         $entities=$newentities;
    }

    $set = '{"count": ' . $totalEntities; 
    $set .= ', "' . lcfirst($class->getPluralName()) . '": ' . json_encode($entities) . '}';
	header("Access-Control-Allow-Origin: *");	
	header('Content-Type: application/json');

	echo $set;
	}
else
	{
		echo "Unsupported HTTP method.";
	}