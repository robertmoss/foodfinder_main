<?php

class Config {
	
	// 9 is highest, meaningly only most urgent (level 10) messages will be logged; 0 means all messages will be logged regardless of level
	public static $debugLevel = "5"; 
	
	// database, file, or both - if database, will write to log file only if database cannot be accessed
	public static $log_mode = 'database'; 
		
	// path to root in CDN - currently using a Public Google Drive folder
	public static $cdn_path = 'https://googledrive.com/host/0B6lk3_H_nu3YOVdIZFdCM0lOYVk/'; 
	
	// folder the system will use to store temporary images while working on them
	public static $img_working = '/working';
	
}