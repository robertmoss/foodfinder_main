<?php

/*
 * Wrapper to the application cache
 * In the future, will implement memcache; as of now this is just a stub
 */
 
 class Cache {
 
     public static function getValue($key) {
        // for now, we always return null (cache miss) - later will actually hit the cache    
        return null;       
     }
     
     public static function putValue($key,$value) {
         // for now, do nothing
         return true;
     }
     
  }