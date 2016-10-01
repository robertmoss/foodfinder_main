<?php
    include_once dirname(__FILE__) . '/../core/classes/dataentity.php';

    class Product extends DataEntity {
        
        public function getName() {
            return "Product";
        }
        
        public function getFields() {
            $fields = array(
                array("name","string",100),
                array("url","string",500),
                array("title","string",300),
                array("author","string",200),
                array("description","string","0"),
                array("price","decimal",10,2),
                array("imageUrl","string",500)
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