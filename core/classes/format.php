<?php
/*
 *  The Format class provides an array of utility functions for formatting text, content, and data for
 *  onscreen presentation - this needs to move out of core: very specific to foodfinder entities
 *  Need a core formatter and then application specific wrapper that calls into it
 */ 
class Format {
 
 public static function formatDateLine($dateValue,$friendlyValues) {
     // formats a "posted date" for proper display
     // $friendlyValues: if true, will convert date/time to more friendlty representation (e.g. "2 hours ago")
     $timestamp = strtotime($dateValue);
     $targetDateTime = new DateTime($dateValue);
     $currentTime = new DateTime();
     $interval =$currentTime->diff($targetDateTime);
     $output = date("F j, Y",$timestamp);
     if ($friendlyValues && $interval->days<2 && $interval->d<2) {
        if ($interval->d==0 && $interval->i<2) {
            $output = 'just now';
        }    
        elseif ($interval->d==0 && $interval->h<1) {
            $output = $interval->i . ' minutes ago';
        }    
        elseif ($interval->d==0 && $interval->h<4) {
            $period= "hours";    
            if ($interval->h==1) {
                $period = "hour"; 
            }
             $output = $interval->h . ' ' . $period . ' ago';
         }
        else {
             // see if these are the same day
             if ($currentTime->format('Y-m-d')==$targetDateTime->format('Y-m-d')) {
                 $output="Today at " . date("g:i a",$timestamp);
             }
            elseif (($currentTime->format('w') - $targetDateTime->format('w'))==1) {
                $output="Yesterday at " . date("g:i a",$timestamp);
             }            
         }
     }    
 
    return $output;
 }   
 
    
 /*
 * Takes a string and augments it to render correctly as HTML content
 */
public static function renderWebContent($content) {
    
    //$content=$runningContent;
    $content = nl2br($content); 
    return $content;
}
    
    public static function addDisplayElements($location) {
    
    // adds helping elements to a location or other data set to support web & mobile display
    // $location is an associative array of data 
    
    // format URLs for on-screen display
    if (array_key_exists("url",$location) && strlen($location["url"])>0) {
        // strip http & trailing slash
        $url = $location["url"];
        $url = str_replace("http://","",$url);
        $url = str_replace("https://","",$url);
        if (substr($url,-1)=='/') {
            $url = rtrim($url,'/');
        }
        $location["displayurl"] = $url;
    }

    // add a version of phonenumber that is clickable on devices
    if (array_key_exists("phone",$location) && strlen($location["phone"])>0) {
        // format to remove characters & make clickable
        $phone = $location["phone"];
        $phone = str_replace("(","",$phone);
        $phone = str_replace(")","",$phone);
        $phone = str_replace("-","",$phone);
        $phone = str_replace(" ","",$phone);
        if (substr($phone,1)!='1') {
            $phone = '+1' . $phone;
        }
        $location["clickablephone"] = $phone;
    }
    
    // add a version of phonenumber that is clickable on devices
    if (array_key_exists("uservisits",$location)) {
        // format to remove characters & make clickable
        if ($location["uservisits"]>0) {
            $location["visited"] = 'yes';
        }
    }
    
    return $location;
}
    
}
