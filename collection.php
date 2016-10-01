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
    </head>
    <body>
        <div id="maincontent">
            <div id="outer">
                <?php include('partials/header.php');
                ?>
                <div class="container featureContainer">
                    <?php if (strlen($errorMsg)>0) {
                        echo '<br/><p>' . $errorMsg . '<p>';
                    }
                    else { ?>
                        <input id="productCollectionId" type="hidden" value="<?php echo $id; ?>"/>
                        <input id="queryParams" type="hidden" value="<?php echo $collection["queryParams"]; ?>"/>
                         <?php if ($user->hasRole('admin',$tenantID)) {
                            $entityType = 'collection';
                            $callback = null;
                            include dirname(__FILE__) . '/core/partials/entityEditModal.php';
                        ?>
                        <div id="headline" class="headline"><h1><?php echo $collection["name"]?></h1></div>
                        
                    <div id="feature-buttons" class="btn-group btn-default featureButtonGroup">
                        <button class="btn btn-default" id="editFeature" onclick="editEntity(<?php echo $id?>,'feature');"><span class="glyphicon glyphicon-pencil"></span> Edit Feature</button>
                    </div>
                    <?php } ?>
                    <?php } ?>
                </div>
                <?php include("partials/footer.php")?>          
            </div>
        </div>
    </body>
</html>    