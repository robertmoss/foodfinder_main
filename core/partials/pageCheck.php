<?php

/*
 * pageCheck is an essential routine. It should be included on every page in the application. 
 * It verifies we have a session, sets the tenantid & userid in memory 
 */
    // need some better way to get to config - right now have to hardwire to assume core is in a certain place 
    include_once dirname(__FILE__) . '/../../classes/config.php';
    
    if (Config::$debugMode) {
    	ini_set('display_errors', 'On'); 
    }
    else {
        ini_set('display_errors', 'Off'); // switch to off for production deployment
    }

	//include_once substr(dirname(__FILE__),1,strlen(dirname(__FILE__))-10) . '/classes/user.php';
    include_once dirname(__FILE__) . '/../classes/user.php';
    include_once dirname(__FILE__) . '/../classes/context.php';
	include_once dirname(__FILE__) . '/../classes/utility.php';
	
	error_reporting(E_ALL | E_STRICT);
    session_start();
    Utility::debug('pageCheck executing for ' . $_SERVER["SCRIPT_FILENAME"] . ' - sessionid=' . session_id(), 1);
    date_default_timezone_set('America/New_York');
	$user = null;
    $newsession = false;
    $applicationID = 1; // not using for anything right now, but conceivable could be used in future if multiple application share same core and database
    if (!isset($_SESSION['userID'])) {
        $newsession = true;
    }

    // look at URL to see if it is custom one for a tenant
    // TO DO: as we add more custom URLs, need to look in DB or elsewhere vs. hardcoding
    
    // if the tenant ID is already set on the session, don't change it
    if (!isset($_SESSION['tenantID'])) {
        if ($_SERVER['SERVER_NAME']=='www.food-find.com' || $_SERVER['SERVER_NAME']=='food-find.com') {
            //$_SESSION['tenantID'] = 1;
            // for now, make bbq default site until food-find is ready
            $_SESSION['tenantID'] = 3;
        }
        elseif ($_SERVER['SERVER_NAME']=='food.food-find.com') {
            $_SESSION['tenantID'] = 1;
        }
        elseif ($_SERVER['SERVER_NAME']=='bars.food-find.com') {
            $_SESSION['tenantID'] = 5;
        }
        elseif ($_SERVER['SERVER_NAME']=='bbq.food-find.com') {
            $_SESSION['tenantID'] = 3;
        }
    }
    
	// if can't determine from URL, look for query string. Will default to 3
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
    Context::$tenantid = $tenantID;
	
    if (!isset($_SESSION['userID'])) {
		// set ID to 0 to indicate unauthenticated user
	   $_SESSION['userID']=0;
       $userID = 0;
    }
    else {
        $userID=$_SESSION['userID'];
    }
    
    Log::debug('instantiating new user for userID=' . $userID,1);
	$user = new User($userID,$tenantID);
    Context::$currentUser = $user;
    
    if ($newsession) {
        Log::startSession(session_id(),$tenantID,$userID);
    }
	
	if ($userID>0 && !$user->canAccessTenant($tenantID)) {
	    Log::debug('Unauthorized user attempted to access tenant page. (user=' . $userID . ', tenant=' . $tenantID . ')', 9);
		header('HTTP/1.0 403 Forbidden');
		echo '<p>You are not allowed to access this resource.</p>';
		exit();
	}
    elseif ($userID==0) {
		// TO DO: check whether tenant allows anonymous access
		// for now, assume that they all do
		$allowAnon = Utility::getTenantProperty($applicationID, $tenantID, $userID, 'allowAnonAccess');
		if (!$allowAnon && strtolower(basename($_SERVER['PHP_SELF']))!='login.php') {
		    //echo strtolower(basename($_SERVER['PHP_SELF']));
		    Log::debug('Unauthenticated user attempted to access tenant page. Redirecting to login. (tenant=' . $tenantID . ')', 9);
		    header('Location: Login.php?context=loginRequired');
            die();
		}
	}
    Utility::debug('pageCheck complete.  (user=' . $userID . ', tenant=' . $tenantID . ')', 1);
	

	
	
	