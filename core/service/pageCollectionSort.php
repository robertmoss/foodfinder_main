<?php

/* a utility service to update the sort order on a page within a page Collection
 * get parameters are:
 *      collection: name of the pageCollection to update (e.g. 'home')
 *      pageid: id of the page
 *      sort: new sort/sequence number for the page
 */

include_once dirname(__FILE__) . '/../partials/pageCheck.php';
include_once dirname(__FILE__) . '/../classes/core/utility.php';
include_once dirname(__FILE__) . '/../classes/core/service.php';

if ($_SERVER['REQUEST_METHOD']=="POST") {
    $collection = Utility::getRequestVariable("collection","");
    $pageid = Utility::getRequestVariable("pageid","");
    $sort = Utility::getRequestVariable("sort","");
    
    if ($collection=="") {
        Service::returnError('collection parameter is required.');
    }
    if ($pageid=="") {
        Service::returnError('pageid parameter is required.');
    }
    if ($sort=="") {
        Service::returnError('sort parameter is required.');
    }
    
     if (!$user->hasRole('admin',$tenantID)) {
         Service::returnError('Access denied.',403);
     }
    
    $query = "call setPageSortOrderForCollection(" . Database::queryString($collection) . "," . Database::queryNumber($pageid) 
        . "," . Database::queryNumber($sort) . "," . Database::queryNumber($tenantID) . ");";
    Database::executeQuery($query);    

    $json='{"success":true}';
    Service::returnJSON($json);
    
    } 
else
    {
        Service::returnError('Unsupported HTTP method.');
    }