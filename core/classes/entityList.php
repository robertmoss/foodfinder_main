<?php

include_once 'database.php';
include_once 'dataentity.php';
include_once 'utility.php';
include_once 'cache.php';

class EntityList extends DataEntity {
    
    
    public function getName() {
            return "EntityList";
        }
    
    public function getFields() {
        $fields = array(
            array("name","string",300),
            array("description","string",2000),
            array("type","picklist",0,"entityListTypes",false),
            array("entity","picklist",0,"entities",false),
            array("entityListItems","childentities","entityListItem",true,true),
        );      
        return $fields;
    }
    
    public function hasOwner() {
        return true;
    }
    
    public function isRequiredField($fieldName) {
        return ($fieldName=='name');
    }
    
   public function hasCustomEditControl($fieldName) {
       return ($fieldName=='entityListItems');
   }
   
   public function getCustomEditControl($fieldName,$value,$entityId) {
       if ($fieldName=='entityListItems') {
           $markup = '<div class="well">';
           $markup .= '<div class="panel panel-default"><div class="panel-heading"><div>List Members';
           $markup .= '<input id="txtEntitySearch" type="input" class="locationSearch" onkeypress="return txtEntitySearchKeyPress(event);"/>';
           $markup .= '<button type="button" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-search" aria-hidden="true" onclick="searchEntities();"></span></button>';
           $markup .= '</div>';
           $markup .= '<div id="entitySearchResults"></div>';
           $markup .= '</div>';
           
           //$child_array = $this->getAvailableChildren($fieldName,$this->tenantid);
           $query = 'call getLocationsByEntityListId(' . $entityId . ',' . $this->tenantid . ')';
           $child_array = Database::executeQueryReturnArray($query);
           
           $markup .= '<div class="panel-body">';
           $markup .= '<div id="entityListContainer" class="row sortable">';
           $separator = '';
           $idList = '';
           foreach($child_array as $c) {
               $markup .= '<p id="listItem' . $c["entityId"] . '"><button type="button" class="btn btn-default btn-xs" onclick="removeListItem(' . $c['entityId'] . ')"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button> ';
               $markup .= $c["name"] . ' (' . $c["city"] . ',' . $c["state"] . ')</p>';
               $idList .= $separator . $c["entityId"];
               $separator=",";
           }
           $markup .= '</div></div></div>';
           $markup .= "</div>";
           $markup .= '<input id="txtEntityListItems" type="hidden" class="idList" name="entityListItems" value="' . $idList . '"';
           
           return $markup;
           }
       else {
        return '<p>Custom Edit Control Not Defined for Field</p>';        
        }
    }
     
}