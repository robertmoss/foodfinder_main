<?php

include dirname(__FILE__) . '/../partials/pageCheck.php';
include_once dirname(__FILE__) . '/../classes/core/database.php';
include_once dirname(__FILE__) . '/../classes/core/utility.php';

$batchId = Utility::getRequestVariable('id', 0);
$action = Utility::getRequestVariable('action', 'status');
if ($batchId==0) {
	echo 'id parameter must be specified';
	header(' ', true, 400);
	die();	
	}

if ($action=='cancel') {
	Utility::debug('Canceling batch ' . $batchId . '...', 5);
	$result = Utility::cancelBatch($batchId,$tenantID,$userID);
	if (!$result) {
		echo 'Unable to cancel batch.';
		header(' ', true, 404);
		}
	else {
		$response = '{"status": "canceled"}';
		header('Content-Type: application/json');
		echo $response;
	}
}
else {
	
	Utility::debug('Checking batch status for batch ' . $batchId, 9);
	
	$result = Utility::getBatchStatus($batchId, $tenantID, $userID);
	
	if (!$result) {
		echo 'Batch status not found.';
		header(' ', true, 404);
		}
	else
		{
		if ($r = mysqli_fetch_array($result))
			{
			$status=$r[2];
			$items=$r[5];
			$processed=$r[6];
			
			$response = '{"status":' . json_encode($status) . ", ";
			$response .= ' "items":' . json_encode($items) . ",";
			$response .= ' "processed":' . json_encode($processed) . "}";
			header('Content-Type: application/json');
			echo $response;
			}
		else {
			header('Content-Type: application/json');
			header(' ', true, 400);
			echo 'Batch not found.';
				}
		}
}