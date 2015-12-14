<?php

include_once 'database.php';
include_once 'config.php';
include_once 'log.php';
include_once 'cache.php';
include_once 'tenant.php';

class Utility{
		
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
                // in the future, may want to load dynamically from database, but right now this is a 
                // known list and can just be hardwired
                $return = array("standard","admin");
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
					if ($id>0 && isset($entity[$field[0]])) {$value = $entity[$field[0]];}
					$required = $class->isRequiredField($field[0]) ? 'required' : '';
                    $readonly='';
                    if (!$class->isUpdatableField($field[0])) {
                        $readonly = ' readonly';
                    } 
                    
					$default_label = '<label class="col-sm-4 control-label" for="txt' . $field[0] . '">' . $class->friendlyName($field[0]) .':</label>';
					if ($class->isClickableUrl($field[0])) {
						// add link to label
						$url = 'getElementById(\'txt' . $class->getName() . ucfirst($field[0]) . '\').value';
						$default_label ='<a onclick="window.open('. $url . ');" target="_blank">' . $default_label . '</a>';						
					}
					
					switch ($field[1]) {
						case "string":	
			        		echo '<div class="form-group">';
							$maxlen = '';
							if (count($field)>2) {
								// add a max-length validator
								$maxlen = 'maxlength="' . $field[2] . '"';
							}
							echo $default_label;
			        		echo '	<div class="col-sm-6">';
                            if (count($field)>2 && $field[2]>200) {
    							echo '     <textarea rows="4" cols="100" id="txt' . $class->getName() . ucfirst($field[0]) . '" name="' . $field[0] . '"  class="form-control" placeholder="'. $field[0] .'" ' . $maxlen . ' ' . $required . '>';
    							echo $value . '</textarea>';
    							}
    						else {
    			        		echo '     <input id="txt' . $class->getName() . ucfirst($field[0]) . '" name="' . $field[0] . '" type="text" class="form-control" placeholder="'. $field[0] .'" value="' . $value . '" ' . $maxlen . ' ' . $required .  $readonly . '/>';
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
							$list = Utility::getList($field[3],$tenantID,0);
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
                            if ($class->isParentId($field[0])) {
                                $value = $parentid;
                            }
                            echo '<input type="hidden" id="txt' . $field[0] . '" name="' . $field[0] . '" value="' . $value . '"/>';
                            break;
						case "childentities":
							echo '<div class="panel panel-info">';
							echo '   <div class="panel-heading"><div class="col-sm-2">'. ucfirst($field[0]) . '</div>';
							$subform='';
							echo '&nbsp;<button type="button" class="btn btn-default" onclick="createChildEntity(\''. $field[2] .'\');">';
                            echo '<span title="add" class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add</button>';	
							echo '</div>';
							echo '   <div class="panel-body">';
							//$options='';
                            $rows='';
							if ($entity && array_key_exists($field[0],$entity)) {
								foreach($entity[$field[0]] as $child) {
									// this assumes all child entities have an id and a name - safe assumption?
									//$options .= '<option value=' . $child['id'] . ' selected>' . $child['name'] . '</option>';
									$rows .= '<tr>
									               <td><div class="user"><span class="description">' . $child['name'] . '</span></div></td>
									               <td><div class="btn-group btn-group-sm" role="group" aria-label="...">
									                       <button type="button" class="btn btn-default" onclick="editChildEntity(\'' . $field[2] .  '\',' . $child['id'] . ');"><span class="glyphicon glyphicon-pencil"></span>&nbsp;</button>
									                       <button type="button" class="btn btn-default" onclick="deleteChildEntity(\'' . $field[2] .  '\',' . $child['id'] . ');"><span class="glyphicon glyphicon-remove"></span>&nbsp;</button>
									                       <div id="workingDelete'.$child['id']. '" class="hidden"></div> 
									                   </div></td></tr>'; 
								}
							}
                            echo '<table class="table table-striped table-hover table-responsive">';
                            //echo '<thead><tr><th>Name</th><th>Actions</th></tr></thead>';
                            echo '<tbody>' . $rows;
                            echo '</tbody></table>';

                            echo '   </div>';
                            echo '</div>';
                            break;
                        case "linkedentities":
                            // need to render special handling for linked entities, differing depending on whether user can add or not when editing parent entity
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
                                    $options .= '<option value=' . $child['id'] . ' selected>' . $child['name'] . '</option>';
                                }
                            }
                            echo '      <select id="' . $field[2] . 'Select" name="' . $field[0] . '" class="form-control" multiple>' . $options . '</select>';
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
						echo $class->getCustomFormControl($field[0],$tenantID,$entity);
			        	}


		}

		public static function renderMultifileUpload($url,$prompt,$buttonText) {
				
			echo '<form action="' . $url .'" method="post" enctype="multipart/form-data">';
  			echo $prompt;
  			echo '<input name="userfile[]" type="file" multiple/><br />';
  			echo '<input type="submit" value="' . $buttonText . '" />';
			echo '</form>';
			
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
	
	// not sure if this is kludgey or elegant: but, we are treating the static properties and dynamically-definable settings
	// as the same thing here - just key/value pairs. All tenants have some keys, other keys may or may not be defined for a tenant
	public static function getTenantProperty($applicationID, $tenantID, $userID, $property) {
	    
        Log::debug('retrieving tenant property ' . $property . " for tenant ID=" . $tenantID, 1);
        
		$class = new Tenant($userID,$tenantID);
        $key = $applicationID . ":" . $tenantID . ":" . $property;
        $value = Cache::getValue($key); 
        $query='';       
		if (!$value) {
		    // cache miss. Need to retrieve from database
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
                    Cache::putValue($key,$value);
                    }                
                }   
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
					return $user->canRead($entityType, $tenantID,0);
					break;
				case 'write':
				case 'edit':
				case 'update':
					return $user->canEdit($entityType, $tenantID,0);
					break;
				case 'add':
				case 'create':
					return $user->canAdd($entityType, $tenantID);
					break;
				case 'delete':
					return $user->canDelete($entityType,$tenantID,0);
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
