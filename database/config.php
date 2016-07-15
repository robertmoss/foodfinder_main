<?php

class Config {

	// database connection information
	public static $server = "localhost";
	public static $user = "appuser";
	public static $password = "Password1";
	public static $database = "food";
 
	// 9 is highest, meaningly only most urgent (level 10) messages will be logged; 0 means all messages will be logged regardless of level
	public static $debugLevel = 6; 
	
    // If true, extra error/debug information will be printed on various screens in the application. Always set to false in production mode  
    public static $debugMode = false; 
        
	// database, file, or both - if database, will write to log file only if database cannot be accessed
	public static $log_mode = 'database'; 
	
	// where to write debug log
	public static $debug_filename = "/var/wwww/logs/foodfinder_debug.log";
    
    // file path location of root folder and core library on server
    public static $root_path = '/var/www/html/foodfinder';
    public static $core_path = '/var/www/html/foodfinder/core';
    
    // root of the application (used for client side URL creation)
    public static $site_root = 'http://www.palmettonewmedia.com/foodfinder';
    public static $core_root = 'http://www.palmettonewmedia.com/foodfinder/core';
    public static $service_path = 'http://www.palmettonewmedia.com/foodfinder/service';
    public static $core_service_path = 'http://www.palmettonewmedia.com/foodfinder/core/service';
    
	// specifies the file and class to use to access the CDN
	public static $cdn_classfile = '/core/classes/cdn.php';
	//public static $cdn_classfile = 'core/classes/googleDriveCDN.php';
	
	public static $cdn_classname =  'localCDN';
	//public static $cdn_classname =  'googleDriveCDN';
		
	// path to root in CDN (used in PHP code to save media)
	//public static $cdn_path = 'https://googledrive.com/host/0B6lk3_H_nu3YOVdIZFdCM0lOYVk/'; 
	public static $cdn_path = '/var/www/html/foodfinder/uploads';
	
	// base Url to root in CDN
	public static $cdn_root = 'http://www.palmettonewmedia.com/foodfinder/uploads';
	
	// folder the system will use to store temporary images while working on them
	public static $img_working = '/working';
    
    //OAUTH information for GitHub (for logging an issue)
    public static $github_token = 'ADD TOKEN HERE';
    public static $github_repo = 'https://api.github.com/repos/robertmoss/foodfinder_main';
    
     public static function getSiteRoot() {
        // gotta work on all this, since the custom URLs break everything    
        if (strtolower($_SERVER['SERVER_NAME'])=='www.palmettonewmedia.com') {
            return 'http://www.palmettonewmedia.com/foodfinder';
        }
        else {
            return 'http://' . $_SERVER['SERVER_NAME'];
        }
     }
     
      public static function getCoreRoot() {
        // gotta work on all this, since the custom URLs break everything    
        if (strtolower($_SERVER['SERVER_NAME'])=='www.palmettonewmedia.com') {
            return $this::$core_root;
        }
        else {
            return 'http://' . $_SERVER['SERVER_NAME'] . '/core';
       }
    }
      
      public static function getServiceRoot() {
        // gotta work on all this, since the custom URLs break everything    
        if (strtolower($_SERVER['SERVER_NAME'])=='www.palmettonewmedia.com') {
            return $this::$service_path;
        }
        else {
            return 'http://' . $_SERVER['SERVER_NAME'] . '/service';
       }
    }   
      
    public static function getCoreServiceRoot() {
        // gotta work on all this, since the custom URLs break everything    
        if (strtolower($_SERVER['SERVER_NAME'])=='www.palmettonewmedia.com') {
            return $this::$core_service_path;
        }
        else {
            return 'http://' . $_SERVER['SERVER_NAME'] . '/core/service';
       }
    }    	
}
