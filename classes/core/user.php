<?php

include_once 'database.php';
include_once 'dataentity.php';
include_once 'utility.php';

class User extends DataEntity {
	
	public $id = 0; 
	public $name = null;
	public $email = null;
	public $twitterHandle = null;
	
	function __construct($id,$tenantid) {
		if ($id>0) {
			// retrieve user from database
			$query = 'SELECT id, name, email, twitterHandle FROM user where id=' . Database::queryNumber($id);
			Utility::debug('Creating user object for id=' . $id, 5);
			$result = Database::executeQuery($query);
			$row = mysqli_fetch_assoc($result);
			if (is_null($row)) {
				throw new Exception("User not found.");
			}
			else {
				$this->tenantid = $tenantid;
				$this->id = $row["id"];
				$this->name = $row["name"];
				$this->email = $row["email"];
				$this->twitterHandle = $row["twitterHandle"];
				Utility::debug('User object created.' .$id, 1);
			}
		
		}
	}
	
	public function getName() {
			return "User";
		}
	
	public function getFields() {
		$fields = array(
			array("name","string"),
			array("email","string"),
			array("password","string"),
			array("twitterHandle","string")
		);
		
		return $fields;
	}
	
	public function isRequiredField($fieldName) {
		// note: password is not required — for an update, you can change name, etc. w/o changing password
		// passwords cannot be set: they can only be reset to a tempoarry value
		return ($fieldName=='id'||$fieldName=='name');
	}
		
	public function getEntity($id) {
		
		// because our constructor queries DB and builds object we just need to return field values
		$entity	= array(
			"id" => $this->id,
			"name" =>$this->name,
			"email" =>$this->email,
			"twitterHandle" => $this->twitterHandle
			);
			
		return $entity;
	}
	
	public static function getUserDetails($username) {
		
		$query = 'SELECT id,name,email,password, twitterHandle, active FROM user where email=' . Database::queryString($username);
		
		$result = Database::executeQuery($query);
		$row = mysqli_fetch_assoc($result);
		
		return $row;
	}
	
	
	public function addEntity($data) {
		
		// before save: salt & hash password and perform user-specific validation
		$pass = Utility::generateHash($data->{"password"});
		$data->{"password"}=$pass;
		
		$newid=parent::addEntity($data,$this->tenantid);
		return $newid;
	}
	

	public function validateUser($username) {
		
		Utility::debug('Validating user ' .$username . ', tenantID=' . $this->tenantID, 9);
		
		if (strlen($username)==0 || strlen($password)==0)
			{
				throw new Exception("Invalid username or password.");
			}
		
		$userDetails = User::getUserDetails($username);
		if ($userDetails["id"]>0 && $userDetails["active"]==0) {
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
					Utility::debug('Validating user ' .$name . 'validated.', 9);
					}
				else {
					throw new Exception("Unable to validate that particular username/password combination.");
					}
			}
		}
	}

	public function getAPIKey() {
		// generates a one-time API Key for user
		// to do: explore more secure ways to do this
        $chars = str_split("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ");
		$key = "";
        for ($i = 0; $i < 32; $i++) {
            $randNum = rand(0, 61);
            $key .= $chars[$randNum];
        }
        return $key;
	}
	
	//Update the user's password
	public function updatepassword($pass) {
		
		$secure_pass = generateHash($pass);
		
		
		$query = "UPDATE user SET password = " . Database::queryString($secure_pass) . ' WHERE id = ' . Database::queryNumber($this->id);

		return (Database::executeQuery($query));

	}
	
	public function canRead($entityType,$id) {
		
		// to do: add mechanism for resolving entity/role permissions
		// for now, any authenticated user can do any thing
		if ($this->id==0) {
			return false;
		}
		else {
			return true;
		}
	}
	
	public function canEdit($entityType,$id) {

		// to do: add mechanism for resolving entity/role permissions
		// for now, any authenticated user can do any thing
		if ($this->id==0) {
			return false;
		}
		else {
			return true;
		}
		
	}
	
	public function canAdd($entityType,$id) {
		
		// to do: add mechanism for resolving entity/role permissions
		// for now, any user can add any entity
		if ($this->id==0) {
			return false;
		}
		else {
			return true;
		}
	}
		
	public function canDelete($entityType) {
		
		// to do: add mechanism for resolving entity/role permissions
		// for now, no user can delete entities
		if ($this->id==0) {
			return false;
		}
		else {
			return false;
		}
	}
	
	public function canAccessTenant($tenantID) {
		
		$query = 'select count(*) from tenantUser where userid=' . $this->id . ' and tenantid=' . $this->tenantid;
		$result = Database::executeQuery($query);
		if ($arr = mysqli_fetch_array($result)) {
			return ($arr[0]>0);
		}
		else {		
			return false;
		}
	}
	
	
	
}
