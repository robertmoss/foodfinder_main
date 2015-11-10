<?php 

/*
 * Implements error, debug and trace log functions
 * 
 */
 include_once 'config.php';
 
 class Log {
 	public static function debug($message,$level) {
		// for now, we just inserting into a debug database. May update this to be more sophisticated in the future

		if ($level >= Config::$debugLevel) {
			$message = str_replace("'","''",$message);
			$message = $message . ' [' . __FILE__ . ']';
			if (Config::$log_mode=='file'||Config::$log_mode=='both') {
				 Log::logToFile($message);
			}
			$query = "insert into debug.debug (message,level) values ('". $message . "'," . $level .")";
			try {
				$con = mysqli_connect(Config::$server,Config::$user,Config::$password, Config::$database);
			}
			catch(Exception $e) {
				// do what on an error? Just eat debug?
				Log::logToFile('unable to connect to database for debug:' . $e->getMessage());
			}
			if ($con) {
				mysqli_query($con,$query);
			}
			else 
				{
				Log::logToFile('unable to connect to database for debug: no connection returned.');
				}
		}		
	}
	
	private static function logToFile($message) {
		// may make this more sophisticated in the future; for now, just dump to file
		date_default_timezone_set('UTC');
		file_put_contents(self::$debug_filename, date('Y-m-d h:i:sa') . ' ' . $message . "\n", FILE_APPEND);
	}
}
