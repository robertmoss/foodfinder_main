<?php
    include_once dirname(__FILE__) . '/../core/classes/dataentity.php';

    class Assignment extends DataEntity {
        
        public function getName() {
            return "Assignment";
        }
        
        public function getFields() {
            $fields = array(
                array("name","string",100),
                array("description","string","0"),
                array("type","picklist",100,"assignmentType",false),
                array("assignedTo","linkedentity",20,"authorList",false,"author"),
                array("targetDate","date"),
                array("status","picklist",100,"assignmentStatus",false)
            );
            
            return $fields;
        }
        
        public function isRequiredField($fieldName) {
            // override
            return ($fieldName=='name'||$fieldName=='type');
        }
        
        public function friendlyName($fieldName) {
            if ($fieldName=="assignedTo") {
                return "Assigned To";
            }
            elseif ($fieldName=="targetDate") {
                return "Targeted Date";
            }
            else {
                return ucfirst($fieldName);
                }
        }
        
        public function hasOwner() {
            return false;
        }
        
        protected function getEntitiesQuery($filters, $return, $offset) {
            
            return "call getAssignments(" . Database::queryNumber($this->userid) . ',' . $return . ',' . $offset . ',' . $this->tenantid . ');';
            }
       
    }
    