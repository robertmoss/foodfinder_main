<?php

include_once 'database.php';
include_once 'dataentity.php';
include_once 'utility.php';

class Tenant extends DataEntity {
		
	public $id = 0; 
	public $name = null;
	
	public function getName() {
			return "Tenant";
		}
	
	public function getFields() {
		$fields = array(
			array("name","string"),
		);		
		return $fields;
	}
	
	public function isRequiredField($fieldName) {
		return ($fieldname=='name');
	}	
	
	// Overrides to parent methods
	protected function getEntitiesQuery($tenantid, $filters, $userid, $return, $offset) {
		// override default since we don't need tenantID on this one.
				
			$query = 'call getTenants(' . Database::queryNumber($userid) . ', ' . Database::queryNumber($return). ', ' . Database::queryNumber($offset) . ');';				
			return $query;	
	}
	
	protected function getEntityCountQuery($tenantid, $filters, $userid) {
			$query = 'select count(*) from ' . strtolower($this->getName());
			return $query;
		}
	
	
}