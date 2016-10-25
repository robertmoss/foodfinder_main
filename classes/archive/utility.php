<?php


class Utility{
	
	private static $debuglevel = 0;
	private static $server = "localhost";
	private static $user = "appuser";
	private static $password = "Password1";
	private static $database = "food";
	
	public static function errorRedirect($errorMessage) {
		$_SESSION['errorMessage'] = $errorMessage;
        $errorUrl = Config::getSiteRoot() . "/error.php";
		header("Location: " . $errorUrl);
		die();	
	}
	
	public static function debug($message,$level) {
		// for now, we just inserting into a debug database. May update this to be more sophisticated in the future
		if ($level >= self::$debuglevel) {
			//echo $message . '<br/>';
			$message = str_replace("'","''",$message);
			$query = "insert into debug.debug (message,level) values ('". $message . "'," . $level .")";
			$con = mysqli_connect(self::$server,self::$user,self::$password, self::$database);

			//mysql_select_db(self::$database) or die(mysql_error());
			mysqli_query($con,$query);
		}
		
	}
	
	public static function getSessionVariable($varname,$default) {
		if (isset($_SESSION[$varname])) {
			return $_SESSION[$varname];
		}
		else {
			return $default;
		}
	}
	
	public static function saltAndHash($plainText, $salt = null)
	{
		if ($salt === null)
		{
			$salt = substr(md5(uniqid(rand(), true)), 0, 25);
		}
		else
		{
			$salt = substr($salt, 0, 25);
		}
	
		return $salt . sha1($salt . $plainText);
	}
		
}
