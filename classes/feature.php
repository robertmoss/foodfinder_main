<?php
    include_once dirname(__FILE__) . '/../core/classes/dataentity.php';
    include_once dirname(__FILE__) . '/../core/classes/format.php';

    class Feature extends DataEntity {
        
        public function getName() {
            return "Feature";
        }
        
        public function getFields() {
            $fields = array(
                array("name","string",100),
                array("headline","string",300),
                array("subhead","string",300),
                array("author","linkedentity",20,"authorList",false,"author"),
                array("datePosted","date"),
                array("introContent","string","0","html"),
                array("closingContent","string","0"),
                array("locationCriteria","string",500),
                array("locationTemplate","string",0),
                array("useLocationDesc","boolean"),
                array("numberEntries","boolean"),
                array("reverseOrder","boolean"),
                array("isNewsItem","boolean"),
                array("coverImage","string",200),
                array("status","picklist",100,"featureStatus",false)
            );
            
            return $fields;
        }
        
        public function isRequiredField($fieldName) {
            // override
            return ($fieldName=='name'||$fieldName=='headline');
        }
        
        public function friendlyName($fieldName) {
            if ($fieldName=="isNewsItem") {
                return "Is a News Item";
            }
            elseif ($fieldName=="datePostedFriendly") {
                return "Date Posted";
            }
            else {
                return ucfirst($fieldName);
                }
        }
        
        public function hasOwner() {
            return true;
        }
        
        protected function getEntitiesQuery($filters, $return, $offset) {
            
            $authorid = null;
            $newsItems = null;
            $status = null;
            $extended = false;
            
            if (array_key_exists('author',$filters)) {
                // use author
                $authorid = Database::queryNumber($filters['author']);
            }
            elseif (array_key_exists('authorid',$filters)) {
                // use author
                $authorid = Database::queryNumber($filters['authorid']);
            }
            
            if (array_key_exists('news',$filters)) {
                $newsItems = (strtolower($filters['news'])=="true" || strtolower($filters['news'])=="yes");
            }
            
             if (array_key_exists('extended',$filters)) {
                $extended= (strtolower($filters['extended'])=="true" || strtolower($filters['extended'])=="yes");
            }
            
            if (array_key_exists('status',$filters)) {
                $status=$filters['status'];
            }
            
            return 'call getFeaturesEx(' . Database::queryNumber($authorid) . ',' . Database::queryBoolean($newsItems) . ',' . Database::queryString($status) . ','. Database::queryBoolean($extended) . ',' . $this->userid . ',' . $return . ',' . $offset . ',' . $this->tenantid . ');';
            
            return $query;
                       
        }
        
        public function getEntities($filters, $return, $offset) {
            // override base to augment fields
            $entities = parent::getEntities($filters,$return,$offset);
            for ($i=0;$i<count($entities);$i++) {
                $entities[$i]["viewLink"] = "feature.php?id=" . $entities[$i]["id"];
                $entities[$i]["post date"] = Format::formatDateLine($entities[$i]["datePosted"], true);
            }
            return $entities;
        }
        
        
        protected function getEntityCountQuery($filters) {
            // override base to allow searching for features by the following:
            //  author (or authorid), same result
            $where='';
            if (array_key_exists('news',$filters)) {
                $where = ' and isNewsItem=1';
            }
            
            if (array_key_exists('author',$filters)) {
                // use author
                $where = ' and author = ' . Database::queryNumber($filters['author']);
            }
            elseif (array_key_exists('authorid',$filters)) {
                // use author
                $where = ' and author = ' . Database::queryNumber($filters['authorid']);
            }
            
            $query = 'select count(*) from ' . lcfirst($this->getName()) . ' where tenantid=' . $this->tenantid . $where;
            return $query;
        }
        
        
        
    }