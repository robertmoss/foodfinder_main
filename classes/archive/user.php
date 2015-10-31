<?php

include_once 'database.php';

class User{
	
	public $id = null; 
	public $name = null;
	
	public static function getUserDetails($username) {
		
		$query = 'SELECT id,name,email,password, active FROM user where email=' . Database::queryString($username);
		
		$result = Database::executeQuery($query);
		$row = mysqli_fetch_assoc($result);
		
		return $row;
	}
	
	function __construct($username,$password,$tenantID) {
		
		if (strlen($username)==0 || strlen($password)==0)
			{
				throw new Exception("Invalid username or password.");
			}
		
		$userDetails = User::getUserDetails($username);
		if ($userDetails["active"]==0) {
			throw new Exception("This user account is inactive. Please check your email for activation instructions.");
			}
		else {
			$saltedPassword = Utility::saltAndHash($password,$userDetails["password"]);
			//echo 'salted:' . $saltedPassword;
			//echo Utility::saltAndHash($password);
			$query = 'call validateUser(' . Database::queryString($username);
			$query .= ',' . Database::queryString($saltedPassword);
			$query .= ',' . Database::queryNumber($tenantID) . ');';
					
			$result = Database::executeQuery($query);
			if (!$result) {
				throw new Exception('Unable to validate that username/password combination.');
				}
			else {
				$userid=0;
				while($o = mysqli_fetch_object($result)) {
					$userid = $o->userid;
					$name = $o->name;
					}
				if ($userid>0) {
					$this->id = $userid;
					$this->name = $name;
						}
				else {
					throw new Exception("Unable to validate that username/password combination.");
					}
			}
		}
	}

	
	//Update the user's password
	public function updatepassword($pass) {
		
		$secure_pass = generateHash($pass);
		
		
		$query = "UPDATE user SET password = " . Database::queryString($secure_pass) . ' WHERE id = ' . Database::queryNumber($this->id);

		return (Database::executeQuery($query));

	}
	
}
