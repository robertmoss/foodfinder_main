<?php

class Config {

	// database connection information
	public static $server = "localhost";
	public static $user = "appuser";
	public static $password = "Password1";
	public static $database = "food";

	
	// 9 is highest, meaningly only most urgent (level 10) messages will be logged; 0 means all messages will be logged regardless of level
	public static $debugLevel = "5"; 
	
	// database, file, or both - if database, will write to log file only if database cannot be accessed
	public static $log_mode = 'database'; 
	
	// where to write debug log
	public static $debug_filename = "/Library/WebServer/Logs/debug.log";
	
	// specifies the file and class to use to access the CDN
	public static $cdn_classfile = '/classes/core/cdn.php';
	//public static $cdn_classfile = '/classes/core/googleDriveCDN.php';
	
	public static $cdn_classname =  'localCDN';
	//public static $cdn_classname =  'googleDriveCDN';
		
	// path to root in CDN (used in PHP code to save media)
	//public static $cdn_path = 'https://googledrive.com/host/0B6lk3_H_nu3YOVdIZFdCM0lOYVk/'; 
	public static $cdn_path = '/Library/WebServer/Documents/foodfinder/uploads';
	
	// base Url to root in CDN
	public static $cdn_root = 'http://localhost/foodfinder/uploads';
	
	// folder the system will use to store temporary images while working on them
	public static $img_working = '/working';
	
}