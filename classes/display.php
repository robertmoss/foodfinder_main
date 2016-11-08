<?php 
/*
 * Display class: use for generating markup and other UI-specific display components
 * Rides on top of the core Format class
 */
 
include_once Config::$root_path . '/classes/media.php';
include_once Config::$core_path . '/classes/format.php';
include_once Config::$root_path . '/classes/product.php';
 
 class Display {
     
     
     public static function showMediaItem($mediaId,$userID,$tenantID) {
         $markup = Display::getMediaItemMarkup($mediaId,$userID,$tenantID);
         echo $markup;
     }
     
     public static function getMediaItemMarkup($mediaId,$userID,$tenantID) {
        
            $class = new Media($userID,$tenantID);
             try {
                 $media=$class->getEntity($mediaId);
                 $caption='';
                 $metadata = $media["metadata"];
                 if (!is_null($metadata)) {
                     $openSep = '';
                     $closeSep = '';
                     if (property_exists($metadata, 'caption') && strlen($metadata->{'caption'})>0) {
                         $caption .= $metadata->{'caption'};
                         $openSep = ' (';
                     }
                     if (property_exists($metadata, 'credit') && strlen($metadata->{'credit'})>0) {
                         $caption .= $openSep . 'Courtesy ';
                         $endTag = '';
                         if (property_exists($metadata, 'crediturl') && strlen($metadata->{'crediturl'})>0) {
                             $caption .= '<a href="' . $metadata->{'crediturl'} . '" target="_blank">';
                             $endTag = "</a>";
                         }
                         $caption .= $metadata->{'credit'} . $endTag;
                         if ($openSep==" (") {
                            $closeSep = ')';
                            $openSep = ' ';
                         }
                     }
                     if (property_exists($metadata, 'license') && strlen($metadata->{'license'})>0) {
                         $caption .= $openSep . 'under ';
                         $endTag = '';
                         if (property_exists($metadata, 'licenseurl') && strlen($metadata->{'licenseurl'})>0) {
                             $caption .= '<a href="' . $metadata->{'licenseurl'} . '" target="_blank">';
                             $endTag = "</a>";
                         }
                         $caption.= $metadata->{'license'}.$endTag;
                        }
                     $caption .= $closeSep;
                     }
                 $markup = '<div id="coverImage" class="coverImage">';
                 $markup .= '<img src="' . $media["url"] . '"/>';
                 $markup .= '<div class="caption">' . $caption . '</div>';
                 $markup .= '</div>';
                 return $markup;
             }
             catch(Exception $ex) {
                 // do anything or just ignore if we can't load? Ignoring for now
                 //echo '<p>unable to load: ' . $ex->getMessage() . '</p>';
                 return '<p class="hidden">Unable to load media id=' .$mediaId . '<p>';
                 }  
         }

        public static function renderWebContent($content,$userID,$tenantID) {
            
            $content = Format::renderWebContent($content);
            
            // replace location tags with links
            $index = strpos($content,'<location ');
            $count = 0;
            $runningContent="";
            while ($index>0 && $count<50) {
                $idindex = $index+9;
                $id='';
                $endfound = false;
                while (!$endfound && $idindex<strlen($content)) {
                    $id .= substr($content,$idindex,1);
                    $idindex++;
                    if (substr($content,$idindex,1)==">") {
                        $endfound=true;
                    }
                }
                $endindex = strpos($content,'</location>');
                $linkURL = Config::$core_root . '/entityPage.php?type=location&id=' . $id;
                $linktext = substr($content,$idindex+1,$endindex-$idindex-1);
                $newcontent = substr($content,0,$index);
                $newcontent .= '<a href="#" onclick="loadLocation(' . $id .');return false;">' . $linktext . '</a>'; 
                $newcontent .= substr($content,$endindex + 11);
                $runningContent .= 'Found at: ' .$index . ' through ' . $endindex . ': ' . $linktext . '<hr/>';
                $content = $newcontent;
                $index = strpos($content,'<location ');
                $runningContent .= 'newind=' . $index;        
                $count++;
                }

            // replace feature tags with links
            $index = strpos($content,'<feature ');
            $count = 0;
            $runningContent="";
            while ($index>0 && $count<50) {
                $idindex = $index+9;
                $id='';
                $endfound = false;
                while (!$endfound && $idindex<strlen($content)) {
                    $id .= substr($content,$idindex,1);
                    $idindex++;
                    if (substr($content,$idindex,1)==">") {
                        $endfound=true;
                    }
                }
                $endindex = strpos($content,'</feature>');
                $linkURL = Config::$core_root . '/entityPage.php?type=location&id=' . $id;
                $linktext = substr($content,$idindex+1,$endindex-$idindex-1);
                $newcontent = substr($content,0,$index);
                $newcontent .= '<a href="feature.php?id=' . $id .'">' . $linktext . '</a>'; 
                $newcontent .= substr($content,$endindex + 10);
                $runningContent .= 'Found at: ' .$index . ' through ' . $endindex . ': ' . $linktext . '<hr/>';
                $content = $newcontent;
                $index = strpos($content,'<feature ');
                $runningContent .= 'newind=' . $index;        
                $count++;
            }
    
             // replace product tags with links
            $index = strpos($content,'<product ');
            $count = 0;
            $runningContent="";
            while ($index>0 && $count<50) {
                $idindex = $index+8;
                $id='';
                $endfound = false;
                while (!$endfound && $idindex<strlen($content)) {
                    $id .= substr($content,$idindex,1);
                    $idindex++;
                    if (substr($content,$idindex,1)==">") {
                        $endfound=true;
                    }
                }
                $endindex = strpos($content,'</product>');
                $linkURL = Config::$core_root . '/entityPage.php?type=product&id=' . $id;
                $linktext = substr($content,$idindex+1,$endindex-$idindex-1);
                $newcontent = substr($content,0,$index);
                $newcontent .= '<a href="#" onclick="loadProduct(' . $id .');return false;">' . $linktext . '</a>'; 
                $newcontent .= substr($content,$endindex + 10);
                $runningContent .= 'Found at: ' .$index . ' through ' . $endindex . ': ' . $linktext . '<hr/>';
                $content = $newcontent;
                $index = strpos($content,'<product ');
                $runningContent .= 'newind=' . $index;        
                $count++;
            }
    
            // replace author tags with links
            $index = strpos($content,'<author ');
            $count = 0;
            $runningContent="";
            while ($index>0 && $count<50) {
                $idindex = $index+8;
                $id='';
                $endfound = false;
                while (!$endfound && $idindex<strlen($content)) {
                    $id .= substr($content,$idindex,1);
                    $idindex++;
                    if (substr($content,$idindex,1)==">") {
                        $endfound=true;
                    }
                }
                $endindex = strpos($content,'</author>');
                $linkURL = Config::getSiteRoot() . '/author.php?id=' . $id;
                $linktext = substr($content,$idindex+1,$endindex-$idindex-1);
                $newcontent = substr($content,0,$index);
                $newcontent .= '<a href="'. $linkURL . '">' . $linktext . '</a>'; 
                $newcontent .= substr($content,$endindex + 9);
                $runningContent .= 'Found at: ' .$index . ' through ' . $endindex . ': ' . $linktext . '<hr/>';
                $content = $newcontent;
                $index = strpos($content,'<author ');
                $runningContent .= 'newind=' . $index;        
                $count++;
            }
    
            // MEDIA - replace media tags with media display (assume images for now, but this can be expanded in future based on media type)
            $index = strpos($content,'<media ');
            $count = 0;
            $runningContent="";
            while ($index>0 && $count<50) {
                $idindex = $index+7;
                $id='';
                $endfound = false;
                while (!$endfound && $idindex<strlen($content)) {
                    $id .= substr($content,$idindex,1);
                    $idindex++;
                    if (substr($content,$idindex,1)==">") {
                        $endfound=true;
                    }
                }
                //update newcontent
                $newcontent = substr($content,0,$index);
                $newcontent .= Display::getMediaItemMarkup($id, $userID, $tenantID);
                $newcontent .= substr($content,$idindex+1);
                $content = $newcontent;
                $index = strpos($content,'<media ');
                $runningContent .= 'newind=' . $index;        
                $count++;
             }
            
            // PRODUCT LIST - replace productList tags with display 
            $index = strpos($content,'<productList ');
            $count = 0;
            $runningContent="";
            while ($index>0 && $count<50) {
                $idindex = $index+13;
                $id='';
                $endfound = false;
                while (!$endfound && $idindex<strlen($content)) {
                    $id .= substr($content,$idindex,1);
                    $idindex++;
                    if (substr($content,$idindex,1)==">") {
                        $endfound=true;
                    }
                }
                //update newcontent
                $newcontent = substr($content,0,$index);
                $newcontent .= Display::getProductListMarkup($id, $userID, $tenantID);
                $newcontent .= substr($content,$idindex+1);
                $content = $newcontent;
                $index = strpos($content,'<productList ');
                $runningContent .= 'newind=' . $index;        
                $count++;
             }

            return $content;

        }

        public static function getProductListMarkup($listId,$userID,$tenantID) {
            $class = new Product($userID,$tenantID);
            $markup='<div class="collection condensed">';
             try {
                 $collection=$class->getEntitiesFromList($listId,10,0);
                 foreach($collection as $product) {
                     $markup.='<div class="collectionItem">';
                     if (strlen(key_exists("imageUrl",$product) && strlen($product["imageUrl"])>0)) {
                        $markup.='<div class="bookCover"><img src="' .$product["imageUrl"] . '"></div>';
                     }
                     $markup .= '<h2><a href="' . $product["url"] . '" target="_blank" onclick="logClick('. $product["id"] . ');">' . $product["title"] . '</a></h2>';
                     if (strlen(key_exists("author",$product) && strlen($product["author"])>0)) {
                        $markup.='<p class="author">' . $product["author"] . '</p>';
                     }
                     $markup .='<p class="description">' . $product["description"] . '</p>';
                     $markup .= '<p><a href="' . $product["url"] . '" target="_blank" onclick="logClick(' . $product["id"] . ');">Buy Now</a></p>';
                     $markup .='</div>';
                 }
            }
             catch(Exception $ex) {
                 return '<p class="hidden">Unable to load collection id=' .$listId . ': ' . $ex->getMessage() . '<p>';
             }
            $markup.="</div>";
            return $markup;
        }
 }
