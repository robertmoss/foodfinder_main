<?php 
    include dirname(__FILE__) . '/core/partials/pageCheck.php';
     include_once Config::$root_path . '/classes/productCollection.php';
     
    $thisPage="collection";
    
    $id = Utility::getRequestVariable('id', 0);
    $errorMsg = "";
    if ($id==0) {
        $errorMsg ="You must specify a valid collection id.";
    }
    else {
        try {
            $class = new ProductCollection($userID,$tenantID);
            $collection = $class->getEntity($id);
            $title = $collection["name"];
             
        }
        catch(Exception $ex) {
            $errorMsg="Unable to load requested collection: " . $ex->getMessage();
            $title = "Not Found";
        }
    }   
    
    
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') . ': ' . $title?></title>
        <?php include("partials/includes.php"); ?>
        <link rel="stylesheet" type="text/css" href="static/css/feature.css" />
        <script src="js/collection.js" type="text/javascript"></script>
        <script type="text/javascript" src="<?php echo Config::$site_root?>/js/validator.js"></script>
        <script type="text/javascript" src="<?php echo Config::$site_root?>/js/bootpag.min.js"></script>
        <script type="text/javascript" src="<?php echo Config::$site_root?>/js/jquery.form.min.js"></script>
    </head>
    <body>
        <div id="maincontent">
            <div id="outer">
                <?php include('partials/header.php');
                ?>
                <div class="container featureContainer">
                    <?php if (strlen($errorMsg)>0) {
                        echo '<br/><h2>' . $errorMsg . '</h2>';
                    }
                    else { ?>
                        <input id="productCollectionId" type="hidden" value="<?php echo $id; ?>"/>
                        <input id="queryParams" type="hidden" value="<?php echo $collection["queryParams"]; ?>"/>
                         <?php if ($user->hasRole('admin',$tenantID)) {
                            $entityType = 'productCollection';
                            $callback = 'afterCollectionEdit';
                            include Config::$core_path . '/partials/entityEditModal.php';
                         } 
                        ?>
                        <div id="headline" class="headline"><h1><?php echo $collection["name"]?></h1></div>                        
                         <div id="feature-buttons" class="btn-group btn-default featureButtonGroup">
                            <button class="btn btn-default" id="editCollection" onclick="editEntity(<?php echo $id?>,'productCollection');"><span class="glyphicon glyphicon-pencil"></span> Edit Collection</button>
                        </div>
                        <div id="description" class="description"><p><?php echo $collection["description"];?></p></div>
                    <div id="collectionAnchor" class="collectionAnchor"></div>
                    <?php include("core/partials/workingPanel.php"); ?>      
                    <?php } ?>
                </div>
                <?php include("partials/footer.php")?>          
            </div>
        </div>
    </body>
</html>    