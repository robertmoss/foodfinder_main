<?php
	include_once 'core/dataentity.php';

	class Location extends DataEntity {
		
		public function getName() {
			return "Location";
		}
		
		public function getFields() {
			$fields = array(
				array("name","string"),
				array("address","string"),
				array("city","string"),
				array("state","picklist",2,"states"),
				array("phone","string"),
				array("url","string"),
				array("imageurl","string"),
				array("latitude","number",20),
				array("longitude","number",20),
				array("shortdesc","string",500),
				array("googleReference","string"),
				array("googlePlacesId","string"),
				array("status","picklist",20,"locationStatus"),
				array("properties","properties"),
				array("categories","childentities","category",false,true),
				array("links","childentities","link",true,true)
				//array("visits","childentities","visit",true,true)
			);
			
			return $fields;
		}
		
		public function isRequiredField($fieldName) {
			// override
			return ($fieldName=='id'||$fieldName=='name');
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
		
		public function getEntity($id, $tenantid, $userid) {
			// overrides parent class to enrich entity with display elements
			$location = parent::getEntity($id, $tenantid, $userid);
			$location = Utility::addDisplayElements($location);
			
			return $location;	
			}
		
		public function getAvailableChildren($fieldname,$tenantid) {
			if ($fieldname=='categories') {
				$query = 'call getCategoriesByType(\'location\',' . $tenantid . ');';	
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
		
		public function getCustomFormControl($fieldname,$tenantid) {
			// philosophical question: should we be merging UI with dataentity logic in the same class?
			// potential is to add a UI helper class to put these UI type functions in
			$control = '';	
			if ($fieldname=='name') {
				$control = '<div class="form-group">';
				$control .= '    <div class="col-sm-2 col-sm-offset-2"><button type="button" class="btn btn-info" onclick="checkGooglePlaces(\'Location\');">Check Google Places</button></div>';
				$control .= '</div>';
			}
			elseif ($fieldname=='longitude') {
				$control = '<div class="form-group">';
				$control .= '    <div class="col-sm-2 col-sm-offset-2"><button type="button" class="btn btn-info" onclick="lookupLatLng(\'Location\');">Resolve From Address</button></div>';
				$control .= '</div>';
			}
				
			return $control;
		}
		
		public function getJavaScript(){
 			return '		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places&sensor=false"></script>';
 		}		
		
		public function getEntitiesQuery($tenantid,$filters,$userid,$return,$offset) {
			
			$query='';
			$name=Utility::getRequestVariable('name', '');
			if (strlen($name)>0) {
				$query = "call getLocationsBySearchCriteria(" . $tenantid . ", " . Database::queryString($name) . ", " . $return . ", " . $offset . ");";
			}
			else {
				$query = parent::getEntitiesQuery($tenantid, $filters, $userid, $return, $offset);	
			}
			
			return $query;
			
		}
		
		protected function getEntityCountQuery($tenantid, $filters, $userid) {
			$query='';
			$name=Utility::getRequestVariable('name', '');
			if (strlen($name)>0) {
				$query = "call countLocationsBySearchCriteria(" . $tenantid . "," . Database::queryString($name) . ")";
				}
			else {
				$query = parent::getEntityCountQuery($tenantid, $filters, $userid);	
			}
			return $query;
		}
		
		public function hasProperties() {
			return true;
		}
		
		public function renderView($entity,$userid,$return) {
				
				echo '
					<div>
						<h2>' . $entity["name"] . '</h2>
						<div id="locationid" class="hidden">'. $entity["id"] . '</div>';
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
							<img src="' . $entity['imageurl'] . '" />
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
								
			echo '</div>
				    ';
		
		
				}
		
	}

		
		
	