<?php

include_once 'utility.php';

class Database {
	
	public static $server = "localhost";
	public static $user = "appuser";
	public static $password = "Password1";
	public static $database = "food";
	
	public static function queryString($value) {
		$value = str_replace("'","''",$value);
		return "'" . $value . "'";
	}
	
	public static function queryDate($value) {
		
		
		if ($value=='') {
				return "null";
			} 
		else {
			// format string as MySQL compliant date
			$time = strtotime($value);
			$newformat = date('Y-m-d',$time);
	
			return "'" . $newformat . "'";
		}
	}
	
	public static function queryNumber($value) {
				
		/*if ($value=='') {Utility::debug('value is empty string ',9);}
		if ($value==0) {Utility::debug('value equals 0',9);}
		if (is_null($value)) {Utility::debug('value is null',9);}*/
				
		if (is_null($value) || $value=='') {  
			return 'null';
		}
		else {		
			return $value;
		}
	}
	
	public static function queryBoolean($value) {
		// booleans are stored as bits in database, so convert to 1 if true, 0 otherwise
		
		if ($value) {
			return 1;
		}		
		else {
			return 0;
		}

		
	}
	
	public static function executeQuery($query)
	{
		// connect to database
		Utility::debug('Database::executeQuery() called. Server=' . self::$server . ', user=' .  self::$user, 1);
		$con = mysqli_connect(self::$server,self::$user,self::$password,self::$database);
		if (!$con) {
				Utility::debug('Error connecting to database: ' . mysql_error(), 1);										
				Utility::errorRedirect('Error connecting to database: ' . mysql_error());											
				}
		else {
			//Utility::debug('Connected.', 9);	
		}	
		
		Utility::debug('executing query [' . $query . ']', 5);
		
		$data = mysqli_query($con,$query);

		if (!$data) {
			Utility::debug('Error executing query:' . mysqli_error($con),1);
			//Utility::errorRedirect('Error connecting to database: ' . mysqli_error());
			throw new Exception(mysqli_error($con));
			}
		else {
			Utility::debug('Query executed successfully', 1);
			return $data;
			}
	}	
	 
}
	
