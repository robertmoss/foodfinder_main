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
			// $message = $message . ' [' . __FILE__ . ']';
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
    
    public static function startSession($sessionid,$tenantid,$userid) {
        
        $query = "insert into session(sessionid,startDateTime,tenantid,userid)
                     values ('". $sessionid. "', now(), " . $tenantid .", " . $userid . ")";
        try {
            $con = mysqli_connect(Config::$server,Config::$user,Config::$password, Config::$database);
        }
        catch(Exception $e) {
            $this->debug('unable to write to session table: ' . $e->getMessage(),10);
        }
        if ($con) {
            mysqli_query($con,$query);
        }
        else 
            {
            $this->debug('unable to connect to database for debug: no connection returned.',10);
            }
    }    
    
    public static function endSession($sessionid) {
        Log::debug('ending session ' . $sessionid,1);
        $query = "update session set endDateTime=now() where sessionid='" . $sessionid . "'";
        try {
            $con = mysqli_connect(Config::$server,Config::$user,Config::$password, Config::$database);
        }
        catch(Exception $e) {
            $this->debug('unable to write to session table: ' . $e->getMessage(),10);
        }
        if ($con) {
            mysqli_query($con,$query);
        }
        else 
            {
            $this->debug('unable to connect to database for debug: no connection returned.',10);
            }
    } 
    
    public static function setSessionUserId($sessionid,$userid) {
        Log::debug('updating user id ' . $userid . ' on session record ' . $sessionid,1);
        $query = "update session set userid= " .$userid . " where sessionid='" . $sessionid . "'";
        try {
            $con = mysqli_connect(Config::$server,Config::$user,Config::$password, Config::$database);
        }
        catch(Exception $e) {
            $this->debug('unable to write to session table: ' . $e->getMessage(),10);
        }
        if ($con) {
            mysqli_query($con,$query);
        }
        else 
            {
            $this->debug('unable to connect to database for debug: no connection returned.',10);
            }
    }     
    
	
	private static function logToFile($message) {
		// may make this more sophisticated in the future; for now, just dump to file
		date_default_timezone_set('UTC');
		file_put_contents(self::$debug_filename, date('Y-m-d h:i:sa') . ' ' . $message . "\n", FILE_APPEND);
	}
}
