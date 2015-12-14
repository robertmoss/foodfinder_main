<?php

include dirname(__FILE__) . '/../partials/pageCheck.php';
include_once dirname(__FILE__) . '/../classes/core/database.php';
include_once dirname(__FILE__) . '/../classes/core/utility.php';
include_once dirname(__FILE__) . '/../classes/core/user.php';
include_once dirname(__FILE__) . '/../classes/core/service.php';
	
//session_start();

$tenantID = $_SESSION['tenantID'];
$userID = $_SESSION['userID'];
if ($_SERVER['REQUEST_METHOD']=="GET") 	{

	$id = Utility::getRequestVariable('id', 0);
    $detail = Utility::getRequestVariable('detail', 'no');

	if ($id==0)
		{
			header(' ', true, 400);
			echo "No user ID specified.";
			die();
		}
	try {
		$requestedUser = new User($id,$tenantID);
	}
	catch(Exception $e) {
			header(' ', true, 400);
			echo $e->getMessage();
			die();
	}

	$entity = $requestedUser->getEntity($id,$tenantID,$userID);
    if ($detail=='yes'||$detail='true') {
        // add tenants and other stuff for full detail requests
        $entity["tenants"] = $requestedUser->getTenants();
    }
    $set = json_encode($entity);

	header('Content-Type: application/json');
	header('Access-Control-Allow-Origin: *');
	echo $set;

	}

elseif ($_SERVER['REQUEST_METHOD']=="POST")
	{
		$json = file_get_contents('php://input');
		$data = json_decode($json);
		$id = $data->{'id'};
		$class = new User($id,$tenantID);
		$type = 'user';
        $action = Utility::getRequestVariable('action','save');
		Log::debug('User service called: method=POST id=' . $id . ' action=' . $action , 5);
        
        switch ($action) {
            case 'setTenantAccess':
                Log::debug('Setting tenant access for user ' . $id, 5);
                if (!$user->canEdit('user', $tenantID, $id)) {
                    Service::returnError('Access denied.',403);
                }
                try {
                    $class->setTenantAccess($data);
                    Utility::debug($type . ' updated with new tenant access.' , 5);
                    $response = '{"id":' . json_encode($id) . "}";
                    header('Content-Type: application/json');
                    echo $response;
                }
                catch(Exception $ex) {
                    Service::returnError('Unable to set user tenant access: ' . $ex->getMessage());
                }
                
                break;
            case 'save':
        		// validate data
        		try {
        			$class->validateData($data);
        			}
        			catch (Exception $ex)
        			{
        				Service::returnError('Unable to save ' . $type . ': ' . $ex->getMessage());
        			}
        		
        		if ($id==0) {
        			
        			Utility::debug('Creating new user.', 5);
        			
        			try {
        			    Log::debug('Adding new user for tenant ' . $tenantID, 5);
        				$newID = $class->addEntity($data);
        			}
        			catch (Exception $ex)
        			{
        				Service::returnError('Unable to save ' . $type . ':' . $ex->getMessage());
        			}
        			
        			if ($newID==0) {
        				Service::returnError('Unable to save ' . $type . ' (returned 0 for id)');
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
        			Utility::debug('Saving ' . $type . ' record with id=' . $id, 5);
        			$result = false;
        			try {
        				$result=$class->updateEntity($id,$data,$tenantID);
        			}
        			catch (Exception $ex)
        			{
        				header(' ', true, 500);
        				echo 'Unable to save ' . $type . ':' . $ex->getMessage();
        				die();
        			}
        			if (!$result) {
        				header(' ', true, 500);
        				echo 'Unable to save ' . $type;
        			}
        			else 
        			{
        				Utility::debug($type . ' updated.' , 5);
        				$response = '{"id":' . json_encode($id) . "}";
        				header('Content-Type: application/json');
        				echo $response; 
        			}
                }
                break;
            default:
	           Service::returnError('Invalid action: ' . $action);
	     
		}


	}
	
elseif ($_SERVER['REQUEST_METHOD']=="PUT")
	{
	$reset = $_GET["reset"];
	$id = $_GET["id"];
	if ($reset=="true") {
		echo "Password reset not yet implemented.";
	}
	header(' ', true, 400);
	}

elseif ($_SERVER['REQUEST_METHOD']=="DELETE") 
	{
	$id = Utility::getRequestVariable('id', 0);

	if ($id==0)
		{
			header(' ', true, 400);
			echo "No user ID specified.";
			die();
		}
	
	// To do: what permissions are needed to delete a user?
	try {
		$class = new User($id,$tenantID);
		$class->deleteEntity($id,$userID,$tenantID);
		}
	catch (Exception $ex)
		{
		header(' ', true, 500);
		echo 'Unable to delete user:' . $ex->getMessage();
		die();
		}
	
	$set = '{"result": "deleted"}';
	header('Content-Type: application/json');
	header('Access-Control-Allow-Origin: *');
	echo $set;	


	} 
else
	{
	header(' ', true, 400);
	echo "Unsupported HTTP method.";
	die();
	}
