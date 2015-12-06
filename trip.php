<?php 
	include dirname(__FILE__) . '/partials/pageCheck.php';
	$thisPage="trip";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') ?>: Plan a Trip</title>
        <link rel="stylesheet" type="text/css" href="static/css/styles.css" />
        <link rel="stylesheet" type="text/css" href="static/css/bootstrap.css" />	
        <link rel="stylesheet" type="text/css" href="static/css/foodfinder.css" />
        <link rel="stylesheet" type="text/css" href="static/css/trip.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'css'); ?>" />	
    
		<script src="js/jquery-1.10.2.js"></script>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB9Zbt86U4kbMR534s7_gtQbx-0tMdL0QA&sensor=true&libraries=places"></script>	
		<script src="js/mustache.js"></script>
		<script src="js/core.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/foodfinder.js"></script>
		<script src="js/trip.js"></script>

		
    </head>
    <body>
    	<div id="maincontent">
	    	<?php include('header.php');?>
			<?php include("partials/configModal.php"); ?>
    		<div id="outer">
    			<div id="main">
    				<div id="searchform">
    					<div class="panel panel-default container toppad">
		    				<div id="formPanel" class="container collapse in">
		    					<div>
				    				<form action="#" class="form-horizontal" onsubmit="getRoute(); return false;">
				    					<div class="form-group">
				    						<label for="txtOrigin" class="col-sm-3 control-label">Starting Location</label>
				    						<div class="col-sm-6">
				    							<div class="input-group">
				    								<input id="txtOrigin" type="text" class="form-control" placeholder="Your starting location"></input>
													<span class="input-group-addon" onclick="detectLocation('resultSpan');"><span class="glyphicon glyphicon-map-marker	`" aria-hidden="true"></span></span>		    							
				    							</div>
				    						</div>
				    					</div>
				    					<div class="form-group">
				    						<label for="txtDestination" class="col-sm-3 control-label">Destination</label>
				    						<div class="col-sm-6">
				    							<input id="txtDestination" type="text" class="form-control" placeholder="Your destination"></input>
				    						</div>
				    					</div>
				    					<div class="form-group">
				    						<label for="txtDetour" class="col-sm-3 control-label">Max. Detour</label>
				    						<div class="col-sm-2">
				    							<div class="input-group">
				    								<input id="txtDetour" type="text" class="form-control" placeholder="Your destination" value="25"/>
				    							 	<span class="input-group-addon">miles</span>		    							
				    							</div>
				    						</div>
				    					</div>
					    				<div id="message" class="hidden">
					        				<a class="close_link" href="#" onclick="hideElement('message');"></a>
					        				<span id='message_text'>Message goes here.</span>
					        			</div>
				    					<div class="form-group">
				    						<label for="tripSubmit" class="col-sm-3 control-label"></label>
				    						<button id="tripSubmit" class="btn btn-primary" onclick="getRoute();">Find Me Some <?php echo ucfirst(Utility::getTenantProperty($applicationID, $tenantID, $userID,'finditem')); ?></button>
				    						<button type="button" class="btn btn-default" onclick="showConfig();"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span></button>
				    					</div>
				    					<input id="txtCurrentLatitude" type="hidden" value=""/>
										<input id="txtCurrentLongitude" type="hidden" value=""/>			        				
				    				</form>

			        			</div>
										        			
		        			</div>
		        			<div id="tripSummary">
		        				<div class="routeSummary">
			        				<span id="tripSummaryText">No route selected yet.</span>
			        			</div>
			        			<div class="toggle">
			        				<a id="toggleLink" class="btn btn-default" role="button" data-toggle="collapse" href="#formPanel" aria-expanded="false" aria-controls="collapseExample" >
	 									<span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span> Hide</a>
								</div>
			        		</div>
	        			</div>
	        			<div id="results" class="container results">

		        			<div class="tripNav">
			        			<ul class="nav nav-pills" role="tablist">
									<li role="presentation" class="active"><a href="#mapwrapper" aria-controls="mapwrapper" role="tab" data-toggle="tab">Map</a></li>
									<li role="presentation"><a href="#directions" aria-controls="directions" role="tab" data-toggle="tab">Directions</a></li>
									<li role="presentation"><a href="#locationList" aria-controls="locationList" role="tab" data-toggle="tab">List</a></li>
								</ul>
		        			</div>
		        			<div class="tab-content">
			        			<div role="tabpanel" class="tab-pane" id="locationList" class="styledpanel">
									<p>No locations retrieved yet.</p>	        					
			        			</div>
			    				<div role="tabpanel" class="tab-pane active" id="mapwrapper">
			    					<div id="mapheading"></div>
				        			<div id="mapcanvas"></div>
				        			<div id="loading" class="modal"><!-- Place inside div to cover --></div>
			        			</div>
			        			<div role="tabpanel" class="tab-pane"  id="directions">
			        				<div class="panel-group" id="directions-accordion" role="tablist" aria-multiselectable="true">
										<div id="directionsZone"><p>No locations retrieved yet.</p></div>
									</div>	        					
			        			</div>
		        			</div>
	        			</div>
	    			</div>
	        	</div>
	            <?php include("partials/locationModal.php")?>
        		<?php include("footer.php")?>     		
        	</div>
        </div>
    </body>
</html>
    