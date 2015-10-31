<?php

interface iDataEntity {
	
	public function getName();
	public function getPluralName();
	public function getDataServiceURL();
	public function getFields();
	public function getEntity($id,$tenantid,$userid);
	public function getEntities($tenantid, $filters, $userid, $return, $offset);
	public function validateData($data);
	public function addEntity($data,$tenantid,$userid);
	public function updateEntity($id,$data,$tenantid,$userid);
	public function renderView($entity,$userid,$returnurl);
	public function getJavaScript();
	public function getCustomValue($fieldname,$currentvalue,$operationtype);
	public function getCustomEditControl($fieldname,$currentvalue,$tenantID);
	public function getCustomFormControl($fieldname, $tenantid);
	public function getEntityCount($tenantid, $filters, $userid);
	public function hasProperties();
	public function hasOwner();
	public function getPropertyKeys($tenantid);
	
}

abstract class DataEntity implements iDataEntity {
				
		public abstract function getName();
		
		// should return an array defining available fields on the object
		public abstract function getFields();
		/* returns array with this format
		 * 	[0] name: name of the field
		 *  [1] field type: string, number, date, viewonly, picklist, linkedentity, childentities, custom
		 * 			string: regular varchar/text field
		 * 			number: integer or decimal number
		 * 			date: date
		 *       boolean: a true/false value (1=true/0=false)
		 * 			viewonly: won't be rendered on edit forms or submitted to database on insert/update
		 * 			picklist: pick from a static list of values, using Utility object's getList method
		 * 		linkedentity:
		 * 	    childentities: children to this entity which should be displayed in list
		 * 			           custom: handling of field is deferred to the entity subclass for special treatment 
		 *  		   image: an image file that gets uploaded to content server with url stored in database
		 *        properties: a dummy placeholder that lets you specify where on forms to place user-defined properties
		 * 
		 *  [2] max length: maxium length of the field (in characters for text, digits for numbers)
		 *         0 or not set indictes no max
		 *  [3+] info varies by field type:
		 * 		picklist: [3] - name of list to choose values from (as found in Utility::getList method )
		 * 				  [4] - (optional) boolean indicating whether to show an "add new button" next to pick list to add new items
		 * 		linkedentity:
		 * 				  [3] - same as picklist #3
		 * 				  [4] - same as picklist #4
		 * 				  [5] - (optional) the class name of the linked entity
		 * 				  [6] - (optional) index of the hidden or viewonly field in the field array to be used to as label for the linked entity 
		 * 		childentities
		 * 				  [2] - name of the childentity
		 * 				  [3] - true/false: whether user should be allowed to dynamically add new childentities (if false, user can only select from a defined list)
		 * 				  [4] - true/false: whether user should be able to delete childentities			  	
		 */

		public function getPluralName() {
			// overrride for classes with funky plurals
			return $this->getName() . 's';
		}
		
		
		public function getDataServiceURL() {
			return "service/entityService.php?type=" . strtolower($this->getName());
		}
		
		public function getJavaScript() {
			// if your class needs custom javascript to handle view elements, override and return a script tag containing the reference to it
			// by default, no script tags are rendered
			
			return '';
		}
		
		public function isRequiredField($fieldName) {
			// override to specify what fields are required for the child object
			// by default, id is only required field
			return ($fieldName=='id');
		}
		
		public function friendlyName($fieldName) {
			// override if you want to to have a more human-readble name for one or more fields
			return ucfirst($fieldName);
		}
		
		public function getEntity($id, $tenantid, $userid) {
			// returns an object graph representing the entity
			
			$query = $this->getEntityQuery($id,$tenantid,$userid);
			
			$data = Database::executeQuery($query);
			$entity = '';
			
			//echo '<p>count= ' . $data->field_count .'</p>';
			
			if ($data->num_rows==0)	{
				//no match found.
				//throw new Exception($this->getName() . ' not found.');
				return array();
			}
			
			while ($r = mysqli_fetch_assoc($data))
				{
				$entity = $r;
				}
			
			// add user-defined properties, if supported
			if ($this->hasProperties()) {
				$query = $this->getPropertyQuery($id,$tenantid);			
				$data = Database::executeQuery($query);
				$properties = array();
				while ($r = mysqli_fetch_assoc($data))
					{
					$properties[] = $r;
					}
				if (count($properties)>0) {
					$entity["properties"] = $properties;
				}
			}
				
			// query child entities, if any exist
			$fieldarray = $this->getFields();
			$separator = "";
			foreach ($fieldarray as $field) {
				if ($field[1]=='childentities') {
					// add .
					$query = "call get" . ucfirst($field[0]) . "By" . $this->getName() . "ID(" . $id . "," . $tenantid . "," . $userid . ")";
					$data = Database::executeQuery($query);
					if ($data->num_rows>0) {
						$subs = array();
						while ($r = mysqli_fetch_assoc($data)) {
							$subs[] = $r;
						}				
						if (count($subs>0)) {
							$entity[$field[0]] = $subs;
							}
					}		
				}
			} 
			
			
			
			return $entity;			 
		}

		public function getEntities($tenantid, $filters, $userid, $return, $offset) {
			// base class doesn't know enough handle filters in a sophisticated way
			// must override if you want filter capability or to return child entities
			// base class simply does an unfiltered query with start and offset
			// 
			// $return:	number of entities to return; defaults to 50
			// offset:	number to start at (i.e. skip)
			if (is_null($return)||$return<=0) {
				$return = 50;
			}
			$query = $this->getEntitiesQuery($tenantid,$filters,$userid,$return,$offset);
			
						
			$data = Database::executeQuery($query);
			$entity = '';
			
			
			if ($data->num_rows==0)	{
				//no match found.
				//throw new Exception($this->getName() . ' not found.');
				return array();
			}
			
			while ($r = mysqli_fetch_assoc($data))
				{
				$entities[] = $r;
				}
			
			return $entities;		
		}

		protected function getEntityQuery($id, $tenantid, $userid) {
			// returns the SQL query used to retrieve multiple entities.
			// by default, all data entities should have a GET stored procedure named get<Entity>ById with params id, userid and tenantid
			// Override if you wish to have a non-standard stored proc or query
			$query = 'call get' . $this->getName() . 'ById(' . Database::queryNumber($id) . ', ' . Database::queryNumber($tenantid). ', ' . Database::queryNumber($userid) . ');';
			return $query;
		}
		
		protected function getPropertyQuery($id,$tenantid) {
			$query = 'call getPropertiesBy' . $this->getName() . 'Id(' . Database::queryNumber($id) . ', ' . Database::queryNumber($tenantid). ');';
			return $query;
		}

		protected function getEntitiesQuery($tenantid, $filters, $userid, $return, $offset) {
			// returns the SQL query used to retrieve multiple entities.
			// Override if you wish to have a non-standard stored proc or query
			$query = 'call get' . $this->getPluralName() . '(' . Database::queryNumber($userid) . ', ' . Database::queryNumber($return). ', ' . Database::queryNumber($offset) . ', ' . Database::queryNumber($tenantid) . ');';				
			return $query;
		}
		
		public function getEntityCount($tenantid, $filters, $userid) {
			// returns the total number of entities matching specified filter
			// assumes table has same name as entity and has a tenantid column
			// currently, the base class isn't smart enough to know how to filter entities, so it returns a count of all entities
			// override if you need different/smarter behavior (or just overwrite getEntityCountQuery method)
			
			// 
			$query=$this->getEntityCountQuery($tenantid, $filters, $userid);
			$data = Database::executeQuery($query);
			
			if ($data->num_rows==0)	{
				//no match found.
				//throw new Exception($this->getName() . ' not found.');
				return 0;
			}
			else {
				$r = mysqli_fetch_row($data);
				return $r[0];
			}				
		}
		
		protected function getEntityCountQuery($tenantid, $filters, $userid) {
			$query = 'select count(*) from ' . strtolower($this->getName()) . ' where tenantid=' . $tenantid;
			return $query;
		}
		
		public function validateData($data) {
			// takes an object graph as input
			// override to add your own validation.
			
			// evaluate required fields
			
			Utility::debug('dataentity.validateData called',9);
			
			$fieldarray = $this->getFields();	
			foreach ($fieldarray as $field) {
				Utility::debug('Validating ' . $field[0],9);
				if (!property_exists($data,$field[0])||$data->{$field[0]}=='') {
					if ($this->isRequiredField($field[0])) {
						throw new Exception($field[0] . ' is required.');
						}
					}
			}
			Utility::debug('dataentity.validateData validated successfully.',9);
			return true;
		}
		
		public function addEntity($data,$tenantid,$userid) {
			
			// this does a very basic add based upon common pattern
			// override to add custom save functionality
			$this->validateData($data);
			
			$newID = 0;
			$query = "call add" . $this->getName() . "(";
			$fieldarray = $this->getFields();
			$followOnQueries = array();
			$separator = "";
			foreach ($fieldarray as $field) {
				$value = '';
				if (!property_exists($data,$field[0])) {
					if ($this->isRequiredField($field[0])) {
						throw new Exception($field[0] . 'is required.');
						}
					}
				else {
					$value = $data->{$field[0]}; 		
					}
				switch ($field[1]) {
					case "string":
						$query .= $separator . Database::queryString($value);
						break;
					case "date":
						$query .= $separator . Database::queryDate($value);
						break;
					case "number":
						$query .= $separator . Database::queryNumber($value);
						break;
					case "boolean":
						$query .= $separator . Database::queryBoolean($value);
						break;
					case "picklist":
						$query .= $separator . Database::queryString($value);
						break;
					case "linkedentity":
						$query .= $separator . Database::queryNumber($value);
						break;
					case "childentities":
						Utility::Debug('Childentity mark 1',1);
						if (is_array($data->{$field[0]})) {
							Utility::Debug('Childentity mark 2' . $field[0] ,1);
							$children = $data->{$field[0]};
	 						foreach ($children as $c) {
	 							Utility::Debug('Childentity mark 3' . $c->id,1);
								$procname = $this->getAddChildProcName($field[2]);
								array_push($followOnQueries,'call ' . $procname . '([[ID]],' . $c->id . ',' . $tenantid . ');');
							}
						}
						break;
					
					case "custom":
						$query .= $separator . $this->getCustomValue($field[0],$data->{$field[0]},'add');
					}
					$separator = ", ";
					}
			// assume tenantid is always needed
			$query .= $separator . Database::queryNumber($tenantid);
			$separator = ", ";
			
			// add userid if object hasOwner 
			if ($this->hasOwner()) {
				$query .= $separator . Database::queryNumber($userid);
			}
			$query .= ')';
			
			
			$result = Database::executeQuery($query);
			
			if (!$result) {
				return false;
			}
			else 
			{
				while ($r = mysqli_fetch_array($result))
					{
					$newID=$r[0];
					}
			}
			
			// next, handle user-defined properties
			if ($this->hasProperties()) {
				// get array of properties configured for this entity & tenant
				$keys = $this->getPropertyKeys($tenantid);
				foreach($keys as $key) {
					if (property_exists($data,'PROP-' . $key[0])) {
							// only save if not empty - that's the MO for now
							$val =  $data->{'PROP-'.$key[0]};
							if (strlen($val)>0) {
								$this->saveProperty($newID, $key[0], $data->{'PROP-'.$key[0]});
							}
						}
				}
			}
			
			// finally, execute follow-on queries to add child entities		
			foreach($followOnQueries as $q) {
					// replace ID placeholder with new ID now that entity is saved
					$q2 = str_replace('[[ID]]',$newID,$q);
					$result = Database::executeQuery($q2);
				}
			
			return $newID;
		
		}
		
		public function updateEntity($id,$data,$tenantid,$userid) {
			
			// this does a very basic update based upon common pattern
			// override to add custom save functionality
			
			// TO-DO: Need to transactionalize this due to the multiple possible queries
			
			Utility::debug('dataentity.updateEntity called',9);
			
			$this->validateData($data);
			
			$newID = 0;
			$query = "call update" . $this->getName() . "(" . $id;
			$followOnQueries = array();
			$fieldarray = $this->getFields();
			$separator = ",";
			foreach ($fieldarray as $field) {
				switch ($field[1]) {
					case "string":
						$query .= $separator . Database::queryString($data->{$field[0]});
						break;
					case "number":
						$query .= $separator . Database::queryNumber($data->{$field[0]});
						break;
					case "date":
						$query .= $separator . Database::queryDate($data->{$field[0]});
						break;
					case "picklist":
						$query .= $separator . Database::queryString($data->{$field[0]});
						break;
					case "linkedentity":
						$query .= $separator . Database::queryNumber($data->{$field[0]});
						break;
					case "childentities":
						$procname = $this->getRemoveChildrenProcName($field[0]);
						array_push($followOnQueries,'call ' . $procname . '('. $id . ',' . $tenantid . ');');
						if (is_array($data->{$field[0]})) {
							$children = $data->{$field[0]};
	 						foreach ($children as $c) {
								$procname = $this->getAddChildProcName($field[2]);
								array_push($followOnQueries,'call ' . $procname . '('. $id . ',' . $c->id . ',' . $tenantid . ');');
							}
						}
						break;
					case "custom":
						$query .= $separator . $this->getCustomValue($field[0],$data->{$field[0]},'update');
					}
					$separator = ", ";
					}
			// assume tenantid is always needed and is last parameter (or 2nd to last if user required)
			$query .= $separator . Database::queryNumber($tenantid);
			$separator = ", ";
			
			// add userid if object hasOwner 
			if ($this->hasOwner()) {
				$query .= $separator . Database::queryNumber($userid);
			}
			
			$query .= ')';
			
			$result = Database::executeQuery($query);
			
			if (!$result) {
				return false;
			}
			else 
			{
				// handle user-defined properties
				if ($this->hasProperties()) {
					// remove all properties for object - if not specified in the data, assume it's not longer a valid property
					$this->deleteProperties($id);
						
					// get array of properties allowed for this entity & tenant
					$keys = $this->getPropertyKeys($tenantid);
					foreach($keys as $key) {
						// determine whether data contains a value for this key - field will be prepended with PROP
						if (property_exists($data,'PROP-' . $key[0])) {
							// only save if not empty - that's the MO for now
							$val =  $data->{'PROP-'.$key[0]};
							if (strlen($val)>0) {
								$this->saveProperty($id, $key[0], $data->{'PROP-'.$key[0]});
							}
						}	
					}
				}
					
				// execute follow-one queries for child entities
				foreach($followOnQueries as $q) {
					$result = Database::executeQuery($q);
				}
				
				return true;				
			}
		
		}

		public function getRemoveChildrenProcName($childentityname) {
			// override if your class has a different name for the proc that removes all linked child entities
			$proc = 'remove' . ucfirst($this->getName()) . ucfirst($childentityname); 
			return $proc;
		}
		
		public function getAddChildProcName($childsinglename) {
			// override if your class has a different name for the proc that adds a linked child entity
			$proc = 'add' . ucfirst($this->getName()) . ucfirst($childsinglename); 
			return $proc;
		}

		public function deleteEntity($id,$userid,$tenantid) {
			
			// this does a very basic delete based upon common pattern
			// override to add custom delete functionality
			
			$query = "call delete" . $this->getName() . "(" . $id;
			// assume tenantid is always needed and is last parameter
			$query .= ',' . Database::queryNumber($tenantid);
			$query .= ')';
			
			$result = Database::executeQuery($query);
			
			if (!$result) {
				return false;
			}
			else 
			{
				return true;				
			}
		
		}

		public function getCustomValue($fieldname, $currentvalue, $operationtype) {
			// operationtype is add, update, etc.
			// override to tell the dataentity to use a value for this field 
			// other than that submitted to the web service
			return $currentvalue;
		}
		
		public function getAvailableChildren($fieldname,$tenantid) {
			// for childentities type fields, override to return a list of eligible child entities that can be linked to this object
			// if no list is returned (i.e. empty array), controlling code will assume children cannot be added
			// no need to override if you don't want to specify a specific set of allowable children
			return array(); 
		}
		
		
		// produces a very basic display of an entity's fields. Override to create your
		// own stylized views
		public function renderView ($entity,$userid,$returnurl) {
						
			$fieldarray = $this->getFields();
			$entityid = $entity["id"];
			$name = 'entity';
			
			foreach ($fieldarray as $field) {
				if ($field[0]=="name") {
					$name = $entity[$field[0]];
					echo "<h1>" . $name . "</h1>";
				}
				elseif ($field[1]=="linkedentity") {
					// do nothing. supress linkedentities: these are ids, and view json should show viewonly labels 
				}
				elseif ($field[1]=="childentities") {
					echo '<div class="subentity">';	
					echo "<h2>" . ucfirst($field[0]) . ": </h2>";
					if (isset($entity[$field[0]])) {
						$childarray = $entity[$field[0]];
						foreach ($childarray as $child) {
							echo '<p><a href="entityPage.php?type=' . $field[2] . '&mode=view&id=' . $child["id"] . '">' . $child["name"] . '</a></p>';
						}
					}
					if (isset($field[3]) && $field[3]) {
						echo '<p><input type="button" class="btn" value="Add New ' . ucfirst($field[2]) . '" onclick="document.location=\'entityPage.php?type=' . $field[2] . '&mode=edit&id=0&parentid=' . $entityid  . '\';"></p>';	
						}
					echo '</div>';
				}
				else {
					echo "<p>" . ucfirst($field[0]) . ": " . $entity[$field[0]] . "</p>";
				}
			}

			
			// render standard button set
			echo '<div class="functions">';
			echo '	<input class="btn" type="button" value="Back " onclick="history.back();" />';
			echo '	<input class="btn" type="button" value="Edit" onclick="setMode(\'edit\');" />';
			echo '</div>';
		}

		public function getCustomEditControl($fieldname, $currentvalue, $tenantID) {
			// type is add, update, etc.
			// override to tell the dataentity the value to use for this field
			return '<p>Custom edit field for ' . $fieldname . ' not defined: ' . $currentvalue . '</p>';
		}
		
		public function getCustomFormControl($fieldname, $tenantid) {
			// by default does nothing
			// if you need a custom control for you entity (e.g. the Google Places lookup for Locations)
			// override this method and return the markup for the control, which will be rendered immediately after the default form fields
			return '';
		}
		
		public function hasProperties() {
			// override and return true if you wish your object to support user-definable properties
			// if you do, it is assumed there is a table called [entityname]Property with columns id, [entityname], key, value
			// to hold your properties
			// default is false
			return false;
		}
		
		public function getPropertyKeys($tenantid) {
			// return an array of the user-defined property keys allowed for this tenant for this entity
			// by default will assume we can query based on entity name; override if you need special handling
			$query = 'call getTenantPropertiesByEntity(' . $tenantid . ',' . Database::queryString($this->getName()) . ')';
			$result = Database::executeQuery($query);
			
			$keys=array();
			while ($r = mysqli_fetch_row($result))
				{
				$keys[] = $r;
				}
			return $keys;
			}
		
		protected function deleteProperties($id) {
			// assumes a pattern to property tables; only override if your object stores properties differently
			$tablename = lcfirst($this->getName()) . 'Property';

			$query = 'delete from ' . $tablename . ' where id in (';
			$query .= ' select * from (select T.id from ' . $tablename . ' T where';
			$query .= ' T.' . lcfirst($this->getName()) . 'id=' . Database::queryNumber($id) . ') as list);';

			return Database::executeQuery($query);

		}
		
		function saveProperty($id,$key,$value) {
			
			Utility::debug('saving property ' . $key . "=" . $value,2);
				
			$tablename = lcfirst($this->getName()) . 'Property';
			$idname = lcfirst($this->getName()) . 'id';

			// key is a reserved word, making this a bit of a pain (hence appendeding table name)
			$query = 'insert into ' . $tablename . ' (' . $idname . ',' . $tablename . '.key,value)';
			$query .= ' values (' . Database::queryNumber($id);
			$query .= ', ' . Database::queryString($key);
			$query .= ', ' . Database::queryString($value) . ');';

			return Database::executeQuery($query);	
		}
		
		public function hasOwner() {
			// override and return true if you wish your object to be ownable by a user
			// if true, userid will be passed to all add/update/get statements
			// default is false
			return false;
		}

}