<?php
	include dirname(__FILE__) . '/partials/pageCheck.php';
	include_once dirname(__FILE__) . '/classes/core/utility.php';
	$thisPage="finder";	
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
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3&key=AIzaSyB9Zbt86U4kbMR534s7_gtQbx-0tMdL0QA"></script>
    </head>
    <body>
		<?php include("header.php"); ?>
			<!-- Modals -->
			<?php include("partials/configModal.php"); ?>
	        <?php include("partials/locationModal.php")?>
	        <?php include("partials/locationEditModal.php")?>
			<div id="mapwrapper">
        		<div id="mapcanvas"></div>
        		<div id="loading" class="modal"><!-- Place inside div to cover --></div>
    		</div>	        		
    		<div id="searchform2" class="container searchPanel">
        		<form class="form-inline" action="#" onsubmit="retrieveResults('txtAddress','resultSpan');return false;">
        				<div class="form-group">
	        				<div class="input-group">
	        					<label class = "sr-only" for="txtAddress">Desired location</label>
								<span class="input-group-addon" id="basic-addon1" data-toggle="tooltip" title="Detect your current location" onclick="detectLocation('resultSpan');"><span class="glyphicon glyphicon-screenshot" aria-hidden="true"></span></span>
								<input id="txtAddress" type="text" class="form-control" placeholder="Your current or desired address/location" aria-describedby="basic-addon1" >
							</div>
	        				<button type="submit" class="btn btn-primary">Find</button>
	        				<button type="button" class="btn btn-default" onclick="showConfig();"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span></button>
	        			</div>
						<input id="txtTenantID" type="hidden" value="<?php echo($_SESSION['tenantID']); ?>"/>
						<input id="txtCurrentLatitude" type="hidden" value="<?php echo Utility::getSessionVariable('latitude', ''); ?>"/>
						<input id="txtCurrentLongitude" type="hidden" value="<?php echo Utility::getSessionVariable('longitude', '');; ?>"/>
        		</form> 
        		<div id="message" class="alert alert-danger hidden">
        			<a class="close_link" href="#" onclick="hideElement('message');"></a>
        			<span id='message_text'>Message goes here.</span>
        		</div>
    		</div>
			<div id="mapcontent" class="container mapcontent">
				<div id="listNav">
					<button class="btn btn-info" onclick="loadPrevLocation();"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true" ></span></button>
					<button class="btn btn-info" onclick="loadNextLocation();"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true" ></span></button>
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
		<?php include("footer.php");?>
    </body>
</html>