<?php

	ini_set('display_errors', 'On'); // switch to off for production deployment

	include_once dirname(__FILE__) . '/../classes/core/user.php';
	include_once dirname(__FILE__) . '/../classes/core/utility.php';

	Utility::debug('pageCheck executing for ' . $_SERVER["SCRIPT_FILENAME"], 1);

	error_reporting(E_ALL | E_STRICT);
	session_start();
	date_default_timezone_set('America/New_York');
	$user = null; 
	
	
	
	//  set tenant for this application. Will default to 0
	if (!isset($_SESSION['tenantID'])) {
		$_SESSION['tenantID'] = 0;
		// look to see if tenant specified on query string
		if (isset($_GET["tenant"])) {
			$_SESSION['tenantID'] = $_GET["tenant"]; 
			}
		else {
			// for now defaulting to 3: need to update to handle in future
			$_SESSION['tenantID'] = 3;
			}
		}
	$tenantID = $_SESSION['tenantID'];
	
	if (!isset($_SESSION['userID'])) {
		// set ID to 0 to indicate unauthenticated user
		$userID = 0;
	}
	else {
		$userID=$_SESSION['userID'];
		$user = new User($userID,$tenantID);
	}
	
	if ($user && !$user->canAccessTenant($tenantID)) {
		header('HTTP/1.0 403 Forbidden');
		echo '<p>You are not allowed to access this resource.</p>';
		exit();
	}
	else {
		// TO DO: check whether tenant allows anonymous access
		// for now, assume that they all do
	}
	
	$applicationID = 1;
	
	// The higher the debug level, the less granular the messages logged. Set to 9 to see only high-importance messages, 1 to see all
	$debug = 4;
	