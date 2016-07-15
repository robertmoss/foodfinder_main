<?php
    include_once dirname(__FILE__) . '/dataentity.php';

    class Content extends DataEntity {
        
        public function getName() {
            return "Content";
        }
        
        public function getFields() {
            $fields = array(
                array("name","string",100),
                array("defaultText","string",0),
                array("language","string",10)
            );
            
            return $fields;
        }
        
        public function isRequiredField($fieldName) {
            // override
            return ($fieldName=='name'||$fieldName=='defaultText');
        }
        
        public function hasTenant() {
            return false;
        }
       
        
    }