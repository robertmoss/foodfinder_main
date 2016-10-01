<?php
    include_once dirname(__FILE__) . '/../core/classes/dataentity.php';

    class Feature extends DataEntity {
        
        public function getName() {
            return "Feature";
        }
        
        public function getFields() {
            $fields = array(
                array("name","string",100),
                array("headline","string",300),
                array("subhead","string",300),
                array("author","string",200),
                array("datePosted","date"),
                array("introContent","string","0"),
                array("closingContent","string","0"),
                array("locationCriteria","string",500),
                array("locationTemplate","string",0),
                array("useLocationDesc","boolean"),
                array("numberEntries","boolean"),
                array("reverseOrder","boolean"),
                array("coverImage","string",200)
            );
            
            return $fields;
        }
        
        public function isRequiredField($fieldName) {
            // override
            return ($fieldName=='name'||$fieldName=='headline');
        }
        
        public function hasOwner() {
            return true;
        }
        
    }