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
	
	$knowntypes = array('tenant','location','link','media');
	if(!in_array($type,$knowntypes,false)) {
		// unrecognized type requested can't do much from here.
		Service::returnError('Unknown type: ' . $type);
	}
	
	$classpath = '/../classes/'; 
	$coretypes = array("tenant");
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
		$entity = $class->getEntity($id,$tenantID,$userID);
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
		$id = $data->{'id'};
		
		// validate data
			try {
				$class->validateData($data);
			}
			catch (Exception $ex)
			{
				header(' ', true, 400);
				echo 'Unable to save ' . $type . ': ' . $ex->getMessage();
				die();
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
			Utility::debug('Saving ' . $type . ' record with id=' . $id, 5);
			$result = false;
			try {
				$result=$class->updateEntity($id,$data,$tenantID,$userID);
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
	}  
else
	{
		header(' ', true, 400);
		echo "Unsupported HTTP method.";
	}