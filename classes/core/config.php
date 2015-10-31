<?php

class Config {
	
	public static $debugLevel = "1"; // 9 is highest, meaningly only most urgent (level 10) messages will be logged; 0 means all messages will be logged regardless of level
	public static $log_mode = 'database'; // database, file, or both - if database, will write to log file only if database cannot be accessed

}