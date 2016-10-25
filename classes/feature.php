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
                array("coverImage","string",200)
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
            else {
                return ucfirst($fieldName);
                }
        }
        
        public function hasOwner() {
            return true;
        }
        
        protected function getEntitiesQuery($filters, $return, $offset) {
            
            $authorid = 0;
            $newsItems = false;
            
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
            
            if ($newsItems) {
                return 'call getFeaturesNewsItems(' . $this->userid . ',' . $return . ',' . $offset . ',' . $this->tenantid . ');';
            }
            elseif ($authorid==0) {
                return 'call getFeatures(' . $this->userid . ',' . $return . ',' . $offset . ',' . $this->tenantid . ');';
                }
            else {
                return 'call getFeaturesByAuthor(' . $authorid . ',' . $this->userid . ',' . $return . ',' . $offset . ',' . $this->tenantid . ');';
            }
            return $query;
                       
        }
        
        public function getEntities($filters, $return, $offset) {
            // override base to augment fields
            $entities = parent::getEntities($filters,$return,$offset);
            for ($i=0;$i<count($entities);$i++) {
                $entities[$i]["viewLink"] = "feature.php?id=" . $entities[$i]["id"];
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
            elseif (array_key_exists('author',$filters)) {
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