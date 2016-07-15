<?php
    include_once dirname(__FILE__) . '/dataentity.php';

    class TenantContent extends DataEntity {
        
        public function getName() {
            return "TenantContent";
        }
        
        public function getFields() {
            $fields = array(
                array("name","string",100),
                array("contentText","string",0),
                array("language","string",10)
            );
            
            return $fields;
        }
        
        public function isRequiredField($fieldName) {
            // override
            return ($fieldName=='name'||$fieldName=='defaultText');
        }
        
        public function hasTenant() {
            return true;
        }
       
        
    }