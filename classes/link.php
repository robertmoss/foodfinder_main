<?php
	include_once 'core/dataentity.php';

	class Link extends DataEntity {
		
		public function getName() {
			return "Link";
		}
		
		public function getFields() {
			$fields = array(
				array("name","string"),
				array("url","string"),
				array("shared","boolean")
			);
			
			return $fields;
		}
		
		public function isRequiredField($fieldName) {
			// override
			return ($fieldName=='name'||$fieldName=='url'||$fieldName=='shared');
		}
		
		public function hasOwner() {
			return true;
		}
		
	}