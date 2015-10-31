<?php

include_once 'database.php';
include_once 'config.php';

class Utility{
	
	private static $server = "localhost";
	private static $user = "appuser";
	private static $password = "Password1";
	private static $database = "food";
	private static $debug_filename = "/Library/WebServer/Logs/debug.log";

	
	public static function errorRedirect($errorMessage) {
		$_SESSION['errorMessage'] = $errorMessage;
		header("Location: error.php");
		die();	
	}
	
	public static function debug($message,$level) {
		// for now, we just inserting into a debug database. May update this to be more sophisticated in the future
		Utility::logToFile($message);
		if ($level >= Config::$debugLevel) {
			$message = str_replace("'","''",$message);
			$message = $message . ' [' . __FILE__ . ']';
			if (Config::$log_mode=='file'||Config::$log_mode=='both') {
				Utility::logToFile($message);
			}
			$query = "insert into debug.debug (message,level) values ('". $message . "'," . $level .")";
			try {
				$con = mysqli_connect(self::$server,self::$user,self::$password, self::$database);
			}
			catch(Exception $e) {
				// do what on an error? Just eat debug?
				Utility::logToFile('unable to connect to database for debug:' . $e->getMessage());
			}
			if ($con) {
				mysqli_query($con,$query);
			}
			else 
				{
				Utility::logToFile('unable to connect to database for debug: no connection returned.');
				}
		}		
	}
	
	private static function logToFile($message) {
		// may make this more sophisticated in the future; for now, just dump to file
		date_default_timezone_set('UTC');
		file_put_contents(self::$debug_filename, date('Y-m-d h:i:sa') . ' ' . $message . "\n", FILE_APPEND);
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
		
		$return = array();
		switch ($listID) {
			case "states":
				$states = array("AK","AL","AZ","CA","CO","CT","DC","DE","FL","GA","HI","ID","IA","IL","IN","KS","KY","LA","MA","MD","ME","MI","MO","MS","NC","ND","NE","NM","NV","NY","OH","OK","OR","PA","RI","SC","SD","TN","TX","UT","VA","WA","WI","WY");
				// for states, we want to use the abbreviation as both display and data value, so create multi
				foreach ($states as $state)
					{
						$return[]= array($state,$state);		
					}
				break;
			case "tenants":
				$query = "select id,name from tenant";
				$result = Database::executeQuery($query);
				while ($r=mysqli_fetch_array($result,MYSQLI_NUM))
				{
					$return[] = $r;
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
				$status_values = array("Active","Closed", "Temporarily Closed", "Unknown");
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
			default:
				echo "unknown list:" . $listID;
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
	
	public static function renderForm($class, $entity, $id, $tenantID, $parentid) {
		
			$fieldarray = $class->getFields();
			$hasImage = false;
			foreach ($fieldarray as $field) {
			    	$value='';
					$required = $class->isRequiredField($field[0]) ? 'required' : '';
					$default_label = '<label class="col-sm-2 control-label" for="txt' . $field[0] . '">' . $class->friendlyName($field[0]) .':</label>';
					if ($id>0 && isset($entity[$field[0]])) {$value = $entity[$field[0]];}
					switch ($field[1]) {
						case "string":	
			        		echo '<div class="form-group">';
							echo $default_label;
			        		echo '	<div class="col-sm-6">';
							if (count($field)>2 && $field[2]>200) {
								echo '     <textarea rows="4" cols="100" id="txt' . $class->getName() . ucfirst($field[0]) . '" name="' . $field[0] . '"  class="form-control" placeholder="'. $field[0] .'" ' . $required . '>';
								echo $value . '</textarea>';
							}
							else {
			        			echo '     <input id="txt' . $class->getName() . ucfirst($field[0]) . '" name="' . $field[0] . '" type="text" class="form-control" placeholder="'. $field[0] .'" value="' . $value . '" ' . $required . '/>';
							}
			        		echo '  </div>';
			        		echo '  <div class="help-block with-errors"></div>';
			        		echo '</div>';
							break;
						case "date":	
			        		echo '<div class="form-group">';
							echo $default_label;
			        		echo '	<div class="col-sm-6"><input id="txt' . $field[0] . '" name="' . $field[0] . '" type="text" class="form-control" placeholder="'. $field[0] .'" value="' . $value . '"/></div>';
							echo '  <div class="help-block with-errors"></div>';
			        		echo '</div>';
							break;
						case "number":
							echo '<div class="form-group">';
							echo $default_label;
							$css_class = $field[2] < 10 ? 'col-sm-1' : 'col-sm-2';
			        		echo '	<div class="' . $css_class . '"><input id="txt' . $class->getName() . ucfirst($field[0]) . '" name="' . $field[0] . '" type="text" class="form-control" placeholder="'. $field[0] . '" value="' . $value . '"/></div>';
							echo '  <div class="help-block with-errors"></div>';
			        		echo '</div>';
							break;
						case "boolean":
							echo '<div class="form-group">';
							echo $default_label;
							$css_class = 'col-sm-1';
							$checked = ($value) ? 'checked' : '';
			        		echo '	<div class="' . $css_class . '"><input id="txt' . $class->getName() . ucfirst($field[0]) . '" name="' . $field[0] . '" type="checkbox" class="form-control" ' . $checked . '/></div>';
							echo '  <div class="help-block with-errors"></div>';
			        		echo '</div>';
							break;
						case "picklist":
							echo '<div class="form-group">';
							echo $default_label;
							$size = $field[2]<4 ? 'col-sm-2' : 'col-sm-6';
			        		echo '<div class="' . $size . '"><select id="txt' . $class->getName() . ucfirst($field[0]) . '" name="' . $field[0] . '" class="form-control">';
							$list = Utility::getList($field[3],$tenantID);
							foreach ($list as $r) {
								$selected = "";
								if ($id>0 && $r[0]==$entity[$field[0]]) {
									$selected = "selected";
									}
 			        			echo '<option value="' . $r[0].'"' . $selected . '>' . $r[1] . '</option>';
			        			}
							echo '</select></div>';
							echo '  <div class="help-block with-errors"></div>';
			        		echo '</div>';
			        		break;
						case "linkedentity":
							echo '<div class="form-group">';
			        		echo $default_label;
			        		echo '<div class="col-sm-6"><select id="' . $field[0] . '" name="' . $field[0] . '" class="form-control">';
							$list = Utility::getList($field[3],$tenantID);
							foreach ($list as $r) {
								$selected = "";
								if (($id>0 && $r[0]==$entity[$field[0]]) || ($id==0 && $r[0]==$parentid)) {
									$selected = "selected";
									}
 			        			echo '<option value="' . $r[0].'"' . $selected . '>' . $r[1] . '</option>';
			        			}
							echo '</select></div>';
							if (isset($field[4])) {
								echo '<a href="#add' . $field[0]  . '" onclick="addSubEntity(\'add' . $field[0]  . '\');">Add New</a>'; 
								}
			        		echo '</span></div>';
			        		break;
						case "image":
							//echo '<div class="row">';
			        		//echo '<span class="label">' . $field[0] . ':</span>';
							//echo '<span class="input"><input id="file' . $field[0] . '" name="file" type="file" placeholder="'. $field[0] .'" value="' . $value . '" readonly></span>';
							echo '<input id="txt' . $field[0] . '" name="' . $field[0] . '" type="hidden" value=""/>';
							//echo '</div>';
							$hasImage = true;
							break;	
						case "viewonly":
							echo '<div class="form-group">';
							echo $default_label;
			        		echo '	<span class="">' . $value . '</span>';
			        		echo '</div>';
							break;
						case "hidden":
						case "childentities":
							// need to render special handling for childentities, differing depending on whether user can add or not when editing parent entity
							echo '<div class="panel panel-info">';
							echo '   <div class="panel-heading"><div class="col-sm-2">'. ucfirst($field[0]) . '</div>';
							$subform='';
							if (!$field[3]) {
								$child_array = $class->getAvailableChildren($field[0],$tenantID);
								$selectName = '';
								$options='';
								foreach($child_array as $c) {
									$options .= '<option value="'. $c['id'] .'">'. $c['name'] .'</option>';
									}
								$selectName = 'add' . $field[0];
								echo '<div class="col-sm-3"><select name="' . $selectName . '" class="form-control">' . $options . '</select></div>';
								echo '&nbsp;<button type="button" class="btn btn-default" onclick="addChildEntity('. $selectName .','. $field[0] .');">';
								echo '<span title="add" class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add</button>';							
								}
							else {
									// add model form to allow user to create new child entities
									//$subform = Utility::renderChildModal($field[2]);
									// This will be handled afterwords so forms don't get nested
									echo '&nbsp;<button type="button" class="btn btn-default" onclick="createChildEntity(\''. $field[2] .'\');">';
									echo '<span title="add" class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add</button>';	
							}	
							echo '</div>';
							echo '   <div class="panel-body">';
							$options='';
							if ($entity && array_key_exists($field[0],$entity)) {
								foreach($entity[$field[0]] as $child) {
									// this assumes all child entities have an id and a name - safe assumption?
									//echo '<div id="' . $field[2] . $child['id'] . '">' . $child['name'];
									// if ($field[4]) { 	
									//	echo '&nbsp;<a href="#" onclick="removeChildRow(\'' . $field[2] . $child['id'] . '\');"><span title="remove" class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>';
									//
									//echo '<input type="hidden" name=' . $field[2] . ' value=" '.  $child['id'] . ' "/>';
									//echo '</div>';
									$options .= '<option value=' . $child['id'] . ' selected>' . $child['name'] . '</option>';
									
								}
							}
							// need to style this
							echo '<select id="' . $field[2] . 'Select" name="' . $field[0] . '" class="form-control" multiple>' . $options . '</select>';
							echo '   </div>';
							echo '</div>';
							break;
						case "custom":
							// call into entity class for its edit field
							echo $class->getCustomEditControl($field[0],$entity[$field[0]],$tenantID);
							break;
						case "properties":
							$prop = array();
							if ($entity && array_key_exists("properties", $entity)) {
								$prop = $entity["properties"];
							}							 
							foreach($class->getPropertyKeys($tenantID) as $key) {
								// find property value matching key
								$value='';
								foreach($prop as $p) {
									if ($p['key']==$key[0]) {
										$value=$p['value'];
										break;
									}
								}
								
								echo '<div class="form-group">';
								echo '<label class="col-sm-2 control-label" for="txtPROP-' . $key[0] . '">' . $key[0] .':</label>';
				        		echo '	<div class="col-sm-6">';
				        		echo '     <input id="txtPROP-' . $key['0'] . '" name="PROP-' . $key[0] . '" type="text" class="form-control" placeholder="'. $key[0] .'" value="' . $value . '"/>';
				        		echo '  </div>';
				        		echo '  <div class="help-block with-errors"></div>';
				        		echo '</div>';
							}
							break;
						default:
							echo '<p>Unknown field type:' . $field[1];
							}
						echo $class->getCustomFormControl($field[0],$tenantID);
			        	}


		}

		public static function renderChildModal($class) {
			
			// determines whether class requires a modal for creating childentities fields and, if so, renders it
			$fieldarray = $class->getFields();
			$requiresModal = false;
			foreach ($fieldarray as $field) {
				if ($field[1]=="childentities" && $field[3]) {
					$requiresModal = true;
					break;
				}
			}
			
			if ($requiresModal) {
	 			$entityName = "child";
	 			$markup = '<div id="' . $entityName . 'EditModal" class="modal fade" role="dialog">';
				$markup .= '  <div class="modal-dialog modal-lg">';
				$markup .= '    <div class="modal-content">';
				$markup .= '    	<div class="modal-header">';
				$markup .= '         <button type="button" class="close" data-dismiss="modal">&times;</button>;';
				$markup .= '         <h4 id="' . $entityName . 'EditHeader" class="modal-title">Modal Header</h4>';
				$markup .= '      </div>';
				$markup .= '      <div id="' . $entityName . 'EditBody" class="modal-body">';
				$markup .= '      	<div id="' . $entityName . 'EditLoading" class="ajaxLoading">';
				$markup .= '      	</div>';
				$markup .= '      	<div id="' . $entityName . 'Container" class="container-fluid">';
				$markup .= '        	<p>Loading information...</p>';
				$markup .= '        </div>';
				$markup .= '      </div>';
				$markup .= '      <div class="modal-footer">';
				$markup .= '		<div id="' . $entityName . 'MessageDiv" class="message hidden">';
				$markup .= '	    	<span id="' . $entityName . 'MessageSpan"><p>Your message here!</p></span>';
				$markup .= '		</div>';
				$markup .= '		<button type="button" class="btn btn-default" onclick="cancelChild();"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cancel</button>';
				$markup .= '      	<button id="' . $entityName . 'EditSaveButton" type="button" class="btn btn-primary" onclick="saveLocation();" disabled>';
				$markup .= '      		<span class="glyphicon glyphicon-save" aria-hidden="true"></span> Save';
				$markup .= '      	</button>';
				$markup .= '      </div>';
				$markup .= '    </div>';			
				$markup .= '  </div>';
				$markup .= '</div>';
			
				echo $markup;
				}
				
		}


	/* 
	 * multi-tenant functions
	 */
	public static function getCSSForTenant($applicationID, $tenantID) {
		
		// does it make sense to hit the database everytime to get CSS?
		// for now, dumbly hardwiring
		$css='static/css/tenant' . $tenantID . '.css';
		
		/*$css = '';
		$query = 'SELECT css FROM application_tenant where applicationid=' . $applicationID . ' AND tenantid=' . $tenantID . ' limit 1;';
		$result = Database::executeQuery($query);
		while ($row = mysqli_fetch_array($result)) {
			$css = $row[0];
		}*/
		return $css;
	}
	
	public static function getTenantProperty($applicationID, $tenantID, $property) {
		
		// does it make sense to hit the database everytime to get these?
		// for now, dumbly hardwiring till we add a cache 
		$value='';
		
		switch ($property) {
			case "title":
				switch ($tenantID) {
					case 3:
						$value='BBQ Hub';
						break;
					default:
					$value="Food Finder";		
				}				
				break;
			case "welcome":
				switch ($tenantID) {
					case 3:
						$value='Welcome to the prototype BBQ Hub!';
						break;
					default:
					$value="Welcome to the prototype Food Finder site.";		
				}				
				break;
			case "finditem":
				switch ($tenantID) {
					case 3:
						$value='barbecue';
						break;
					default:
					$value="food";		
				}				
				break;
		}
		
		return $value;
	}
	
	public static function userAllowed($user,$entityType,$operation,$tenantID) {
		// operation: 'read','write' or 'edit' (same thing), 'add' or 'create', 'delete'
		if (is_null($user)) {
			return false;
		}
		else {
			switch($operation) {
				case 'read':
					return $user->canRead($entityType, $tenantID);
					break;
				case 'write':
				case 'edit':
				case 'update':
					return $user->canEdit($entityType, $tenantID);
					break;
				case 'add':
				case 'create':
					return $user->canAdd($entityType, $tenantID);
					break;
				case 'delete':
					return $user->canDelete($entityType,$tenantID);
					break;
				default:
					return false;
			}
		}
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
	
/* Web display functions */
public static function addDisplayElements($location) {
	
	// adds helping elements to a location or other data set to support web & mobile display
	// $location is an associative array of data 
	
	// format URLs for on-screen display
	if (array_key_exists("url",$location) && strlen($location["url"])>0) {
		// strip http & trailing slash
		$url = $location["url"];
		$url = str_replace("http://","",$url);
		$url = str_replace("https://","",$url);
		if (substr($url,-1)=='/') {
			$url = rtrim($url,'/');
		}
		$location["displayurl"] = $url;
	}

	// add a version of phonenumber that is clickable on devices
	if (array_key_exists("phone",$location) && strlen($location["phone"])>0) {
		// format to remove characters & make clickable
		$phone = $location["phone"];
		$phone = str_replace("(","",$phone);
		$phone = str_replace(")","",$phone);
		$phone = str_replace("-","",$phone);
		$phone = str_replace(" ","",$phone);
		if (substr($phone,1)!='1') {
			$phone = '+1' . $phone;
		}
		$location["clickablephone"] = $phone;
	}
	
	// add a version of phonenumber that is clickable on devices
	if (array_key_exists("uservisits",$location)) {
		// format to remove characters & make clickable
		if ($location["uservisits"]>0) {
			$location["visited"] = 'yes';
		}
	}
	
	return $location;
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