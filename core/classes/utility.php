<?php

include_once dirname(__FILE__) . '/../../classes/config.php';
include_once dirname(__FILE__) . '/database.php';
include_once dirname(__FILE__) . '/log.php';
include_once dirname(__FILE__) . '/cache.php';
include_once dirname(__FILE__) . '/tenant.php';
include_once dirname(__FILE__) . '/format.php';


class Utility {
	
   
    	
	public static function errorRedirect($errorMessage) {
		$_SESSION['errorMessage'] = $errorMessage;
		header("Location: error.php");
		die();	
	}
	
	public static function debug($message,$level) {
		// originally logging functions were in Utility, so retaining wrapper method to not break old code.
		// Just passes through to the Log class now. Use Log class instead of Utility going forward
		Log::debug($message, $level);
	}
	
	
	public static function getSessionVariable($varname,$default) {
		if (isset($_SESSION[$varname])) {
			return $_SESSION[$varname];
		}
		else {
			return $default;
		}
	}
	
	public static function getRequestVariable($varname,$default) {
		if (isset($_REQUEST[$varname])) {
			return $_REQUEST[$varname];
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
	
	//@ Thanks to - http://phpsec.org
	public static function generateHash($plainText, $salt = null)
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
	

	public static function getList($listID,$tenantID,$userID) {
		
		// putting this into the Utility class as a future wrapper
		// currently, some lists are hard-coded (like states—-things unlikely to change much)
		// others are retrieved from database
		
		// in future, need to add caching here since many of these lists will be slowly-changing at best
		// also, this is now very application specific and needs to get moved out of Utility to some sort
		// of list server object
		
		$return = array();
		switch ($listID) {
			case "states":
				$states = array("","AK","AL","AZ","CA","CO","CT","DC","DE","FL","GA","HI","ID","IA","IL","IN","KS","KY","LA","MA","MD","ME","MI","MO","MS","NC","ND","NE","NJ","NM","NV","NY","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT","VA","VT","WA","WI","WY");
				// for states, we want to use the abbreviation as both display and data value, so create multi
				foreach ($states as $state)
					{
						$return[]= array($state,$state);		
					}
				break;
            case "addressType":
                $query = "select id,type from addressType where tenantID=" . Database::queryNumber($tenantID);
                $result = Database::executeQuery($query);
                while ($r=mysqli_fetch_array($result,MYSQLI_NUM))
                {
                    $return[] = $r;
                }
                break;
			case "tenants":
                if ($userID==1) {
    				$query = "select id,name from tenant";
                }
                else {
                    $query = "select * from tenant T
                            inner join tenantUser TU on TU.tenantid=T.id
                            inner join tenantUserRole TUR on TUR.tenantuserid=TU.id
                            inner join role R on R.id=TUR.roleid
                            where R.name='admin'
                                and TU.userid=" . Database::queryNumber($userID) . ";";
                    }
				$result = Database::executeQuery($query);
				while ($r=mysqli_fetch_array($result,MYSQLI_NUM))
				{
					$return[] = $r;
				}
				break;
            case "roles":
                $query = "select name from role;";
                $result = Database::executeQuery($query);
                while ($r=mysqli_fetch_array($result,MYSQLI_NUM))
                {
                    $return[] = $r[0];
                }

                break;
			case "categories":
				$query = "call getCategories(" . $tenantID . ")";
				$result = Database::executeQuery($query);
				while ($r=mysqli_fetch_assoc($result))
				{
					$return[] = $r;
				}
				break;
			case "units":
				$units = array("gallon","liter", "milliliter", "ounces","pint","quart");
				foreach ($units as $unit)
					{
						$return[]= array($unit,$unit);
					}
				break;
			case "distilleries":
				Utility::debug('retrieving distilleries list: ' . $tenantID, 5);
				$query = "call getDistilleries(" . $tenantID . ");";
				$distilleries = Database::executeQuery($query);
				while ($r=mysqli_fetch_array($distilleries,MYSQLI_NUM))
				{
					$return[] = $r;
				}
				break;
			case "spirit_categories":
				Utility::debug('retrieving spirit categories . . .', 5);
				$query = "select C.id,C.name from category C inner join categoryType CT on C.categorytypeid=CT.id where CT.name='spirit' and C.tenantID=" . $tenantID . " order by C.name;";
				$categories = Database::executeQuery($query);
				while ($r=mysqli_fetch_array($categories,MYSQLI_NUM))
				{
					$return[] = $r;
				}
				break;
			case "categorytypes":
				Utility::debug('retrieving categorytypes . . .', 5);
				$query = "select id,name from categoryType";
				$types = Database::executeQuery($query);
				while ($r=mysqli_fetch_array($types,MYSQLI_NUM))
				{
					$return[] = $r;
				}
				break;
			case "locationStatus":
                // Pending: will be displayed only to certain roles (for now, admins), as they are locations waiting visits and write-ups
				$status_values = array("Active","Closed", "Temporarily Closed", "Unknown","Pending","Coming Soon");
				foreach ($status_values as $unit)
					{
						$return[]= array($unit,$unit);
					}
				break;
			case "locationProperty":
				// will need to be more dynamic in future to allow for tenant-specific lists and admin capability for adding
				// but for now we'll use a hardcoded list
				$values = array("Date Founded","Cooking Method");
				foreach ($values as $unit) {
						$return[]= array($unit,$unit);
					}
				break;
            case "entities":
                // list of system entities that can be managed/expanded with categories, entity lists, etc.
                $values = array("location","product");
                foreach ($values as $entity) {
                        $return[]= array($entity,$entity);        
                    }
                break;
            case "entityListTypes":
                // types of entity lists support; currently only "static" (i.e. defined by set members in a table); in future should
                // support "dynamic" (i.e. list defined by a query)
                $values = array("static");
                foreach ($values as $entity) {
                        $return[]= array($entity,$entity);        
                    }
                break;
            case "collectionTypes":
                $types= array("product");
                foreach ($types as $type)
                    {
                        $return[]= array($type,$type);        
                    }
                break;
             case "featureStatus":
                // list of system entities that can be managed/expanded with categories, entity lists, etc.
                $values = array("Draft","Awaiting Review","Published");
                foreach ($values as $entity) {  
                        $return[]= array($entity,$entity);        
                    }
                break;
            case "assignmentStatus":
                // list of system entities that can be managed/expanded with categories, entity lists, etc.
                $values = array("Unassigned","Assigned","Complete");
                foreach ($values as $entity) {  
                        $return[]= array($entity,$entity);        
                    }
                break;
            case "assignmentType":
                // list of system entities that can be managed/expanded with categories, entity lists, etc.
                $values = array("Feature","Other");
                foreach ($values as $entity) {  
                        $return[]= array($entity,$entity);        
                    }
                break;
            case "authorList":
                Utility::debug('retrieving author list . . .', 5);
                $query = "call getAuthorListByTenant(" . Database::queryNumber($tenantID) . ")";
                $types = Database::executeQuery($query);
                $return[] = array(null,"-- none --",null,null);
                while ($r=mysqli_fetch_array($types,MYSQLI_NUM))
                {
                    $return[] = $r;
                }
                break;
			default:
				Log::debug("Utility:getList() called with unknown list type:" . $listID,10);
				return false;
		}
		return $return;
		
	}

	public static function renderOptions($listID,$tenantID,$userID,$selectedID) {
		// takes the requested list and uses it to render the Options markup
		// use to populate a select control		
		// if selectedID is specialized, any item matching that ID will be flagged as the selected item
		$optionList = Utility::getList($listID,$tenantID,$userID);
		if (!$optionList) {
			// no list to render
			echo "<option>--No values--</option>";
			return false;
		}
		else {
			foreach($optionList as $o) {
				echo '<option value="' . $o[0] . '"';
				if ($selectedID && $selectedID==$o[0]) {
					echo ' selected';
				}
				echo '>' . $o[1];
				echo "</option>";
			}
		}
		
	}

	public static function getSubClass($listID) {
		// returns the name of the dataentity class associated with the submitted list.
		$subclass='';
		switch ($listID) {
			case "distilleries":
				$subclass="Distillery";
				break; 
			}
		return $subclass;
	}
	
	


	/* 
	 * multi-tenant functions
	 */
	
	// not sure if this is kludgey or elegant: but, we are treating the static properties and dynamically-definable settings
	// as the same thing here - just key/value pairs. All tenants have some keys, other keys may or may not be defined for a tenant
	public static function getTenantProperty($applicationID, $tenantID, $userID, $property) {
	        
	    Log::debug('retrieving tenant property ' . $property . " for tenant ID=" . $tenantID, 1);
        
		$key = $applicationID . ":" . $tenantID . ":" . $property;
        $value = Cache::getValue($key);     
		if (!$value) {
		    // cache miss. Need to retrieve from database
		    $class = new Tenant($userID,$tenantID);
            $query='';
		    if ($class->hasField($property)) {
		        // this is one the fields on the tenant table
                $query = 'select ' . $property . ' from tenant where id=' . Database::queryNumber($tenantID);
            }
            else {
                // this might be a dynamically-set property   
                $query = 'select value from tenantSetting where setting= ' . Database::queryString($property) . ' and tenantid=' . Database::queryNumber($tenantID);
            }                
            $data = Database::executeQuery($query);
            if ($data) {
                if ($row=$data->fetch_row()) {
                    $value = $row[0];
                    }
                else {
                    Log::debug('Warning: tenant property requested but not found: ' . $property . ' (tenantid= ' . $tenantID . ')',5);
                    $value = '*undefined*';    
                }
                Cache::putValue($key,$value);                
                }   
            }
        if ($value=='*undefined*') {
            // little dance we do here, so we can still cache the null
            $value = null; 
        }
         
        return $value;
	}

    /*
     * Same as getTenantProperty but adds a default value option 
     */
    public static function getTenantPropertyEx($applicationID, $tenantID, $userID, $property,$defaultValue) {
        $value = Utility::getTenantProperty($applicationID, $tenantID, $userID, $property);
        if (is_null($value)) {
            $value = $defaultValue;
        }
        return $value;
    }

    public static function getTenantMenu($applicationID, $userID,$tenantID) {
        // returns an array representing the menu for the particular tenant
        // caches array
        
    Log::debug('retrieving menu for tenant ID=' . $tenantID, 1);
    $key = $applicationID . ":" . $tenantID . ":menu";
    $value = Cache::getValue($key); 
    if (!$value) {
        // retrieve from database
        $query = 'call getMenuItemsByTenantID(' . $tenantID . ',' . $tenantID . ',' . Database::queryNumber($userID) . ');';
        $menu = array();
        $results = Database::executeQuery($query);
        if ($results) {
            // build string representation
            while ($row=$results->fetch_assoc()) {
                array_push($menu,$row);                
                 }
            }
            $value=$menu;
            Cache::putValue($key, $value);
         }
    return $value;        
    }
    
 public static function getTenantPageCollection($applicationID, $userID,$tenantID,$collectionName) {
        
        $query = "call getPagesForTenantByCollection(" . Database::queryString($collectionName) . ",$tenantID)";
        $results = Database::executeQuery($query);
        
        $col = array();
        while ($row = $results->fetch_assoc()) {
            $col[]=$row;
        }
        
        return $col;
 }
 
 public static function getTenantContent($key,$tenantID) {

        // in the future, we may want to cache content — depends upon how widely it gets used
        
        // default to en_US for now. In future add multilanguage support
        $language = 'en_US';
        
        $query = "call getTenantContent(" . Database::queryString($key) . ",$tenantID," . Database::queryString($language) .")";
        $results = Database::executeQuery($query);
        
        $content = array(
            'id' =>0,
            'name'=>$key,
            'contentText'=>'[Content Not Found - ' . $key . ']',
            'language'=>$language
            );
        if ($row = $results->fetch_assoc()) {
            $content=$row;
        }
        
        return $content;
     
 }
 
 public static function renderContent($key,$tenantID,$user) {
                 
             
         // TO DO: update this to retrieve the content object, not just the content - need to ID for saving updates    
         $content = Utility::getTenantContent($key, $tenantID);
         $class="";
         if ($user->hasRole('admin',$tenantID)) {
             $class=' class="content"';
         }
         
         echo '<div id="content_' . $content['id'] . '"' .$class .'>';
         echo '<input type="hidden" value="' . $key . '"/>';
         echo Format::renderWebContent($content['contentText']) .'</div>';
     
 }
 
	public static function isPositive($term) {
		$term = strtolower($term);
		return ($term=="yes" || $term=="y" || $term=="true" || $term=="1");		
		}
	
	public static function writeHiddenAPIKey() {
		if (isset($_SESSION["APIKey"])) {
			echo '<input id="APIKey" type="hidden" value=' .  $_SESSION["APIKey"] . '/>';
		}
	}

	public static function getArrayValue($array,$key) {
		// utility function to get an array value if key exists or empty string if it doesn't
		if (array_key_exists($key, $array)) {
			return  $array[$key];
			}
		else {
			return '';
		}
	}
	
/* passthroughs for functions moved to other classes so we don't break calling code */
public static function addDisplayElements($location) {

	return Format::addDisplayElements($location);
}

public static function renderWebContent($content) {
    
    return Format::renderWebContent($content);
}


	
/* Batch functions */	
	public static function startBatch($name,$itemcount,$tenantid) {
	
		$query = 'call addBatch(' . Database::queryString($name) . ','. $itemcount . ',' . $tenantid . ')';
		$result = Database::executeQuery($query);
		$row = mysqli_fetch_array($result);
		return $row[0];	
	}
	
	public static function updateBatch($id,$itemscomplete,$tenantid) {
		$query = 'call updateBatchById(' . $id . ',' . $tenantid . ',\'running\',' . $itemscomplete . ')';
		try {
			$result = Database::executeQuery($query);
			return true;
		}
		catch(Exception $e)
		{
			// couldn't update Batch status — can assume error is because batch has been canceled.	
			return false;			
		}
	}
	
	public static function cancelBatch($id,$tenantid,$userid) {
		$query = 'call updateBatchById(' . $id . ',' . $tenantid . ',\'canceled\', -1 )';
		try {
			$result = Database::executeQuery($query);
			return true;
		}
		catch(Exception $e)
		{
			// couldn't update Batch status — can assume error is because batch has been canceled.	
			return false;			
		}
	}
	
	public static function getBatchStatus($id,$tenantid,$userid) {
		$query = 'call getBatchById(' . Database::queryNumber($id) . ',' . $tenantid . ',' . $userid . ')';
		$result = Database::executeQuery($query);
		return $result;
	}
	
	public static function finshBatch($id,$itemscomplete,$tenantid) {
		$query = 'call updateBatchById(' . $id . ',' . $tenantid . ',\'complete\',' . $itemscomplete . ')';
		$result = Database::executeQuery($query);
	}
	
    	
}

