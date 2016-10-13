<?php

include_once dirname(__FILE__) . '/../partials/pageCheck.php';
include_once dirname(__FILE__) . '/../classes/database.php';
include_once dirname(__FILE__) . '/../classes/utility.php';
include_once dirname(__FILE__) . '/../classes/service.php';

if ($_SERVER['REQUEST_METHOD']=="POST") {
    $event = Utility::getRequestVariable('event', 'unknown event');
    $entityType = Utility::getRequestVariable('entityType', 'unknown entity');
    $entityId = Utility::getRequestVariable('entityId', 0);
    
    $query = "INSERT INTO event (event,entityType,entityId,userId,sessionId,tenantId) values (";
    $query .= Database::queryString($event);
    $query .= ',' . Database::queryString($entityType);
    $query .= ',' . Database::queryNumber($entityId);
    $query .= ',' . Database::queryNumber($userID);
    $query .= ',' . Database::queryString(session_id());
    $query .= ',' . Database::queryNumber($tenantID);
    $query .= ")";
    
    $errorMsg = '';
    try {
        Database::executeQuery($query);
    }
    catch(Exception $ex) {
        $errorMsg = $ex->getMessage();
    }
    
    if (strlen($errorMsg)>0) {
        Service::returnError($errorMsg);
    }
    else {
        Service::returnJSON('{result: true}');
    }
}
else {
    echo "Unsupported HTTP method.";
    }
