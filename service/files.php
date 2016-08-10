<?php

/* files.php: handler for uploading media files, linking to locations, and distributing to CDN
 * NOTE: default settings for PHP limit 
 * post_max_size and upload_max_filesize to rather small files. To handle typical large image
 * files, you will need to update these settings in the user.ini or php.ini files
 * Symptom is for $_FILES array to be empty if these limits are exceeded
 */

include_once dirname(__FILE__) . '/../core/partials/pageCheck.php';
include_once dirname(__FILE__) . '/../core/classes/database.php';
include_once dirname(__FILE__) . '/../core/classes/utility.php';
include_once dirname(__FILE__) . '/../core/classes/service.php';
include_once dirname(__FILE__) . '/../core/classes/imageHandler.php';
include_once dirname(__FILE__) . '/../classes/media.php';
include_once dirname(__FILE__) . '/../classes/location.php';
include_once dirname(__FILE__) . '/../' . Config::$cdn_classfile;

Utility::Debug('files.php invoked ',5);

if ($_SERVER['REQUEST_METHOD']=="GET") {
	Service::returnError('Method not supported.');
}
elseif ($_SERVER['REQUEST_METHOD']=="POST") {
	
	if (count($_FILES)==0) {
		Service::returnError('No files submitted or files unable to be received. Current maximum file size is ' . ini_get("upload_max_filesize") . ' and total upload max size is ' . ini_get("post_max_size") . '.');
	}
	
	// if a locationid is included on post, all files submitted will be linked to specified location
	$locationid = Utility::getRequestVariable('locationid', 0);
	if ($locationid>0) {
	    $location = new Location($userID,$tenantID);    
	    if (!$location->userCanEdit($locationid,$user)) {
	          Log::debug('User ' . $userID . ' attempted unauthorized edit of location id=' . $locationid, 9);
		      Service::returnError('User does not have permission to edit specified location',401);
	       }
	   }
	
	// build array of files. These need to match the Media class fields
	$files = array();
	for ($i=0;$i<count($_FILES["importFile"]["name"]);$i++) {
		$file = array(
			"id"=>0,	
			"url"=>'',
			"name"=>$_FILES["importFile"]["name"][$i],
			"type"=>$_FILES["importFile"]["type"][$i],
			"tmp_name"=>$_FILES["importFile"]["tmp_name"][$i],
			"description"=>"",
			"metadata"=>"",
			"public"=>0
		);
		array_push($files,$file);
	}
	
	// 1. Validate files
	$errMessage = "";	
	foreach ($files as $file) {
		$supported_types = array("image/png","image/jpeg","image/jpg","image/gif");
		if (!in_array($file["type"],$supported_types)) {
			$errMessage .= 'File type not supported. (' .$file["type"] . ')';
		}
	}
	if (strlen($errMessage)>0) {
		Service::returnError('Unable to upload files: ' . $errMessage);
	}
	
	// 2. Resize image if it's too large
	// TO DO: do we need to automatically create thumbnails, too?
	for ($i=0;$i<count($files);$i++) {
	     $sourcefile = $files[$i]["tmp_name"];
        $type = $files[$i]["type"];
	    $handler = new ImageHandler($sourcefile,$type);
	    if ($handler->getWidth()> 500 || $handler->getHeight()>500) {
            try {
                $destination = $files[$i]["tmp_name"];
                $handler->resize(500, 500,$destination);
            }
            catch(Exception $ex) {
                Service::returnError('Unable to upload files. Error resizing file: ' . $ex->getMessage());
            }
        }
    }
	
	// 3. store in CDN
	try {
    	$cdn = new Config::$cdn_classname($userID,$tenantID);
    }
    catch (Exception $ex) {
        Service::returnError('Unable to save media file. Unable to create interface to CDN.');
    }
	for ($i=0;$i<count($files);$i++) {
		$sourcefile = $files[$i]["tmp_name"];	
		$key = $files[$i]["name"];
        try {
    		$files[$i]["url"] = $cdn->putContent($sourcefile,$key,'');
        }
        catch(Exception $ex) {
            Service::returnError('Unable to store file in CDN: ' . $ex->getMessage());
        }
	}
	
	
	// 4. save metadata in DB
	$media = new Media($userID,$tenantID);
	foreach($files as $file) {
		$data = (object) $file;
		try {
			$mediaid=$media->addEntity($data);
			}
		catch(Exception $e) {
			Service::returnError('Unable to save media record:' . $e->getMessage());
		}
		
		if ($locationid>0) {
			$media->linkMediaToLocation($mediaid,$locationid);
		}
	}
	
	// 5. build & return response
	$output= array();
	foreach ($files as $file) {
		$set = array(
			"name" => $file["name"],
			"url" => $file["url"]
			);
		array_push($output,$set);
	}
	
	$response = json_encode($output);
	Service::returnJSON($response);
	}	
else {
	Service::returnError('Method not supported.');
}
