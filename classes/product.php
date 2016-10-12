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
        
        public function getEntityCountForList($listId) {
             $query = 'select count(*) from entityList EL inner join entityListItem ELI on EL.id =ELI.entityListId';
             $query .= ' where EL.entity="product" and EL.id=' . Database::queryNumber($listId) . ' and EL.tenantid=' . Database::queryNumber($this->tenantid) . ';';  
        
            $data = Database::executeQuery($query);
            if ($data->num_rows==0) {
                    //no match found.
                    return 0;
                }
                else {
                    $r = mysqli_fetch_row($data);
                    return $r[0];
                }   
        }
     
        public function getEntitiesFromList($listId,$return,$offset) {

            // for now, ignoring return & offset - will return all products in list
            $query = 'call getProductsByEntityListIdEx(' . $listId . ',' . $this->tenantid . ',' . $offset. ',' . $return . ')';
        
            $data = Database::executeQuery($query);
            $entity = '';
            
            if ($data->num_rows==0) {
                    return array();
                }
            while ($r = mysqli_fetch_assoc($data))
                {
                $entities[] = $r;
                }
            
            return $entities;   
    }
    
        
        
    }