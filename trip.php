<?php 
	include dirname(__FILE__) . '/core/partials/pageCheck.php';
	$thisPage="trip";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') ?>: Plan a Trip</title>
        <?php include("partials/includes.php"); ?>
        <link rel="stylesheet" type="text/css" href="static/css/trip.css" />
    
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB9Zbt86U4kbMR534s7_gtQbx-0tMdL0QA&sensor=true&libraries=places"></script>	
		<script src="js/trip.js"></script>

		
    </head>
    <body>
    	<div id="maincontent">
	    	<?php include 'partials/header.php';
	    	      $showNumLocations = false;
			     include 'partials/configModal.php';
			?>
    		<div id="outer">
    			<div id="main">
    				<div id="searchform">
    					<div id="loading" class="modal"><!-- Place inside div to cover --></div>
    					<div class="panel panel-default container toppad">
		    				<div id="formPanel" class="container collapse in">
		    					<div>
				    				<form action="#" class="form-horizontal" onsubmit="getRoute(); return false;">
				    					<div class="form-group">
				    						<label for="txtOrigin" class="col-sm-3 control-label">Starting Location</label>
				    						<div class="col-sm-6">
				    							<div class="input-group">
                                                    <span class="input-group-addon" id="basic-addon1" data-toggle="tooltip" title="Detect your current location" onclick="detectLocation('resultSpan');"><span class="glyphicon glyphicon-screenshot" aria-hidden="true"></span></span>
				    								<input id="txtOrigin" type="text" class="form-control" placeholder="Your starting location"></input>
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
				    						<button type="button" class="btn btn-default" onclick="showConfig();"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Options</button>
				    						<button id="tripSubmit" class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Go</button>
                                        </div>
				    					<input id="txtCurrentLatitude" type="hidden" value=""/>
										<input id="txtCurrentLongitude" type="hidden" value=""/>			        				
				    				</form>

			        			</div>
										        			
		        			</div>
		        			<div id="tripSummary">
		        				<div class="routeSummary">
			        				<span id="tripSummaryText"></span>
			        			</div>
			        			<div class="toggle">
			        				<a id="toggleLink" class="btn btn-default hidden" role="button" data-toggle="collapse" href="#formPanel" aria-expanded="false" aria-controls="collapseExample" >
	 									<span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span> Hide</a>
								</div>
			        		</div>
	        			</div>
	        			<div id="results" class="container results">
		        			<div class="tripNav">
			        			<ul class="nav nav-pills" role="tablist">
									<li role="presentation" class="active"><a href="#mapwrapper" aria-controls="mapwrapper" role="tab" data-toggle="tab">Map</a></li>
									<li role="presentation"><a href="#directions" aria-controls="directions" role="tab" data-toggle="tab">Directions</a></li>
									<li role="presentation"><a href="#locationListTab" aria-controls="locationListTab" role="tab" data-toggle="tab">List</a></li>
								</ul>
		        			</div>
		        			<div class="tab-content">
			        			<div role="tabpanel" class="tab-pane" id="locationListTab" class="styledpanel">
									<h4>Here is the list of locations that you will pass near on your way.</h4>
									<div id="locationList">
    									<p>No locations retrieved yet.</p>
                                    </div>	        					
			        			</div>
			    				<div role="tabpanel" class="tab-pane active" id="mapwrapper">
			    					<div id="mapheading"></div>
				        			<div id="mapcanvas"></div>
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
	            <?php include 'partials/locationModal.php' ?>
        		<?php include 'partials/footer.php'?>     		
        	</div>
        </div>
    </body>
</html>
    