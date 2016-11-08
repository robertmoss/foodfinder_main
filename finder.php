<?php
	include dirname(__FILE__) . '/core/partials/pageCheck.php';
	include_once dirname(__FILE__) . '/core/classes/utility.php';
    
	$thisPage=Utility::getRequestVariable('type', 'finder');
    
    $zoom = Utility::getRequestVariable('zoom', 0);
    $list = Utility::getRequestVariable('list', 0);
    $selectedLocation = Utility::getRequestVariable('location', 0);
    
    Log::logPageView('finder', 0,'list=' . $list . '&selectedLocation=' . $selectedLocation);

 ?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') ?>: Finder</title>
		<?php include("partials/includes.php"); ?>
		<link rel="stylesheet" type="text/css" href="static/css/map.css" />
		<script src="js/main.js"></script>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB9Zbt86U4kbMR534s7_gtQbx-0tMdL0QA&libraries=places"></script> 
    </head>
    <body>
        <div id="topPart">
		    <?php include("partials/header.php"); ?>
			<!-- Modals -->
			<?php  
        		   $showNumLocations = true;
			       include("partials/configModal.php");
	               include("partials/locationModal.php");
	               include("partials/locationEditModal.php");
                   $defaultIcon = Utility::getTenantProperty($applicationID, $tenantID, $userID, 'defaultIcon');
	               if (strlen($defaultIcon)>0) {
	                   echo '<input type="hidden" id="defaultIcon" value="' . $defaultIcon. ' " />';
                   }       
	               ?>
        </div>
        <div class="mapPane">
            <div id="expandMap" class="mapEnlarge hidden" ><button class="btn btn-default btn-sm"  onclick="expandMap();"><span class="glyphicon glyphicon-resize-full" aria-hidden="true"></span></button></div>
            <div id="shrinkMap" class="mapEnlarge" ><button class="btn btn-default btn-sm"  onclick="shrinkMap();"><span class="glyphicon glyphicon-resize-small" aria-hidden="true"></span></button></div>
            <div id="mapwrapper" class="mapWrapper">
        		<div id="mapcanvas"></div>
        		<?php //include "partials/mapOptions.php";?>
        		<div id="loading" class="modal"><!-- Place inside div to cover --></div>
    		</div>
		</div>	
    	<div id="searchform2" class="container searchPanel hidden">
    		<form class="form-inline" action="#" onsubmit="retrieveResults('txtAddress','resultSpan');return false;">
    				<div class="form-group">
        				<div class="input-group">
        					<label class = "sr-only" for="txtAddress">Desired location</label>
							<span class="input-group-addon" id="basic-addon1" data-toggle="tooltip" title="Detect your current location" onclick="detectLocation('resultSpan');"><span class="glyphicon glyphicon-screenshot" aria-hidden="true"></span></span>
							<input id="txtAddress" type="text" class="form-control" placeholder="Enter your current (or desired) location" aria-describedby="basic-addon1" value="<?php echo Utility::getSessionVariable('currentAddress', 'none'); ?>"/>
						</div>
        				<button type="submit" class="btn btn-primary">Find</button>
        			</div>
					<input id="txtTenantID" type="hidden" value="<?php echo($_SESSION['tenantID']); ?>"/>
					<input id="txtCurrentLatitude" type="hidden" value="<?php echo Utility::getSessionVariable('latitude', ''); ?>"/>
					<input id="txtCurrentLongitude" type="hidden" value="<?php echo Utility::getSessionVariable('longitude', ''); ?>"/>
                    <input id="txtZoom" type="hidden" value="<?php echo $zoom; ?>"/>
                    <input id="txtList" type="hidden" value="<?php echo $list; ?>"/>
                    <input id="txtLocation" type="hidden" value="<?php echo $selectedLocation; ?>"/>
    		</form> 
    		<div id="message" class="alert alert-danger alert-dismissible hidden">
    			<button type="button" class="close" aria-label="Close" onclick="hideElement('message');"><span aria-hidden="true">&times;</span></button>
    			<span id='message_text'>Message goes here.</span>
    		</div>
		</div>
		<div id="mapcontent" class="container mapcontent">
				<div id="listNav" class="listNav">
					<div class="left">
					   <button class="btn btn-info" onclick="loadPrevLocation();"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true" ></span></button>
					   <button class="btn btn-info" onclick="loadNextLocation();"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true" ></span></button>
				    </div>
				    <div class="right">
                        <button id="showSearchBtn" type="button" class="btn btn-default" onclick="showSearch();"><span class="glyphicon glyphicon-triangle-bottom" aria-hidden="true"></span></button>
                        <button id="hideSearchBtn" type="button" class="btn btn-default hidden" onclick="hideSearch();"><span class="glyphicon glyphicon-triangle-top" aria-hidden="true"></span></button>
    				    <button type="button" class="btn btn-default" onclick="showConfig();"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span></button>
	                </div>
				</div> 
    			<div id="locationlist" class="row">
    				<div id="resultSpan"></div>
    				<div id="list-loader">
    					<div>
    						<div class="thumbnail loc-panel">
    							<div class="ajaxLoading"></div>
    						</div>
    					</div>
    				</div>
				</div>
		</div>        		
		<?php include("partials/footer.php");?>
    </body>
</html>