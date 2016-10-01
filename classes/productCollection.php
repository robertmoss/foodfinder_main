<?php
    include_once dirname(__FILE__) . '/../core/classes/dataentity.php';

    class ProductCollection extends DataEntity {
        
        public function getName() {
            return "ProductCollection";
        }
        
        public function getFields() {
            $fields = array(
                array("name","string",200),
                array("description","string","0"), 
                array("introText","string","0"),
                array("queryParams","string",200),
                array("imageUrl","string",200)
            );
            
            return $fields;
        }
        
        public function isRequiredField($fieldName) {
            // override
            return ($fieldName=='name');
        }
        
        public function hasOwner() {
            return true;
        }
        
    }