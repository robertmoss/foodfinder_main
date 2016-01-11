<?php
	include_once 'core/dataentity.php';
    include_once 'core/user.php';

	class Location extends DataEntity {
		
		public function getName() {
			return "Location";
		}
		
		public function getFields() {
			$fields = array(
				array("name","string",100),
				array("address","string"),
				array("city","string"),
				array("state","picklist",2,"states"),
				array("phone","string"),
				array("url","string"),
				array("imageurl","string"),
				array("latitude","number",20),
				array("longitude","number",20),
				array("shortdesc","string",1000),
				array("googleReference","string"),
				array("googlePlacesId","string"),
				array("status","picklist",20,"locationStatus"),
				array("properties","properties"),
				array("categories","linkedentities","category",false,true),
				array("links","linkedentities","link",true,true)
				//array("visits","childentities","visit",true,true)
			);
			
			return $fields;
		}
        
        public function getOwner($id) {
                
            $ownerid = -1;
            $ownerkey = "LOCOWN:" . $id;
            // first check cache
            $owner = Cache::getValue($ownerkey);
            if (is_null($owner)) {
                // not cached: try database
                $roles = array();
                $query = "select ownerid from location where id=" . Database::queryNumber($id) . ";";
                 $results = Database::executeQuery($query);
                 if ($arr=mysqli_fetch_array($results)) {
                    $ownerid = $arr[0];
                 }
                 Cache::putValue($ownerkey, $ownerid);
            }
            return $ownerid;
        }
		
		public function isRequiredField($fieldName) {
			// override
			return ($fieldName=='id'||$fieldName=='name');
		}
        
        public function fieldAllowsSingleUpdates($fieldName) {
            // Can use entityService to update imageurl one-off
            return ($fieldName=='imageurl');
        }

		public function isClickableUrl($fieldName) {
			return ($fieldName=='url'||$fieldName=='imageurl');
		}
		
		public function friendlyName($fieldName) {
			$name = parent::friendlyName($fieldName);	
			switch($fieldName)
			{
				case 'imageurl':
					$name="Image URL";
					break;
				case 'shortdesc':
					$name="Short Description";
					break;
				case 'categoryid':
					$name="Category";
					break;
				case 'url':
					$name="Website URL";
					break;
					
			}
			return $name;
		}
		
		public function getEntity($id) {
			// overrides parent class to enrich entity with display elements
			$location = parent::getEntity($id, $this->tenantid, $this->userid);
			$location = Utility::addDisplayElements($location);
			$location["images"] = ''; // blank for now; may want some other value here depending upon how it gets used

			return $location;	
			}
		
		public function getAvailableChildren($fieldname) {
			if ($fieldname=='categories') {
				$query = 'call getCategoriesByType(\'location\',' . $this->tenantid . ');';	
				$data = Database::executeQuery($query);
			
				if ($data->num_rows==0)	{
					return array();
					}
				else {
					while ($r = mysqli_fetch_assoc($data))
						{
						$entities[] = $r;
						}
					return $entities;
				}
			}
			else {
				return parent::getAvailableChildren($fieldname);
			}
		
			return array(); 
		}
    
        public function hasOwner() {
            return true;
        }

        public function addEntity($data) {
            
            // need to have special handling depending upon role of user
            // how inefficient is it to spin up a new user object here? Calling service will already have done just that?
            
            // contributor role users can only add into the pending state (for review by admin)
            $user = new User($this->userid,$this->tenantid);
            if ($user->hasRole('contributor',$this->tenantid)) {
                $data->{"status"}='Pending';    
            }
            
            return parent::addEntity($data);
        }
        
        public function updateEntity($id,$data) {
            
            // need to have special handling depending upon role of user
            // how inefficient is it to spin up a new user object here? Calling service will already have done just that?
            
            // contributor role users can't pending state (for review by admin)
            $user = new User($this->userid,$this->tenantid);
            if ($user->hasRole('contributor',$this->tenantid)) {
                $data->{"status"}='Pending';    
            }
            
            return parent::updateEntity($id,$data);
        }
		
		public function getCustomFormControl($fieldname,$entity) {
			// philosophical question: should we be merging UI with dataentity logic in the same class?
			// potential is to add a UI helper class to put these UI type functions in
			$control = '';	
			if ($fieldname=='name') {
				$control = '<div class="form-group">';
				$control .= '    <div class="col-sm-2 col-sm-offset-2"><button type="button" class="btn btn-primary" onclick="checkGooglePlaces(\'Location\');">Check Google Places</button></div>';
				$control .= '</div>';
			}
			elseif ($fieldname=='longitude') {
				$control = '<div class="form-group">';
				$control .= '    <div class="col-sm-2 col-sm-offset-2"><button type="button" class="btn btn-primary" onclick="lookupLatLng(\'Location\');">Resolve From Address</button></div>';
				$control .= '</div>';
			}
				
			return $control;
		}
		
		public function getJavaScript(){
 			return '<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places&sensor=false"></script>
 			<script type="text/javascript" src="js/imagehandler.js"></script>
 			<script type="text/javascript" src="js/workingPanel.js"></script>
 			<script type="text/javascript" src="js/jquery.form.min.js"></script>
 					';
 		}		
		
		public function getEntitiesQuery($filters,$return,$offset) {
			
			$query='';
			$name=Utility::getRequestVariable('name', '');
			if (strlen($name)>0) {
				$query = "call getLocationsBySearchCriteria(" . $this->tenantid . ", " . Database::queryString($name) . ", " . $return . ", " . $offset . ");";
			}
			else {
				$query = parent::getEntitiesQuery($filters, $return, $offset);	
			}
			
			return $query;
			
		}
		
		protected function getEntityCountQuery($filters) {
			$query='';
			$name=Utility::getRequestVariable('name', '');
			if (strlen($name)>0) {
				$query = "call countLocationsBySearchCriteria(" . $this->tenantid . "," . Database::queryString($name) . ")";
				}
			else {
				$query = parent::getEntityCountQuery($filters);	
			}
			return $query;
		}
		
		public function hasProperties() {
			return true;
		}
		
		public function renderView($entity,$return) {
				
				echo '
					<div>
						<h2>' . $entity["name"] . '</h2>';
				if (array_key_exists('categories', $entity)) {
					echo '		<h3>';
					$separator='';
					foreach($entity['categories'] as $category) {
						echo $separator . '<span class="label label-info">' . $category['name'] . '</span>';
						$separator = ' ';
					}
					echo '</h3>
					';	
					}		
				echo '		<p><span class="list-label">Status: </span>' . $entity['status'] . '</p>
							<div class="locationModalImage"><img id="primaryImage" src="' . $entity['imageurl'] . '" /></div>
							<address> ' . $entity['address'] . '<br/>' . $entity['city'] . ', ' . $entity['state'] . '<br/>';
				if (array_key_exists('displayurl',$entity)) {
					echo '<a href="' . $entity['url'] . '" target="_blank">' . $entity['displayurl'] . '</a><br/>';
				}
				if (array_key_exists('clickablephone',$entity)) {
					echo '<a href="tel:' . $entity['clickablephone'] . '">' . $entity['phone'] . '</a><br/>';
				}
				echo '
							</address>';
						
				if (array_key_exists('properties', $entity)) {
					foreach($entity['properties'] as $prop) {	
						echo '
							<p><span class="list-label">' . $prop['key'] . '</span> ' . $prop['value'] . '</p>';
						}
					}
							
				echo '<div class="panel panel-default">
							<div class="panel-body">' . $entity['shortdesc'] . '</div>
						</div>
				';
				
				
				
				if (array_key_exists('links', $entity)) {
					echo '	<div class="panel panel-info">
							<div class="panel-heading">Read More</div>
							<div class="panel-body">
							';
					
					foreach($entity['links'] as $link) {
						echo '	<p><a href=" ' . $link["url"] . '">' . $link['name'] . '</a></p>';
						echo "\n\t\t\t\t\t\t\t";
					}
					echo '
							</div>
						</div>
					';
				}
				
				// images
				if (array_key_exists("editable", $entity) && $entity["editable"]) {
				    echo '	<div class="panel panel-info">
							<div class="panel-body">
								<div id="imageStrip"><p>Loading . . .</p></div>';
					 include("partials/workingPanel.php");
				    echo '			<form id="uploadForm" action="service/files.php" method="post" enctype="multipart/form-data" role="form">
					        		<input id="imageLocationId" name="locationid" type="hidden" value="'. $entity["id"] . '"/> 
					        		<div class="form-group">
					        			<label for="importFile">Choose files to upload:</label>
					        			<input id="importFile" type="file" name = "importFile[]" multiple="multiple"/>
					        		</div>
									<button id="uploadSubmit" type="button" class="btn btn-default" onclick="uploadForm();"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Upload</button> 
				        		</form>';
                    echo '      </div>
						</div>
							';
                    }        
								
			echo '</div>
				    ';
		
		
				}
				
	 public function userCanAdd($user) {
        // override base to allow contributor role to add
        if (!$user||$user->id==0) {
            return false;
        }
        elseif ($user->id==1 ) {
            // superuser can do anything!
            return true;
        }
        else {
            return ($user->hasRole('admin',$this->tenantid)||$user->hasRole('contributor',$this->tenantid));
        }
    }
    
    public function userCanEdit($id,$user) {
        // different editing rules for location than other entities thanks to Contributor role

       if ($user->id==0) {
           // must be authenticated user to edit 
            return false;
        }
       elseif ($user->id==1) {
           // superuser can always edit 
           return true;
       }
       else {
            // for locations, you can edit if an admin OR if a contributor and you own the location
            return (($user->hasRole('admin',$this->tenantid)) || 
                    (($user->hasRole('contributor',$this->tenantid)) && ($this->userid==$this->getOwner($id)))); 
            }
        }
       
		
	}

		
		
	