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
	
	public static function queryNumber($value) {
		
		if ($value=='') {
			return 0;
		}
		else {		
		return $value;
		}
	}
	
    /*
     * Executes a query against the database, returning a mysqli_result class is successful (or TRUE for queries not returning results)
     */
	public static function executeQuery($query)
	{
		// connect to database
		Utility::debug('executing query ' . $query, 5);
		Utility::debug('server=' . self::$server . 'user=' .  self::$user, 9);
		$con = mysqli_connect(self::$server,self::$user,self::$password,self::$database);
		if (!$con) {
				Utility::debug('Error connecting to database: ' . mysql_error(), 1);										
				Utility::errorRedirect('Error connecting to database: ' . mysql_error());											
				}
		else {
			Utility::debug('Connected.', 5);	
		}	
		
		Utility::debug('Executing query . . .', 5);
		
		$data = mysqli_query($con,$query);

		if (!$data) {
			Utility::debug('Error executing query:' . mysql_error(),1);
			Utility::errorRedirect('Error connecting to database: ' . mysql_error());
			}
		else {
			Utility::debug('Query executed successfully', 5);
			return $data;
			}
	}	
	 
}
	
