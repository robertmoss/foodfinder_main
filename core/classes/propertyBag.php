<?php
 
include_once 'database.php';
include_once 'dataentity.php';
include_once 'utility.php';
include_once 'cache.php';

class PropertyBag extends DataEntity {
    
    private $bagContents = array();
    private $bagName = "";
    private $bagId = 0;
      
    public function getName() {
            return "PropertyBag";
        }
    
    public function getFields() {
        $fields = array(
            array("name","string",300),
            array("properties","string",0)
        );      
        return $fields;
    }
    
    public function hasOwner() {
        return false;
    }
    
    public function isRequiredField($fieldName) {
        return ($fieldName=='name');
    }
    
    public function getProperty($bag,$property,$defaultValue) {
        // a little caching - right now just to keep from multiple DB hits per page
        // in future potential to do proper caching if volume justifies it
        $return = $defaultValue;
        $bag = strtolower($bag);
        $properties = $this->getPropertyBag($bag);
        if (array_key_exists($property,$properties)) {
            $return = $properties[$property];
        }     
        return $return;        
    }
    
    public function putProperty($bag,$property,$value) {

        $bag = strtolower($bag);
        $properties = $this->getPropertyBag($bag);
        if (array_key_exists($property,$properties) && $properties[$property] == $value) {
            // current value in bag; no need to save
        }
        else {
            $properties[$property] = $value;
            $this->savePropertyBag($properties);
        }
    }
    
    public function removeProperty($bag,$property) {
        // removes property from the bag altogether
        $bag = strtolower($bag);
        $properties = $this->getPropertyBag($bag);
        if (array_key_exists($property,$properties)) {
            unset($properties[$property]);
            $this->savePropertyBag($properties);            
        } 
    }
    
    private function getPropertyBag($bag) {
            
        $propertyBag = array();
        if ($bag==$this->bagName) {
            // already cached
            $propertyBag = $this->bagContents;
        }
        else {
            $query = "call getPropertyBagByName(" . Database::queryString($bag) . "," . $this->tenantid . "," . $this->userid . ")";
            $result = Database::executeQuery($query);
            if ($row = mysqli_fetch_assoc($result)) {
                $propertyBag = unserialize($row["properties"]);
                Log::debug('Property bag ' . $bag . ' retrieved. Contains ' . count($propertyBag) . ' items.', 5);
                $this->bagName = $bag;
                $this->bagContents = $propertyBag;
                $this->bagId = $row["id"];
                }
            else {
                // property bag doesn't exist. Need to create upon save
                Log::debug('Property bag ' . $bag . ' requested but not found.', 5);
                $this->bagId=0;
                $this->bagName = $bag;
            }
        }
        return $propertyBag;
    }
    
    private function savePropertyBag($properties) {
        // first, cache and serialize property array
        $this->bagContents = $properties;
        $properties = serialize($properties);
        if ($this->bagId>0) {
            // bag exists in DB: update    
            $query = "call updatePropertyBag(" . $this->bagId . ',' . Database::queryString($this->bagName) . ',' . Database::queryString($properties) . ','. $this->tenantid . ')';
            Database::executeQuery($query); 
            }
        else {
            // new bag: insert
            $query = "call addPropertyBag(" . Database::queryString($this->bagName) . ',' . Database::queryString($properties) . ','. $this->tenantid . ')';
            $results=Database::executeQuery($query); 
            if ($row=mysqli_fetch_assoc($results)) {
                $this->bagId = $row["newID"];
            }
        }
        }
     
}  