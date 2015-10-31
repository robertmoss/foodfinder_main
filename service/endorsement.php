<?php

include dirname(__FILE__) . '/../partials/pageCheck.php';
include_once dirname(__FILE__) . '/../classes/database.php';
include_once dirname(__FILE__) . '/../classes/utility.php';

//session_start();

$tenantID = $_SESSION['tenantID'];
if ($_SERVER['REQUEST_METHOD']=="GET") {
	
	echo "Unsupported HTTP method.";	

	}
elseif ($_SERVER['REQUEST_METHOD']=="POST")
	{
		$json = file_get_contents('php://input');
		$data = json_decode($json);
		
		// currently we ignore type. If we need additional kinds of endorsements in future will add.
		// right now, just location endorsements
		
		$id = $data->{'id'};
		if ($id==0) {
			// this is a new record: insert
			
			$errMessage = '';
			
			// perform data validations
			if (strlen($data->{'userid'})<=0) {
				$errMessage .= 'Userid is required. ';
			}
			
			if (strlen($data->{'locationid'})<=0) {
				$errMessage .= 'Locationid is required. ';
			}
			
			if (strlen($data->{'date'})<=0) {
				$errMessage .= 'Date is required. ';
			}
			
			if (strlen($errMessage)>0) {
				echo 'Unable to save endorsement: ' . $errMessage;
				header(' ', true, 400);
				die();
			}
			
			Utility::debug('Adding endorsement', 5);
			
			$query = "call addLocationEndorsement(" . Database::queryNumber($data->{'locationid'});
			$query .= "," . Database::queryNumber($data->{'userid'});
			$query .= "," . Database::queryString($data->{'date'});
			$query .= "," . Database::queryString($data->{'comments'});
			$query .= ')';
			
			$result = Database::executeQuery($query);
			
			if (!$result) {
				echo 'Unable to save endorsement.';
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
				Utility::debug('Endorsement added: ID=' . $newID, 5);
				header('Content-Type: application/json');
				echo $response; 
			}
			
		}
		else {
				
			// this is an existing record: update	
			// to do: add data validations
			
			Utility::debug('Updating endorsement', 5);
		
			echo 'Unable to uodate endorsement: method is not yet implemented';
			header(' ', true, 500);
			
			}

	}
elseif ($_SERVER['REQUEST_METHOD']=="DELETE") 
	{
		$json = file_get_contents('php://input');
		$data = json_decode($json);
		
		// to do: got to figure out how to secure this sucker

		$id = $data->{'id'};
		if (!$id>0) {
			echo 'Unable to delete endorsement: an ID is required';
			header(' ', true, 400);
			die();
		}
		
		Utility::debug('Deleting endorsement id=' . $id, 5);
			
		$query = "call deleteLocationEndorsement(" . Database::queryNumber($id);
		$query .= "," . Database::queryNumber($tenantID);
		$query .= ')';
			
		$result = Database::executeQuery($query);
		
	} 
else
	{
		echo "Unsupported HTTP method.";
	}


