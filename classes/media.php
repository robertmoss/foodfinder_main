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
				array("metadata","json"),
				array("public","boolean")
			);
			
			return $fields;
		}
		
		public function isRequiredField($fieldName) {
			// override
			return ($fieldName=='name'||$fieldName=='url');
		}
        
         public function isUpdatableField($fieldName) {
            // and updatable field can be set on a new entity but after that cannot be updated through the API
            // override and return true for any field needing such handling
            return ($fieldName!='url');
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
				throw new Exception('To retrieve media a filter must be set.');
			}
			
	
		}

		public function getEntityCountQuery($filters) {
			if (isset($filters["locationid"])) {
				$query = 'select count(*) from locationMedia where locationid=' . Database::queryNumber($filters["locationid"]) . ';';
				return $query;
			}
			else {
				throw new Exception('To retrieve media a filter must be set.');
			}
		}
	
	}
	