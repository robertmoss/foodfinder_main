<?php

/*
 * pageCheck is an essential routine. It should be included on every page in the application. 
 * It verifies we have a session, sets the tenantid & userid in memory 
 */

	ini_set('display_errors', 'On'); // switch to off for production deployment

	include_once dirname(__FILE__) . '/../classes/core/user.php';
	include_once dirname(__FILE__) . '/../classes/core/utility.php';

	
	error_reporting(E_ALL | E_STRICT);
    session_start();
    Utility::debug('pageCheck executing for ' . $_SERVER["SCRIPT_FILENAME"] . ' - sessionid=' . session_id(), 1);
    date_default_timezone_set('America/New_York');
	$user = null;
    $newsession = false; 
    if (!isset($_SESSION['userID'])) {
        $newsession = true;
    }

    // look at URL to see if it is custom one for a tenant
    // TO DO: as we add more custom URLs, need to look in DB or elsewhere vs. hardcoding
    if ($_SERVER['SERVER_NAME']=='www.food-find.com' || $_SERVER['SERVER_NAME']=='www.food-find.com') {
        $_SESSION['tenantID'] = 1;
    }
    if ($_SERVER['SERVER_NAME']=='bars.food-find.com') {
        $_SESSION['tenantID'] = 5;
    }
	
	//  set tenant for this application. Will default to 3
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
	   $_SESSION['userID']=0;
       $userID = 0;
    }
    else {
        $userID=$_SESSION['userID'];
    }
    
    if ($userID>0) {
		$user = new User($userID,$tenantID);
	}
    
    if ($newsession) {
        Log::startSession(session_id(),$tenantID,$userID);
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
	
	
	