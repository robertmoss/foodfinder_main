<?php 
    include dirname(__FILE__) . '/core/partials/pageCheck.php';
    $thisPage="states";
    
    // get list of states to enable for tenant - set as Tenant Property using admin page
    $stateList = Utility::getTenantProperty($applicationID,$tenantID,$userID,'enabledStates');
    if (is_null($stateList)) {
        $stateList="";
    }
    
         
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Food Finder</title>
        <?php include("partials/includes.php"); ?>
        <script src="js/raphael.js" type="text/javascript" ></script>
        <script src="js/jquery.usmap.js" type="text/javascript"></script>
        <script src="js/states.js" type="text/javascript" ></script>
        <script src="js/content.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="static/css/statemap.css" >
            <div id="outer">
                <?php include('partials/header.php');?>
                <?php include('core/partials/contentControls.php');?>
                <div class="container">
                    <input id="stateList" type="hidden" value="<?php echo $stateList;?>"/>
                    <h1><?php echo Utility::renderContent('states:title', $_SESSION['tenantID'],$user); ?></h1>
                    <div class="col-md-8"><p><?php echo Utility::renderContent('states:welcomeText', $_SESSION['tenantID'],$user); ?></p></div>
                    <div id="map" class="statemap"></div>
                    <div id="clicked-state"></div>
                </div>  
                <?php include("partials/footer.php")?>
                </div>          
            </div>
        </div>
    </body>
</html>
    