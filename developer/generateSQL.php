<?php
/* a utility page that generates the SQL for the specified entity
 * needs type as GET parameter (e.g. generateSQL.php?type=patient)
 */
 
include_once dirname(__FILE__) . '/../core/partials/pageCheck.php';
include_once dirname(__FILE__) . '/../core/classes/service.php';
include_once dirname(__FILE__) . '/../core/classes/dataentity.php';
include_once dirname(__FILE__) . '/../classes/application.php';
include_once dirname(__FILE__) . '/../classes/config.php';
    
 // must be an super user to access this page
 if ($userID==0 || ($user && !$user->hasRole('superuser',$tenantID))) {
    Log::debug('Non super user (id=' . $userID . ') attempted to access generateSQL.php page', 10);
    $path = Config::getSiteRoot() . '/403.php';
    header('Location: ' . $path);
    die();
    }

$type = Utility::getRequestVariable('type', '');

if (strlen($type)<1) {
    Service::returnError('Please specify a type');
}

    $coretypes = array('tenant','tenantSetting','tenantProperty','category','menuItem','page','pageCollection','content','tenantContent');
    if(!in_array($type,$coretypes,false) && !in_array($type, Application::$knowntypes,false)) {
        // unrecognized type requested can't do much from here.
        Service::returnError('Unknown type: ' . $type,400,'entityService?type=' .$type);
    }
    
    $classpath = dirname(__FILE__) . '/../classes/'; 
    if(in_array($type,$coretypes,false)) {
        // core types will be in core subfolder
        $classpath = Config::$core_path . '/classes';
    }
    
    // include appropriate dataEntity class & then instantiate it
    $classfile =  $classpath . '/' . $type . '.php';
    if (!file_exists($classfile)) {
        Service::returnError('Internal error. Unable to process entity ' . $classfile,400,'entityService?type=' .$type);
    }
    include_once $classfile;
    $classname = ucfirst($type);    // class names start with uppercase
    $class = new $classname($userID,$tenantID); 
    $tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp';
    
    function stringValue($field) {
        
       if (sizeof($field)>=3 && $field[2]==0 ) {
          return 'text';                            
       }
       else {
           if (sizeof($field)<3|| !$field[2]) {
               $length = 100;
            }
            else {
                $length = $field[2];
            } 
           return 'varchar(' . $length . ')';
      }
    
     }
    
    function getFieldValue($field) {
        $value = "";
        switch ($field[1]) {
            case "string":
            case "picklist":
                $value = stringValue($field); 
                break;
            case "json":
                break;
            case "boolean":
                $value = "bit";
                break;
            case "number":
            case "hidden":
                break;
            case "date":
                $value = "datetime";
                break;
            case "linkedentity":
                $value = 'int';
                break;
            case "linkedentities":
                break;
            case "custom":
                break;
            }
        return $value;
    }

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Generate SQL</title>
        <?php include dirname(__FILE__) . '/../partials/includes.php'; ?>
        <link rel="stylesheet" type="text/css" href="css/core-forms.css" />
        <script type="text/javascript" src="js/validator.js"></script>
        <script type="text/javascript" src="js/jquery.form.min.js"></script>
        <script src="js/modalDialog.js"></script>
        <script src="js/entityPage.js"></script>
        
    </head>
    <body>
        <div class="container">
            <h2>SQL for <?php echo $class->getName() ?></h2>
            <div class="well">
                <code>
                    <?php
                    $tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp';
                    
                    /* CREATE table */
                    echo 'USE `' . Config::$database . '`;<br/>';
                    echo 'CREATE TABLE IF NOT EXISTS ' . lcfirst($class->getName()) . '(<br/>';
                    echo $tab . '`id` int(11) NOT NULL AUTO_INCREMENT,<br/>';
                    if ($class->hasTenant()) {
                        echo $tab . '`tenantid` int(11) NOT NULL,<br/>';
                    }
                    if ($class->hasOwner()) {
                        echo $tab . '`userid` int(11) NOT NULL,<br/>';
                    }
                    $fieldarray = $class->getFields();
                    $separator = $tab . "";
                    foreach ($fieldarray as $field) {
                        if ($field[1]!="linkedentities") {
                            echo $separator . '`' . $field[0] . '` ' . getFieldValue($field);
                            if ($class->isRequiredField($field[0])) {
                                echo ' NOT NULL';
                            }
                            $separator = ',<br/>' . $tab;
                        }
                    }
                    echo $separator . 'PRIMARY KEY (`id`)';
                    if ($class->hasTenant()) {
                        echo $separator . 'KEY `fk_' . lcfirst($class->getName()) . '_tenant_idx` (`tenantid`)';
                        echo $separator . 'CONSTRAINT `fk_' . lcfirst($class->getName()) . '_tenant` FOREIGN KEY (`tenantid`) REFERENCES `tenant` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION';
                    }
                    echo '<br/>);<br/>';

                    
                    echo '<br/>';
                    
                
                     /* GET proc */
                    echo '/* Stored Procedures for ' . $class->getName() . '*/<BR/><BR/>';
                   
                    echo 'USE `' . Config::$database . '`;<br/>';
                    echo 'DROP procedure IF EXISTS `get' . $class->getName() . 'ById`;<br/><br/>';
                    echo 'DELIMITER $$<br/>';
                    echo 'USE `' . Config::$database . '`$$<br/><br/>';
                    echo 'CREATE PROCEDURE get' . $class->getName() . 'ById(_id int, _tenantid int, _userid int)<br/>';
                    echo 'BEGIN<br/><br/>';
                    echo $tab . 'SELECT id,<br/>';
                    $fieldarray = $class->getFields();
                    $separator = $tab . $tab . "";
                    foreach ($fieldarray as $field) {
                        if ($field[1]!="linkedentities") {
                            echo $separator . $field[0];
                            $separator = ',<br/>' . $tab . $tab;
                        }
                    }
                    echo '<br/>' . $tab . 'FROM<BR/>'. $tab . $tab . lcfirst($class->getName()) . '<BR/>';
                    echo $tab . ' WHERE<br/>' . $tab . $tab . 'id=_id';
                    if ($class->hasTenant()) {
                        echo ' AND tenantid=_tenantid';
                    }
                    echo ';<br/><br/>';
                    echo 'END$$<br/>DELIMITER ;';
                    echo '<br/><br/>';
                    
                    /* GET procs and table for any linked entities */
                    foreach ($fieldarray as $field) {
                        if ($field[1]=="linkedentities") {
                             
                             $tablename = lcfirst($class->getName()) . ucfirst($field[2]);
                             echo 'CREATE TABLE IF NOT EXISTS `' . Config::$database . '`.`' . $tablename . '` (<br/>';
                             echo $tab . 'id INT NOT NULL AUTO_INCREMENT,</br/>';
                             echo $tab . lcfirst($class->getName()) . 'Id INT NOT NULL, <br/>';
                             echo $tab . lcfirst($field[2]) . 'Id INT NOT NULL, <br/>';
                             echo $tab . 'PRIMARY KEY (`id`),<br/>';
                             echo $tab . 'INDEX `fk_' . $tablename . '_' . lcfirst($class->getName()) . '_idx` (`' . lcfirst($class->getName()) . 'Id` ASC),<br/>';
                             echo $tab . 'INDEX `fk_' . $tablename . '_' . lcfirst($field[2]) . '_idx` (`' . lcfirst($field[2]) . 'Id` ASC),<br/>';
                             echo $tab . 'CONSTRAINT `fk_' . $tablename . '_' . lcfirst($class->getName()) . '` FOREIGN KEY (`' . lcfirst($class->getName()) . 'Id`)<br/>';
                             echo $tab . $tab . 'REFERENCES `' . Config::$database . '`.`' . lcfirst($class->getName()) . '` (`id`)<br/>';
                             echo $tab . $tab . 'ON DELETE CASCADE<br/>';
                             echo $tab . $tab . 'ON UPDATE NO ACTION,<br/>';
                             echo $tab . 'CONSTRAINT `fk_' . $tablename . '_' . lcfirst($field[2]) . '` FOREIGN KEY (`' . lcfirst($field[2]) . 'Id`)<br/>';
                             echo $tab . $tab . 'REFERENCES `' . Config::$database . '`.`' . lcfirst($field[2]) . '` (`id`)<br/>';
                             echo $tab . $tab . 'ON DELETE CASCADE<br/>';
                             echo $tab . $tab . 'ON UPDATE NO ACTION);<br/>';               
                             echo $tab . '<br/>';
                            
                             $procname = 'get' . ucfirst($field[0])  . 'By' . $class->getName(). 'Id';
                             $classname = ucfirst($field[2]);    // class names start with uppercase
                             $classpath = dirname(__FILE__) . '/../classes/'; 
                             if(in_array($field[2],$coretypes,false)) {
                                    // core types will be in core subfolder
                                    $classpath = Config::$core_path . '/classes/';
                                     }
                             $classfile = $classpath . lcfirst($classname) . '.php';
                       
                             include_once $classfile;
                             $subclass = new $classname($userID,$tenantID);
                             $subfieldarray = $subclass->getFields();
                             
                             echo 'USE `' . Config::$database . '`;<br/>';
                             echo 'DROP procedure IF EXISTS `' . $procname . '`;<br/><br/>';
                             echo 'DELIMITER $$<br/>';
                             echo 'USE `' . Config::$database . '`$$<br/><br/>';
                             echo 'CREATE PROCEDURE ' . $procname . '(_id int, _tenantid int, userid int)<br/>';
                             echo 'BEGIN<br/><br/>';
                             echo $tab . 'SELECT<br/>';
                             echo $tab . $tab . 'T1.id';
                             foreach ($subfieldarray as $subfield) {
                                 echo ',<br/>' . $tab . $tab . 'T1.' . $subfield[0];
                             }
                             echo $tab . '<br/>FROM<br/>';
                             echo $tab . $tab . lcfirst($field[2]) . ' T1<br/>';
                             echo $tab . $tab . 'INNER JOIN ' .  $tablename . ' T2 ON T1.id=T2.'.lcfirst($field[2]) . 'Id<br/>' ;
                             echo $tab . 'WHERE<br/>';
                             echo $tab . $tab . 'T2.' . lcfirst($class->getName()) . 'Id=_id<br/>';
                             echo $tab . $tab . 'and T1.tenantid=_tenantid;<br/><br/>';
                             echo 'END$$<br/>DELIMITER ;';
                             echo '<br/><br/>';
                             
                             
                             
                        }
                    }

                    /* GET LIST proc */                   
                    echo 'USE `' . Config::$database . '`;<br/>';
                    echo 'DROP procedure IF EXISTS `get' . $class->getPluralName() . '`;<br/><br/>';
                    echo 'DELIMITER $$<br/>';
                    echo 'USE `' . Config::$database . '`$$<br/><br/>';
                    echo 'CREATE PROCEDURE get' . $class->getPluralName() . '(userid int, numToReturn int,startAt int,tenantid int)<br/>';
                    echo 'BEGIN<br/><br/>';
                    echo $tab . 'prepare stmt from "SELECT id,<br/>';
                    $fieldarray = $class->getFields();
                    $separator = $tab . $tab . "";
                    foreach ($fieldarray as $field) {
                        if ($field[1]!="linkedentities") {
                            echo $separator . $field[0];
                            $separator = ',<br/>' . $tab . $tab;
                        }
                    }
                    echo '<br/>' . $tab . 'FROM<BR/>'. $tab . $tab . lcfirst($class->getName()) . '<BR/>';
                    if ($class->hasTenant()) {
                        echo $tab . ' WHERE<br/>' . $tab . $tab;
                        echo ' tenantid=tenantid';
                    }
                    echo '<br/>' . $tab . ' LIMIT  ?,?";<br/><br/>';
                    echo 'set @start=startAt;<br/>';
                    echo 'set @num=numToReturn;<br/><br/>';
                    echo 'execute stmt using @start,@num;';
                    echo '<br/><br/>';
                    echo 'END$$<br/>DELIMITER ;';
                    echo '<br/><br/>';
                    
                    /* ADD proc */
                    echo 'USE `' . Config::$database . '`;<br/>';
                    echo 'DROP procedure IF EXISTS `add' . $class->getName() . '`;<br/><br/>';
                    echo 'DELIMITER $$<br/>';
                    echo 'USE `' . Config::$database . '`$$<br/><br/>';
                    echo 'CREATE PROCEDURE add' . $class->getName() . '(';
                    $fieldarray = $class->getFields();
                    $separator = "";
                    foreach ($fieldarray as $field) {
                        if ($field[1]!="linkedentities") {
                            echo $separator . $field[0] . ' ';
                            $separator = ", ";
                        }
                        echo getFieldValue($field);

                        }
                    echo ", tenantid int";
                    if ($class->hasOwner()) {
                        echo ', userid int';
                    }
                    echo ")<br/>";
                    echo 'BEGIN<br/>';
                    echo '<br/>';
                    echo $tab . 'INSERT INTO ' . lcfirst($class->getName()) . '(<br/>';
                    $separator = $tab . $tab;
                    foreach ($fieldarray as $field) {
                        if ($field[1]!="linkedentities") {
                            echo $separator . $field[0];
                            $separator = ',<br/>' . $tab . $tab;
                            }
                        }
                    if ($class->hasTenant()) {
                        echo ',<br/>' .$tab . $tab . 'tenantid';
                    }
                    if ($class->hasOwner()) {
                        echo ',<br/>' .$tab . $tab . 'userid';
                    }
                    echo ')<br/>' . $tab . 'VALUES (';
                    $separator="";
                    foreach ($fieldarray as $field) {
                        if ($field[1]!="linkedentities") {
                            echo $separator . $field[0] ;
                            $separator = ',<br/>' . $tab . $tab;
                            }
                        }
                    if ($class->hasTenant()) {
                        echo ',<br/>' .$tab . $tab . 'tenantid';
                    }
                    if ($class->hasTenant()) {
                        echo ',<br/>' .$tab . $tab . 'userid';
                    }
                    echo ');';
                    echo '<br/><br/>';
                    echo $tab . 'SELECT Last_Insert_ID() as newID;';
                    echo '<br/><br/>';
                    echo 'END$$<br/>DELIMITER ;';
 
                    echo '<br/><br/>';
 
                    /* Update Proc */
                    $tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp';
                     echo 'USE `' . Config::$database . '`;<br/>';
                    echo 'DROP procedure IF EXISTS `update' . $class->getName() . '`;<br/><br/>';
                    echo 'DELIMITER $$<br/>';
                    echo 'USE `' . Config::$database . '`$$<br/><br/>';
                    echo 'CREATE PROCEDURE update' . $class->getName() . '(_id int';
                    $fieldarray = $class->getFields();
                    $separator = ", ";
                    foreach ($fieldarray as $field) {
                        if ($field[1]!="linkedentities") {
                            echo $separator . $field[0] . ' ';
                        }
                        echo getFieldValue($field);
                        }
                    echo ", _tenantid int";
                    if ($class->hasOwner()) {
                        echo ', _userid int';
                    }
                    echo ")<br/>";
                    echo 'BEGIN<br/>';
                    echo '<br/>';
                    echo $tab . 'UPDATE ' . lcfirst($class->getName()) . ' SET<br/>';
                    $separator = $tab . $tab;
                    foreach ($fieldarray as $field) {
                        if ($field[1]!="linkedentities") {
                            echo $separator . $field[0] . ' = ' . $field[0];
                            $separator = ',<br/>' . $tab . $tab;
                            }
                        }
                    echo '<br/>';
                    echo $tab . 'WHERE';
                    echo '<br/>';
                    echo $tab . $tab . "id=_id<br/>";
                    if ($class->hasTenant()) {
                        echo $tab . $tab . "AND tenantid=_tenantid";
                    }
                    if ($class->hasOwner()) {
                        echo '<br/>' . $tab . $tab . "AND userid=_userid";
                    }
                    echo ';<br/>END$$<br/>DELIMITER ;<br/><br/>';
                    
                    /* DELETE proc */
                    echo 'USE `' . Config::$database . '`;<br/>';
                    echo 'DROP procedure IF EXISTS `delete' . $class->getName() . '`;<br/><br/>';
                    echo 'DELIMITER $$<br/>';
                    echo 'USE `' . Config::$database . '`$$<br/><br/>';
                    echo 'CREATE PROCEDURE delete' . $class->getName() . '(id int, tenant int, userid int)<br/>';
                    echo 'BEGIN<br/><br/>';
                    echo $tab . 'DELETE FROM ' . lcfirst($class->getName());
                    echo ' WHERE id=id';
                    if ($class->hasTenant()) {
                        echo ' AND tenantid=tenantid';
                    }
                    echo ';<br/><br/>';
                    echo 'END$$<br/>DELIMITER ;';
                    echo '<br/><br/>';
                    
                    
                    echo '<BR/></BR>/* End ' . $class->getName() . ' stored procs */<BR/><BR/>';
                    
                    ?>
                </code>
            </div>
       </div>  
       <?php include dirname(__FILE__) . '/../partials/footer.php';?>         
    </body>
</html>    
