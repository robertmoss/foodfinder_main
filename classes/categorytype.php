<?php
	include_once 'core/dataentity.php';

	class Categorytype extends DataEntity {
		
		public function getName() {
			return "CategoryType";
		}
	
		public function getFields() {
			$fields = array(
				array("name","string")
			);
			
			return $fields;
		}
		
		
	}