<?php

include_once 'database.php';
include_once 'dataentity.php';
include_once 'utility.php';
include_once 'cache.php';

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
				Utility::debug('User object instantiated for user id ' .$id, 1);
			}
		}
        else {
            $this->tenantid = $tenantid;
            $this->id = 0;
            Utility::debug('New user object instantiated.', 1);
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
            
	    Log::debug('Adding new user for tenant ' + $this->tenantid, 5);
        
		// before save: salt & hash password and perform user-specific validation
		
		// ensure email not already in use
		$query = "select count(*) from user where email=" . Database::queryString($data->{"email"}) . ";";
        $result = Database::executeQuery($query);
        while ($arr = mysqli_fetch_row($result)) {
            if ($arr[0]>0) {
                throw new Exception("That email address is already in use. Please select another.");
            }
        }
		
		$pass = Utility::generateHash($data->{"password"});
		$data->{"password"}=$pass;
		
		$newid=parent::addEntity($data);
        
        // by default, a newly created user gets assigned to the current tenant
        $query = "call addTenantUserRole(" . Database::queryNumber($newid) . "," .
                                             Database::queryNumber($this->tenantid) . "," .
                                             Database::queryString('standard')  
                                                 . ");";
        Database::executeQuery($query);
        
		return $newid;
	}
	

	public function validateUser($username,$password) {
		
		Utility::debug('Validating user ' .$username . ', tenantID=' . $this->tenantid, 9);
		
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
			$query .= ',' . Database::queryNumber($this->tenantid) . ');';
					
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
					Utility::debug('User ' .$name . 'validated.', 9);
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
	
	public function canRead($entityType,$tenantid,$id) {
		
		// to do: add mechanism for resolving entity/role permissions
		// for now, any authenticated user read/view any entity
		if ($this->id==0) {
			return false;
		}
		else {
			return true;
		}
	}
	
	public function canEdit($entityType,$tenantid,$id) {

	   if ($this->id==0) {
	       // must be authenticated user to edit anything
			return false;
		}
		else {
		    // simple rule for now: to edit an entity within a tenant, you must be an admin within that tenant
		    return ($this->id==1 || $this->hasRole('admin',$tenantid));
			
		}
		
	}
	
	public function canAdd($entityType,$tenantid) {
		
		// rules for now: must be authenticated user to add and must be an admin within the tenant (or superuser)
		if ($this->id==0) {
			return false;
		}
		else {
			return ($this->id==1 || $this->hasRole('admin',$tenantid));
		}
	}
		
	public function canDelete($entityType,$tenantid,$id) {
		
		// for now, only admin user can delete entities
		if ($this->id==0) {
			return false;
		}
		else {
			return ($this->id==1 || $this->hasRole('admin',$tenantid));
		}
	}
	
	public function canAccessTenant($tenantID) {
		
        if ($this->id==1) {
            return true; // superuser can access anything
        }
        
        // first, check cache for roles
        $rolekey = "UTR:" . $this->id . ':' . $tenantID;
        $roles = Cache::getValue($rolekey);
        if (!is_null($roles)) {
            // user has roles, so can access
            return true;
        }
        else {
              // nothing in cache: check database     
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
    
    // returns an array containing the roles (as strings) for which the use has 
    // been enabled for the specified tenant; empty array if no roles enabled for that tenant 
    public function getTenantRoles($tenantid) {
        $roles = array();

        if ($this->id>0) {
            $rolekey = "UTR:" . $this->id . ':' . $tenantid;
            // first check cache
            $roles = Cache::getValue($rolekey);
            if (is_null($roles)) {
                // not cached: try database
                $roles = array();
                $query = "call getTenantRolesByUserId(" . Database::queryNumber($this->id) .
                    "," . Database::queryNumber($tenantid) . ");";
                 $results = Database::executeQuery($query);
                 while ($arr=mysqli_fetch_array($results)) {
                     array_push($roles,$arr[1]);
                 }
                 Cache::putValue($rolekey, $roles);
            }
        }    
        
        return $roles;
    }
    
    // returns true if user is assigned to specified role for specified tenant, false otherwise
    public function hasRole($role,$tenantid) {
        $hasRole = false; 
        if ($this->id>0) {
            if ($this->id==1) {
                $hasRole = true; // superuser by definition has all roles
                }
            else {
                $roles = $this->getTenantRoles($tenantid);
                $hasRole = in_array($role,$roles,false);
                }                
            }
        return $hasRole;
    }
	
    // returns all the tenant access and roles the user has
    public function getTenants() {
        $tenants = array();
        if ($this->id>0) {
            $query = "call getRolesByUserId(" . Database::queryNumber($this->id) . ");";
            $results = Database::executeQuery($query);
            $i=0;
            while ($arr=mysqli_fetch_assoc($results)) {
                $arr["index"]=$i;
                $i++;
                 array_push($tenants,$arr);
                 }
        }
        return $tenants;
    }
    
    public function setTenantAccess($data) {
        // TO DO: 1. remove all exiting tenants
        //        2. cycle through $data and add access to each tenant specified
        
        $queries = array("call removeTenantUsers(" . $this->id . ");");
        $tenants = $data->{'tenants'};
        foreach($tenants as $tenant) {
            $query = "call addTenantUserRole(" . Database::queryNumber($this->id) . "," .
                                                 Database::queryNumber($tenant->{'tenantid'}) . "," .
                                                 Database::queryString($tenant->{'role'})  
                                                 . ");";
            array_push($queries,$query);
        }
        
        Database::executeQueriesInTransaction($queries);
                
    }
	
	
}
