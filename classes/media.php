<?php

	include_once dirname(__FILE__) . '/../core/classes/dataentity.php';
	
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
		
		protected function getEntitiesQuery($filters, $return, $offset) {
			// overrides base to implement location-based filtering
			// right now, can't get media without some sort of filter
			
			if (isset($filters["locationid"])) {
				$query = "call getMediaByLocationId(" .
					Database::queryNumber($filters["locationid"]) . "," .
					Database::queryNumber($this->tenantid) . "," .
					Database::queryNumber($this->userid) . ");";
				
				return $query;
			}
			else {
				$query = "call getMediaItems(" .
                    Database::queryNumber($this->tenantid) . "," .
                    Database::queryNumber($this->userid) . "," .
                    Database::queryNumber($return) . "," .
                    Database::queryNumber($offset) . ");";
                
                return $query;
			}
			
	
		}

		public function getEntityCountQuery($filters) {
			if (isset($filters["locationid"])) {
				$query = 'select count(*) from locationMedia where locationid=' . Database::queryNumber($filters["locationid"]) . ';';
			     }
			else {
                $query = 'select count(*) from media where tenantid=' . Database::queryNumber($this->tenantid) . ';';
			}
            return $query;
            
		}
        
       
	
	}
	