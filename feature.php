<?php 
	include dirname(__FILE__) . '/core/partials/pageCheck.php';
     include_once Config::$root_path . '/classes/feature.php';
	$thisPage="feature";
    
    $id = Utility::getRequestVariable('id', 0);
    $errorMsg = "";
    if ($id==0) {
        $errorMsg ="You must specify a valid feature id.";
    }
    else {
        try {
            $class = new Feature($userID,$tenantID);
            $feature = $class->getEntity($id);
        }
        catch(Exception $ex) {
            $errorMsg="Unable to load requested feature: " . $ex->getMessage();
        }
    }	
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Food Finder</title>
        <?php include("partials/includes.php"); ?>
    </head>
    <body>
    	<div id="maincontent">
    		<div id="outer">
	    		<?php include('partials/header.php');?>
    			<div class="container">
    			    <?php if (strlen($errorMsg)>0) {
    			        echo '<br/><p>' . $errorMsg . '<p>';
    			    }
                    else {
                        ?>
    			    <input id="featureId" type="hidden" value="<?php echo $id; ?>"/>
    				<h1><?php echo $feature["headline"]?></h1>
    				<?php if (strlen($feature["subhead"])>0) {
    				   echo '<h2>' . $feature["subhead"] . '</h2>'; 
    				}
                    ?>
    				<?php } ?>
	        	</div>	
        		<?php include("partials/footer.php")?>     		
        	</div>
        </div>
    </body>
</html>
    