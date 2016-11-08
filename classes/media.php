<?php

	include_once dirname(__FILE__) . '/../core/classes/dataentity.php';
        include_once dirname(__FILE__) . '/../core/classes/cdn.php';
	
	class Media extends DataEntity {
	
		public function getName() {
			return 'Media';
		}
		
		public function getPluralName() {
			return 'Media';
		}
		
		public function getFields() {
			$fields = array(
				array("url","string"),
				array("name","string"),
				array("description","string",65000),
				array("public","boolean"),
				array("thumbnailurl","string"),
				array("width","number",20000),
				array("height","number",20000),
				array("metadata","propertybag")
			);
			
			return $fields;
		}
        
        public function getSearchFields() {
            // returns an array of fields for which this entity supports searching by
            // by default, no fields are searchable
            // override to return array of fieldnames that support searching
            $searchFields = array('name','description');
            return $searchFields;
        }

        
        public function friendlyName($fieldName) {
           if ($fieldName=='thumbnailurl') {
               return 'Thumbnail Url';
           }
           else {
            return ucfirst($fieldName);
            }
        }
		
		public function isRequiredField($fieldName) {
			// override
			return ($fieldName=='name'||$fieldName=='url');
		}
        
        		
		public function hasOwner() {
			return true;
		}
		
		public function linkMediaToLocation($mediaid,$locationid) {
			$query = "call AddLocationMedia(" .
				Database::queryNumber($mediaid) .
				"," . Database::queryNumber($locationid) .
				"," . Database::queryNumber($this->tenantid) .
				"," . Database::queryNumber($this->userid) . ");";
			Database::executeQuery($query);
		}
        
        protected function getEntityCountQuery($filters) {
                
            if (isset($filters["locationid"])) {
                $query = 'select count(*) from locationMedia where locationid=' . Database::queryNumber($filters["locationid"]) . ';';
            }
            else {    
                $name=null;
                $description=null;
                
                if (isset($filters["name"])) {
                        $name=$filters["name"];
                    }
                if (isset($filters["description"])) {
                        $description=$filters["description"];
                    }
                $query = "call getMediaItemsCountEx(" .
                        Database::queryString($name) . "," .
                        Database::queryString($description) . "," .
                        Database::queryNumber($this->tenantid) . "," .
                        Database::queryNumber($this->userid) . ");";
            }        
            return $query;
        }
		
		protected function getEntitiesQuery($filters, $return, $offset) {
						
			$name=null;
            $description=null;
			if (isset($filters["locationid"])) {
				$query = "call getMediaByLocationId(" .
					Database::queryNumber($filters["locationid"]) . "," .
					Database::queryNumber($this->tenantid) . "," .
					Database::queryNumber($this->userid) . ");";
				
				return $query;
			}
 			else {
 			    if (isset($filters["name"])) {
                    $name=$filters["name"];
                }
                if (isset($filters["description"])) {
                    $description=$filters["description"];
                }
				$query = "call getMediaItemsEx(" .
				    Database::queryString($name) . "," .
                    Database::queryString($description) . "," .
                    Database::queryNumber($this->tenantid) . "," .
                    Database::queryNumber($this->userid) . "," .
                    Database::queryNumber($return) . "," .
                    Database::queryNumber($offset) . ");";
                
                return $query;
			}
			
	
		}

		
        
        public function addEntity($data) {
            // override on new save to set default metadata
            Log::debug('saving media ',5);
            if (!key_exists('metadata',$data)) {
                // no object at all, so create
                $data->{'metadata'}=new stdClass();
            }
            $arr = $data->{'metadata'};
            if (!key_exists("caption",$arr)) {
                $arr->{'caption'}='';
            }
            if (!key_exists("credit",$arr)) {
                $arr->{'credit'}='';
            }
            $data->{'metadata'} = $arr;
            return parent::addEntity($data);   
        }
        
        public function deleteEntity($id) {
            // need special handling for media entities because we must manage the CDN, too
            $entity = $this->getEntity($id);
            if (!is_null($entity["url"])||strlen($entity["url"])>0) {
                $cdn = new Config::$cdn_classname($this->userid,$this->tenantid);
                try {
                    $result = $cdn->removeContent($entity["url"]);
                }
                catch(Exception $ex) {
                    // may want to offer alternate handling in future, but for now if we can't delete from CDN we don't delete
                    // from data store
                    throw new Exception('Unable to delete media: ' . $ex->getMessage());
                }
            }
            if (!is_null($entity["thumbnailurl"])||strlen($entity["thumbnailurl"])>0) {
                $cdn = new Config::$cdn_classname($this->userid,$this->tenantid);
                try {
                    $result = $cdn->removeContent($entity["thumbnailurl"]);
                }
                catch(Exception $ex) {
                    // for now: do nothing. Can assume there may not be a thumbnail for all media
                }
            }
            return parent::deleteEntity($id);
            
            
        } 
        
       
	
	}
	