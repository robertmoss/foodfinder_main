<?php

include dirname(__FILE__) . '/../partials/pageCheck.php';
include_once dirname(__FILE__) . '/../classes/core/database.php';
include_once dirname(__FILE__) . '/../classes/core/utility.php';
include_once dirname(__FILE__) . '/../classes/core/service.php';


	if (isset($_GET["type"])) {
		$type=$_GET["type"];
	}
	else {
		Service::returnError('Type is required.');
	}
	
	Utility::debug('entity service invoked for type:' . $type . ', method=' . $_SERVER['REQUEST_METHOD'], 5);
	
    if ($userID==0) {
        if ($_SERVER['REQUEST_METHOD']=="GET") {
            // if a GET, check whether anonymous access is allowed by tenant
            if (!Utility::getTenantProperty($applicationID, $tenantID, $userID, 'allowAnonAccess')) {
                Service::returnError('Service must be invoked by an authenticated user.' );
            }
        }
        else {
            Service::returnError('Service must be invoked by an authenticated user.');
        }
    }
    
	$knowntypes = array('tenant','location','link','media','tenantSetting','tenantProperty','category');
	if(!in_array($type,$knowntypes,false)) {
		// unrecognized type requested can't do much from here.
		Service::returnError('Unknown type: ' . $type);
	}
	
	$classpath = '/../classes/'; 
	$coretypes = array('tenant','tenantSetting','tenantProperty','category');
	if(in_array($type,$coretypes,false)) {
		// core types will be in core subfolder
		$classpath .= 'core/';
	}
	
	// include appropriate dataEntity class & then instantiate it
	$classfile = dirname(__FILE__) . $classpath . $type . '.php';
	if (!file_exists($classfile)) {
		Utility::debug('Unable to instantiate class for ' . $type . ' Classfile does not exist.', 9);
		Service::returnError('Internal error. Unable to process entity.');
	}
	include_once $classfile;
	$classname = ucfirst($type); 	// class names start with uppercase
	$class = new $classname($userID,$tenantID);	

if ($_SERVER['REQUEST_METHOD']=="GET") {
	
	// retrive required parameters
	$id=0;
	if (isset($_GET["id"])) {
		$id = $_GET["id"];
	}
	if ($id==0) {
		header(' ', true, 400);
		echo 'id is required parameter and must be non-zero.';
		die();		
	}
	
	try {
		$entity = $class->getEntity($id);
	}
	catch(Exception $ex) {
		Service::returnError('Unable to retrive requested ' . $type . '. Internal error.');
	}
	
	$set = json_encode($entity);

	header("Access-Control-Allow-Origin: *");	
	header('Content-Type: application/json');

	echo $set;
	}
elseif ($_SERVER['REQUEST_METHOD']=="POST")
	{
		$json = file_get_contents('php://input');
		$data = json_decode($json);
		if (!$data || !array_key_exists('id', $data)) {
		  Service::returnError('ID must be specified for an update.');   
		}
		$id = $data->{'id'};
        
        $action = Utility::getRequestVariable('action', '');
        
        // specifying 'action=fieldUpdate' on GET string will cause service to update just the submitted field (if specified)
        // for this usage, the data payload should be a JSON object containing the id of the entity and the values of the fields to be updated
        // Underlying data object must allow each specified field to be updated singly
        // Though typically used to update just a single field, this function will allow multiple fields to be updated in a single call 
        if ($action=='fieldUpdate' && $id>0) {
                
                Utility::debug('Updating ' . $type . ' record with id=' . $id . ' via single field updates.', 5);
                try {
                    $class->updateFields($id,$data);
                }
                catch (Exception $ex)
                {
                    Service::returnError('Unable to update ' . $type . ': ' . $ex->getMessage());
                }
            }
        else {
            // full entity add or update

            // validate data
			try {
				$class->validateData($data);
			}
			catch (Exception $ex) {
			    Service::returnError('Unable to save ' . $type . ': ' . $ex->getMessage());
			}
		
    		if ($id==0) {
    			// this is a new record: insert
    									
    			Utility::debug('Saving new ' . $type, 5);
    			
    			try {
    				$newID = $class->addEntity($data,$tenantID,$userID);
    			}
    			catch (Exception $ex)
    			{
    				header(' ', true, 500);
    				echo 'Unable to save ' . $type . ':' . $ex->getMessage();
    				die();
    			}
    			
    			if ($newID==0) {
    				header(' ', true, 500);
    				echo 'Unable to save ' . $type;
    			}
    			else 
    			{
    				$response = '{"id":' . json_encode($newID) . "}";
    				Utility::debug($type . ' record added: ID=' . $newID, 5);
    				header('Content-Type: application/json');
    				echo $response; 
    			}
    			
    		}
    		else {
    				
    			// this is an existing record: update
                // full entity update
    			Utility::debug('Saving ' . $type . ' record with id=' . $id, 5);
    			$result = false;
    			try {
    				$result=$class->updateEntity($id,$data);
    			}
    			catch (Exception $ex)
    			{
    				header(' ', true, 500);
    				echo 'Unable to save ' . $type . ':' . $ex->getMessage();
    				die();
    			}
                
    
    			if (!$result) {
                    Service::returnError('Unable to save ' . $type);
    			}
    			else 
    			{
    				Utility::debug($type . ' updated.' , 5);
    				$response = '{"id":' . json_encode($id) . "}";
    				header('Content-Type: application/json');
    				echo $response; 
    			}
    		}
    	}
	}
elseif ($_SERVER['REQUEST_METHOD']=="DELETE") {

    $supportedtypes = array('media','tenantSetting','tenantProperty');
    if(!in_array($type,$supportedtypes,false)) {
        // delete method not supported from all types
        Service::returnError('Method not supported for type: ' . $type);
    }
    // retrive required parameters
    $id = Utility::getRequestVariable('id', 0);
    if ($id==0) {
        Service::returnError("id is required parameter and must be non-zero.");
    }
    
    try {
        $entity = $class->deleteEntity($id);
    }
    catch(Exception $ex) {
        Service::returnError('Unable to delete requested ' . $type . '. Internal error.');
    }
    $set = json_encode(array('deleted'=> $id));

    header("Access-Control-Allow-Origin: *");   
    header('Content-Type: application/json');

    echo $set;


}  
else
	{
		header(' ', true, 400);
		echo "Unsupported HTTP method.";
	}