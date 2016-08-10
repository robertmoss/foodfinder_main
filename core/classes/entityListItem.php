<?php

include_once 'database.php';
include_once 'dataentity.php';
include_once 'utility.php';
include_once 'cache.php';

class EntityListItem extends DataEntity {
    
    
    public function getName() {
            return "EntityListItem";
        }
    
    public function getFields() {
        $fields = array(
            array("entityListId","parententity",'entityList'),
            array("entityId","number"),
            array("sequence","number",0)
        );      
        return $fields;
    }
    
  
    public function isRequiredField($fieldName) {
        return ($fieldName=='entityListId');
    }
    
     
}