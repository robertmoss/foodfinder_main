<?php

include dirname(__FILE__) . '/../partials/pageCheck.php';
include_once dirname(__FILE__) . '/../classes/core/database.php';
include_once dirname(__FILE__) . '/../classes/core/utility.php';
include_once dirname(__FILE__) . '/../classes/core/user.php';
	


//session_start();

$tenantID = $_SESSION['tenantID'];
$userID = $_SESSION['userID'];
if ($_SERVER['REQUEST_METHOD']=="GET") 	{

	$id = Utility::getRequestVariable('id', 0);

	if ($id==0)
		{
			echo "No user ID specified.";
			header(' ', true, 400);
			die();
		}
	try {
		$requestedUser = new User($id);
	}
	catch(Exception $e) {
			echo $e->getMessage();
			header(' ', true, 400);
			die();
	}

	$set = json_encode($requestedUser->getEntity($id,$tenantID,$userID));

	header('Content-Type: application/json');
	header('Access-Control-Allow-Origin: *');
	echo $set;

	}

elseif ($_SERVER['REQUEST_METHOD']=="POST")
	{
		$json = file_get_contents('php://input');
		$data = json_decode($json);
		$id = $data->{'id'};
		$class = new User($id);
		$type = 'user';
		
		// validate data
		try {
			$class->validateData($data);
			}
			catch (Exception $ex)
			{
				echo 'Unable to save ' . $type . ': ' . $ex->getMessage();
				header(' ', true, 400);
				die();
			}
		
		if ($id==0) {
			
			Utility::debug('Creating new user.', 5);
			
			try {
				$newID = $class->addEntity($data,$tenantID);
			}
			catch (Exception $ex)
			{
				echo 'Unable to save ' . $type . ':' . $ex->getMessage();
				header(' ', true, 500);
				die();
			}
			
			if ($newID==0) {
				echo 'Unable to save ' . $type . ' (returned 0 for id)';
				header(' ', true, 500);
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
				echo 'Unable to save ' . $type . ':' . $ex->getMessage();
				header(' ', true, 500);
				die();
			}
			if (!$result) {
				echo 'Unable to save ' . $type;
				header(' ', true, 500);
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
			echo "No user ID specified.";
			header(' ', true, 400);
			die();
		}
	
	// To do: what permissions are needed to delete a user?
	try {
		$class = new User($id);
		$class->deleteEntity($id,$userID,$tenantID);
		}
	catch (Exception $ex)
		{
		echo 'Unable to delete user:' . $ex->getMessage();
		header(' ', true, 500);
		die();
		}
	
	$set = '{"result": "deleted"}';
	header('Content-Type: application/json');
	header('Access-Control-Allow-Origin: *');
	echo $set;	


	} 
else
	{
	echo "Unsupported HTTP method.";
	header(' ', true, 400);
	die();
	}
