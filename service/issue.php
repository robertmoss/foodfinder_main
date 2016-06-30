<?php
//http_response_code(500); // set to return error unless explicitly succeeds (handles internal errors)
include dirname(__FILE__) . '/../core/partials/pageCheck.php';
include_once dirname(__FILE__) . '/../core/classes/utility.php';
include_once dirname(__FILE__) . '/../core/classes/service.php';
include_once dirname(__FILE__) . '/../classes/config.php';

Utility::debug('Issue service invoked, method=' . $_SERVER['REQUEST_METHOD'], 5);

if ($userID==0 || ($userID!=0 && !$user->hasRole('admin',$tenantID) && !$user->hasRole('contributor',$tenantID) )) {
    Service::returnError('You do not have permission to access this resource.',403,'issue');
}

if ($_SERVER['REQUEST_METHOD']=="POST") {
    
    // 1. build request body
    $json = file_get_contents('php://input');
    $data = json_decode($json);
    if (!array_key_exists('title', $data) || !array_key_exists('body', $data) || strlen($data->{"body"})==0 || strlen($data->{"title"})==0) {
        Service::returnError('Title and body (issue description) parameters are required.');
    }
    $data->{"labels"} = array('user submitted');
    $submittedBy = '*** Submitted via FoodFinder Log an Issue by ' . $user->name . ' *** ';
    $data->{"body"} = $submittedBy . $data->{"body"};
    
    // 2. submit API request
    $url = Config::$github_repo . '/issues';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, True);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);
    curl_setopt($ch, CURLOPT_VERBOSE, True);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: token ' . Config::$github_token));
    curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($data));
    curl_setopt($ch, CURLOPT_USERAGENT, 'Food Finder');
    
    Utility::debug('Posting issue via cUrl (url='. $url . ')',1);
    $response = curl_exec($ch);
    $error='';
    if ($error = curl_error($ch)) {
        Utility::debug('cUrl exception:' . $error,9);
        }
    curl_close($ch);
    
    if ($error) {
        Service::returnError('Unable to post issue to GitHub: ' . $error,500);
    }
    else {
        Utility::debug('Issue service call completed successfully.', 5);
        $returnData = json_decode($response);
        if (array_key_exists('number',$returnData)) {
            $response = json_encode(array("id"=>$returnData->{"number"}));
        } 
        else {
            Service::returnError('Unable to log issue. Response from repositor: ' . $response);
        }
        //http_response_code(200); 
        Service::returnJSON($response);
    }
        
}
else {
    Service::returnError('Unsupported method.',400,'issue');
}
