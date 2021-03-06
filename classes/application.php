<?php

/* because the core classes can be shared across applications, the Application class gives information specific to 
 * the current application they are being used in.
 * Currently, all just static properties, but come upgrade in the future to be generated dynamically for applications
 * from the database, config files, etc.
 */

class Application {

    // name of the application
    public static $name = "Food Finder";
    
    // version
    public static $version = "2.1.9";
    
    // indicate the known entity types that the Entity Service can manage
    // as you add entities to your application, add them here
    public static $knowntypes = array('location','link','media','category','feature','product','productCollection','assignment'); 
}