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
        <title><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') . ': ' . $feature["headline"] ?></title>
        <?php include("partials/includes.php"); ?>
        <link rel="stylesheet" type="text/css" href="static/css/feature.css" />
        <script type="text/javascript" src="js/feature.js"></script>
        <?php include("partials/facebookMeta.php"); ?>
        <meta property="og:title"       content="<?php echo $feature['headline'] ?>" />
        <meta property="og:description" content="<?php echo $feature['subhead'] ?>" />
        <meta property="og:image" content="<?php echo Config::getSiteRoot() . '/' . $feature['coverImage'] ?>" />
    </head>
    <body>
    	<div id="maincontent">
    		<div id="outer">
	    		<?php include('partials/header.php');
                    include("partials/locationModal.php");
                    include("partials/locationEditModal.php");
                ?>
    			<div class="container featureContainer">
    			    <?php if (strlen($errorMsg)>0) {
    			        echo '<br/><p>' . $errorMsg . '<p>';
    			    }
                    else {
                        ?>
    			    <input id="featureId" type="hidden" value="<?php echo $id; ?>"/>
    			    <input id="locationCriteria" type="hidden" value="<?php echo $feature["locationCriteria"]; ?>"/>
    			    <?php
                         $return = 10; // default of service unless specific in query string       			     
    			         $pos1 = strpos($feature["locationCriteria"],"return=");
                         if ($pos1>=0) {
                             $pos2 = strpos($feature["locationCriteria"],"&",$pos1);
                             if (!$pos2) {
                                 // no & found; must be at end of criteria
                                 $return = substr($feature["locationCriteria"],$pos1+7);
                             }
                             else {
                                  $return = substr($feature["locationCriteria"],$pos1+7,$pos2-$pos1-7);                                 
                             } 
                         } 
    			    ?>
    			    <input id="maxLocations" type="hidden" value="<?php echo $return; ?>"/>
    			    <input id="numberEntries" type="hidden" value="<?php echo $feature["numberEntries"]; ?>"/>
    			    <?php if ($user->hasRole('admin',$tenantID)) {
    			        $entityType = 'feature';
                        $callback = "afterFeatureEdit";
    			        include dirname(__FILE__) . '/core/partials/entityEditModal.php';
    			        ?>
    			    <div id="feature-buttons" class="btn-group btn-default featureButtonGroup">
                        <button class="btn btn-default" id="editFeature" onclick="editEntity(<?php echo $id?>,'feature');"><span class="glyphicon glyphicon-pencil"></span> Edit Feature</button>
                    </div>
    			    <?php } ?>
    			    <div id="headline" class="headline"><h1><?php echo $feature["headline"]?></h1></div>
    			    <div id="subhead" class="featureHeading">
    			    <?php if (strlen($feature["subhead"])>0) {
                           echo '<h2>' . $feature["subhead"] . '</h2>'; 
                            }
                        if (strlen($feature["author"])>0) {
                                echo '<p class="author">By ' . $feature["author"] . "</p>";
                            }
                            if (strlen($feature["datePosted"])>0) {
                                $postDate = new DateTime($feature["datePosted"]);
                                echo '<p class="postdate">Posted ' . $postDate->format('F d, Y') . "</p>";
                            }
                    ?>
                    </div>
    			    <div id="featureSocialBar">
    			        <?php
    			             $text = urlencode($feature["headline"]);
                             $url = urlencode(Config::getSiteRoot() . $_SERVER['REQUEST_URI']);
    			        ?>
    			        <ul class="socialList">
    			             <li><a class="social icon icon-twitter" href="http://twitter.com/intent/tweet?text=<?php echo $text; ?>&amp;via=bbqhub&amp;url"=<?php echo $url;?>" target="_blank" rel="nofollow" title="Share on Twitter" aria-label="Share on Twitter"></a></li>
    			             <li><a class="social icon icon-facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" target="_blank" rel="nofollow" title="Share on Facebook" aria-label="Share on Facebook"></a></li>
    			        </ul>
    			   </div>
                    <div id="coverImage" class="coverImage"><img src="<?php echo $feature["coverImage"];?>"/></div>
    				<div id="openingContent">
    				    <div class="featureContent">
                            <p class="featureBodyText"><?php echo Utility::renderWebContent($feature["introContent"]); ?></p>
                        </div>
                        <?php if (strlen($feature["locationCriteria"])>0) { ?>
                        <div class="featureLaunch">
                            <button class="btn btn-primary" id="viewSlideshow" onclick="launchSlideshow();">Let's Get Started <span class="glyphicon glyphicon-play"></span></button>
                        </div>
                        <?php } ?> 
                    </div>
                    <div id="locationAnchor" class="hidden"></div>
                    <?php if (strlen($feature["locationCriteria"])>0) { ?>
                        <input id="txtList" type="hidden" value="<?php echo $feature["locationCriteria"]; ?>"/>
                    <?php } ?>
                    <?php include"(partials/workingPanel.php)";?> 
                    <div id="closingContent" class="featureContent hidden">
                        <div class="featureContent">
                            <p class="featureBodyText"><?php echo $feature["closingContent"] ?></p>
                        </div>
                        <div class="featureLaunch">
                            <button class="btn btn-primary" id="viewSlideshowAgain" onclick="launchSlideshow();">View Again <span class="glyphicon glyphicon-play"></span></button>
                        </div>
                    </div>
                    <?php } ?>
	        	</div>	
        		<?php include("partials/footer.php")?>     		
        	</div>
        </div>
    </body>
</html>
    