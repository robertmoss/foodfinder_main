<?php
    // the class factory returns classes based upon class name (for supported classes only)
    include_once dirname(__FILE__) . '/../../classes/application.php';
    
class ClassFactory {
    
    public static function getClass($classname,$userID,$tenantID) {
                   
        $coretypes = array('tenant','tenantSetting','tenantProperty','category','menuItem','page','tenantContent','entityList');
        if(!in_array($classname,$coretypes,false) && !in_array($classname, Application::$knowntypes,false)) {
            throw new Exception('Unknown class name: ' . $classname);
        }
        
        $classpath = Config::$root_path . '/classes/'; 
        if(in_array($classname,$coretypes,false)) {
            // core types will be in core path as configured in config.php
            $classpath = Config::$core_path . '/classes/';
        }
    
        // include appropriate dataEntity class & then instantiate it
        $classfile = $classpath . $classname . '.php';
        if (!file_exists($classfile)) {
            Utility::debug('Unable to instantiate class for ' . $classname . ' Classfile does not exist. Looking for ' . $classfile, 9);
            throw new Exception('Unable to instantiate new : ' . classname);
        }
        include_once $classfile;
        $classname = ucfirst($classname);    // class names start with uppercase
        $class = new $classname($userID,$tenantID); 
        
        return $class;
        
    }
}
    