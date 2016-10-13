<?php

    include dirname(__FILE__) . '/../partials/pageCheck.php';
    include_once dirname(__FILE__) . '/../classes/propertyBag.php';
    include_once dirname(__FILE__) . '/../classes/service.php';
    include_once dirname(__FILE__) . '/../classes/utility.php';

if ($_SERVER['REQUEST_METHOD']=="POST")  {

    // for now, must be an admin to access property bags: will have to update later if we being using propertyBags for things
    // other than system/tenant settings
    if (!$user->hasRole('admin',$tenantID)) {
        Service::returnError('Access denied.',403,'propertyBag');
    }
    
    $json = file_get_contents('php://input');
    $data = json_decode($json);
    if (!$data || !array_key_exists('name', $data)) {
          Service::returnError('PropertyBag name must be specified for an update.',400,'propertyBag Service');   
        }
    if (!$data || !array_key_exists('properties', $data)) {
          Service::returnError('PropertyBag properties must be specified for an update.',400,'propertyBag Service');   
        }
    
    $bagName = $data->{"name"};
    $properties = $data->{"properties"};
    $propertyBag = new PropertyBag($userID,$tenantID);
     foreach ($properties as $property=>$value) {
        $propertyBag->putProperty($bagName, $property, $value);
    }
 
    header("Access-Control-Allow-Origin: *");   
    header('Content-Type: application/json');

    echo json_encode($properties);
    
}
else {
    Service::returnError('Method not supported.',400,'propertyBag');
}
    