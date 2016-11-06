<?php 
	include dirname(__FILE__) . '/core/partials/pageCheck.php';
    include_once dirname(__FILE__) . '/core/classes/log.php';
    include_once dirname(__FILE__) . '/core/classes/format.php';
    include_once Config::$root_path . '/classes/feature.php';
	
	$thisPage="feature";
    
    $id = Utility::getRequestVariable('id', 0);
    $errorMsg = "";
    $preview="";
    if ($id==0) {
        $errorMsg ="You must specify a valid feature id.";
    }
    else {
        try {
            $class = new Feature($userID,$tenantID);
            $feature = $class->getEntity($id);
            if (strtolower($feature["status"])!="published") {
                // if contributor, allow  to preview and add preview stripe
                if ($user->hasRole("admin", $tenantID) || $user->hasRole("contributor", $tenantID)) {
                    $preview = "You are previewing a feature that is currently in <strong>" . $feature["status"] . '</strong> status.';    
                }
                else {
                    $errorMsg="We don't seem to be able to find what you're looking for.";
                }
            }
            else {
                // don't log page views for unpublished feature: distorts counts
                Log::logPageView('feature', $id,'');
            }
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
        <meta property="og:type"        content="article" />
        <meta property="og:description" content="<?php echo $feature['subhead'] ?>" />
        <meta property="og:image"       content="<?php echo Config::getSiteRoot() . '/' . $feature['coverImage'] ?>" />
        <meta property="og:image:width" content="" />
        <meta property="og:image:height" content="" />
        <meta property="og:url"         content="<?php echo Config::getSiteRoot();?>/feature.php?id=<?php echo $id?>" />
        <?php include("partials/twitterScript.php"); ?>
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <script>
          (adsbygoogle = window.adsbygoogle || []).push({
            google_ad_client: "ca-pub-0081868233628623",
            enable_page_level_ads: true
          });
        </script>
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
    			        echo '<h2>Hmm . . .</h2>';
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
    			    <?php if (strlen($preview)>0) {
                            echo '<div class="alert alert-warning" role="alert">' . $preview .'</div>';
                        }
                    if ($user->hasRole('admin',$tenantID)) {
    			        $entityType = 'feature';
                        $callback = "afterFeatureEdit";
                        $modalSize = "large";
    			        include dirname(__FILE__) . '/core/partials/entityEditModal.php';
                        $modalSize = "";
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
                        if (strlen($feature["authorName"])>0) {
                                echo '<p class="author">By <a href="author.php?id=' . $feature["author"] . '">' . $feature["authorName"] . "</a></p>";
                            }
                            if (strlen($feature["datePosted"])>0) {
                                echo '<p class="postdate">Posted ' . Format::formatDateLine($feature["datePosted"], true) . "</p>";
                            }
                    ?>
                    </div>
    			    <div id="featureSocialBar">
    			        <?php
    			             $text = urlencode($feature["headline"]);
                             $url = urlencode(Config::getSiteRoot() . '/feature.php?id='. $id .  '&ch=t');
    			        ?>
    			        <ul class="socialList">
     			             <li><a class="social icon icon-twitter" href="http://twitter.com/intent/tweet?text=<?php echo $text; ?>&amp;url=<?php echo $url;?>&amp;via=thebbqhub" target="_blank" rel="nofollow" title="Share on Twitter" aria-label="Share on Twitter"></a></li>
    			             <li><a class="social icon icon-facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" target="_blank" rel="nofollow" title="Share on Facebook" aria-label="Share on Facebook"></a></li>
    			        </ul>
    			   </div>
                    <div id="coverImage" class="coverImage"><img src="<?php echo $feature["coverImage"];?>"/></div>
    				<div id="openingContent">
    				    <div class="featureContent">
                            <div class="featureBodyText"><?php echo Utility::renderWebContent($feature["introContent"]); ?></div>
                        </div>
                        <?php if (strlen($feature["locationCriteria"])>0) { ?>
                        <div class="featureLaunch">
                            <button class="btn btn-primary" id="viewSlideshow" onclick="launchSlideshow();"><span class="glyphicon glyphicon-play"></span> Let's Get Started</button>
                        </div>
                        <?php } ?> 
                    </div>
                    <div id="locationAnchor" class="hidden"></div>
                    <?php
                        $hideClosing=false; 
                        if (strlen($feature["locationCriteria"])>0) {
                            $hideClosing = true;
                        ?>
                        <input id="txtList" type="hidden" value="<?php echo $feature["locationCriteria"]; ?>"/>
                    <?php }
                        else {
                            echo '<hr/>';
                        }
                     ?>
                    <?php include("core/partials/workingPanel.php");?> 
                    <div id="closingContent" class="featureContent<?php if ($hideClosing) { echo ' hidden'; }?>">
                        <div class="featureContent">
                            <p class="featureBodyText"><?php echo Utility::renderWebContent($feature["closingContent"]) ?></p>
                            <?php include("partials/twitterFollowButton.php");?> 
                        </div>
                    </div>
                    <div id="featureNav" class="featureLaunch hidden">
                            <button class="btn btn-default" id = "viewOnMap" onclick="viewOnMap();"><span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span> Map View</button>
                            <button class="btn btn-default" id="viewSlideshowAgain" onclick="restartSlideshow();"><span class="glyphicon glyphicon-repeat"></span> Restart</button>
                            <button class="btn btn-primary" id="viewNext" onclick="moveNextSlide();"><span class="glyphicon glyphicon-play"></span> Next</button>
                    </div>
                    <?php } ?>
	        	</div>	
        		<?php include("partials/footer.php")?>     		
        	</div>
        </div>
    </body>
</html>
    