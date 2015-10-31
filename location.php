<?php 

	// DEPRECATED PAGE - USE entityPage.php TO MANAGE LOCATIONS NOW
	// WILL BE REMOVING FROM CODEBASE SOON


	include_once dirname(__FILE__) . '/partials/pageCheck.php';
	include_once dirname(__FILE__) . '/classes/core/database.php';
	include_once dirname(__FILE__) . '/classes/core/utility.php';
	
	$id=0;
	if (isset($_GET["id"])) {
		$id=$_GET["id"];
	}

	$mode = "view"; // the default mode	
	if (isset($_GET["mode"]))
		{
			$mode = $_GET["mode"];
		}

	if (!$id) {
		// assume creating a new location
		$id=0;
		}
	 elseif ($id!=0 && $mode=='edit') {
		// Right now, we use client side AJAX/mustache to render in 'view' mode
		// and server side direct query for building 'edit' mode form 
		$query = "call getLocationById(" . $id . "," . $tenantID . "," . $userID . ")";
		$data = Database::executeQuery($query);
		if (!$data) {
			$id=0;
		}
		else {
			while ($o=mysqli_fetch_object($data)) {
				$location = $o;
				Utility::debug('Retrieved data for ' . $location->name,5);
			}													
		}
	} 
	
	if ($id==0 && $mode!='edit')
		{
		Utility::debug('Location.php called with no id and non-edit mode', 1);
		Utility::errorRedirect("Unable to load location: no location specified in request.");
		}
	else{
		Utility::debug('Rendering location.php for location ID=' . $id, 5);
		}
	
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Location</title>
        <?php include("partials/includes.php"); ?>
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=places&sensor=false"></script>
		<script type="text/javascript" src="js/validator.js"></script>
		<script src="js/location.js"></script>		
    </head>
    <body>
    	<div id="maincontent">
    		<?php include('header.php');?>
    		<div id="outer">
    			<div id="main">
    				<input id="editMode" type="hidden" value="<?php echo $mode; ?>" />
    				<?php if ($mode!='edit') { ?>
    				<input type="hidden" id="id" name="id" value="<?php echo $id; ?>"/>
	        		<div id="location_pane" class="location_info">
	        			<div id="location_anchor">
							<!-- location info will be rendered here -->
						</div>
						<div class="functions">
							<button class="btn" type="button" value="<< Return to Search" onclick="window.location='index.php';" />
							<button class="btn" type="button" value="Edit" onclick="setMode('edit');" />
						</div>
						<div id="loading" class="modal"><!-- Place inside div to cover --></div>
	        		</div>
	        		<?php } ?>
	        		<?php if ($mode=='edit') { ?>
	        		<div class="location_edit centered">
	        			<form id="locationForm" class="form-horizontal" action="service/entityService.php?type=location" method="post">
			        		<div class="edit">
			        			<input type="hidden" id="id" name="id" value="<?php echo $id; ?>"/>
			        			<input type="hidden" name="tenantid" value="<?php echo $tenantID; ?>"/>
			        			<input id="txtCurrentLatitude" type="hidden" value="<?php echo Utility::getSessionVariable('latitude', ''); ?>"/>
								<input id="txtCurrentLongitude" type="hidden" value="<?php echo Utility::getSessionVariable('longitude', '');; ?>"/>
			        			<div class="form-group">
			        				<label for="txtName" class="col-sm-2 control-label" >Name:</label>
			        				<div class="col-sm-6">
			        					<input id="txtName" name="name" type="text" class="form-control" placeholder="name" value="<?php if ($id>0) {echo $location->name;} ?> " required>
										<button class="btn btn-default" onclick="checkGooglePlaces();">Check Google Places</button>
										<div class="help-block with-errors"></div>
			        				</div>
			        			</div>
			        			<div id="mapwrapper" class="hidden">
			        				<div id="mapcanvas"></div>
			        			</div>
			        			<div class="form-group">
			        				<label for="txtAddress" class="col-sm-2 control-label">Street Address:</label>
			        				<div class="col-sm-4">
			        					<input id="txtAddress" name="address" type="text" class="form-control" placeholder="street address" 
			        						<?php if ($id>0) {echo 'value = "' . $location->address . '"';} ?>>
									</div>
								</div>		        			
			        			<div class="form-group">
			        				<label for="txtCity" class="col-sm-2 control-label">City:</label>
			        				<div class="col-sm-2">	
			        					<input id="txtCity" name="city" type="text" class="form-control" placeholder="city" value="<?php if ($id>0) {echo $location->city;} ?>">
			        				</div>
			        				<label for="txtState" class="col-sm-1 control-label">State:</label>
			        				<div class="col-sm-1">
			        					<input id="txtState" name="state" type="text" class="form-control" placeholder="state" value="<?php if ($id>0) {echo $location->state;} ?>">
			        				</div>
			        			</div>
			        			<div class="form-group">
			        				<label for="txtPhone" class="col-sm-2 control-label">Phone:</label>
			        				<div class="col-sm-2">
			        					<input id="txtPhone" name="phone" type="text" class="form-control" placeholder="phone" value="<?php if ($id>0) {echo $location->phone;} ?>" />
									</div>
								</div>	
			        			<div class="form-group">
			        				<label for="txtURL" class="col-sm-2 control-label">Website URL</label>
			        				<div class="col-sm-2">
			        					<input id="txtURL" name="url" type="text" class="form-control" placeholder="website url" value="<?php if ($id>0) {echo $location->url;} ?>" />	
									</div>
									<div class="col-sm-2">
			        					<a href="#" onclick="visitURL();">Visit Link</a>
			        				</div>
								</div>	
								<div class="form-group">
			        				<label for="imageURL" class="col-sm-2 control-label">Image URL</label>
			        				<div class="col-sm-4">
			        					<input id="imageURL" name="imageurl" type="text" class="form-control" placeholder="image URL" value="<?php if ($id>0) {echo $location->imageurl;} else {echo 'img/us/none.png';} ?>" onblur="loadImagePreview();">
									</div>
								</div>
								<div class="form-group">
			        				<label for="txtLatitude" class="col-sm-2 control-label">Latitude:</label>
			        				<div class="col-sm-2">
			        					<input id="txtLatitude" name="latitude" type="text" class="form-control" placeholder="latitude" value="<?php if ($id>0) {echo $location->latitude;} ?>" />	
									</div>
									<label for="txtLongitude" class="col-sm-1 control-label">Longitude:</label>
			        				<div class="col-sm-2">
			        					<input id="txtLongitude" name="longitude" type="text" class="form-control" placeholder="longitude" value="<?php if ($id>0) {echo $location->longitude;} ?>" />	
									</div>
								</div>
								<div class="form-group">
			        				<label for="shortdescription" class="col-sm-2 control-label">Short Description</label>
			        				<div class="col-sm-4">
			        					<textarea id="shortdescription" name="shortdescription" type="text" class="form-control" placeholder="short description"><?php if ($id>0) {echo $location->shortdescription;}  ?></textarea>
			        				</div>
								</div>
								<div class="form-group">
									<label for="longdescription" class="col-sm-2 control-label">Long Description</label>
			        				<div class="col-sm-4">
			        					<textarea id="longdescription" name="longdescription" type="text" class="form-control" placeholder="long description"><?php if ($id>0) {echo $location->description;}  ?></textarea>
			        				</div>
								</div>
			        			<div class="form-group">
			        				<label for="txtGoogleReference" class="col-sm-3 control-label">Google Places Reference #</label>
			        				<div class="col-sm-3">
			        					<input id="txtGoogleReference" name="googleReference" type="text" class="form-control" placeholder="Google Places Ref # (readonly)" readonly value="<?php if ($id>0) {echo $location->googleReference;} ?>">
			        				</div>
			        			</div>
								<div class="form-group">
			        				<label for="txtPlacesId" class="col-sm-3 control-label">Google Places ID</label>
			        				<div class="col-sm-3">
			        					<input id="txtGooglePlacesId" name="googlePlacesId" type="text" class="form-control" placeholder="Google Places ID (readonly)" readonly value="<?php if ($id>0) {echo $location->googlePlacesId;} ?>">
			        				</div>
			        			</div>
			        			<div id="Categories" class="well">
			 						<h2>Categories</h2>
			 						<?php 
		        							$query = "call getCategoriesByLocationID(" . $id . "," . $tenantID . ")";
											$data = Database::executeQuery($query);
											if (!$data) {
												echo '<p>No categories defined.</p>';
												}	
											else {
												$count = 0;
												while ($link=mysqli_fetch_object($data)) {
													$count++;
													?>
													<div class="row">
														<p><a href="#" onclick="deleteLink(<?php echo $link->id ?>);"><img src="img/icons/delete.png" alt="delete"/></a>&nbsp;<b>
															<a href="<?php echo $link->link ?>" target="_blank">
															<?php echo $link->title .'</b>' ?>
															<?php if (strlen($link->publication)>0) {echo ' (' . $link->publication . ')';} ?>
															</a>
														</p>		
													</div>
									<?php				
													}													
											}
									?>
									<div class="row">
										<?php if ($id==0) { ?>
											<p>Save record to add categories.</p>
										<?php } else { ?>
											<button type="button" onclick="addCategory();" class="btn btn-default"><img src="img/icons/addnew_green.jpeg" alt="Add New"/>Add New</button>
										<?php } ?>
									</div>	
			 					</div>
		        				<div i
			 					<div id="links" class="well">
			 						<h2>Links</h2>
			 						<?php 
		        							$query = "call getLinksByLocationID(" . $id . "," . $tenantID . ")";
											$data = Database::executeQuery($query);
											if (!$data) {
												echo '<p>No links yet.</p>';
												}	
											else {
												$count = 0;
												while ($link=mysqli_fetch_object($data)) {
													$count++;
													?>
													<div class="row">
														<p><a href="#" onclick="deleteLink(<?php echo $link->id ?>);"><img src="img/icons/delete.png" alt="delete"/></a>&nbsp;<b>
															<a href="<?php echo $link->link ?>" target="_blank">
															<?php echo $link->title .'</b>' ?>
															<?php if (strlen($link->publication)>0) {echo ' (' . $link->publication . ')';} ?>
															</a>
														</p>		
													</div>
									<?php				
													}													
											}
									?>
									<div class="row">
										<?php if ($id==0) { ?>
											<p>Save record to add links.</p>
										<?php } else { ?>
											<button type="button" onclick="addEndorsement();" class="btn btn-default"><img src="img/icons/addnew_green.jpeg" alt="Add New"/>Add New</button>
											<button type="button" onclick="addLink();" class="btn btn-default"><img src="img/icons/addnew_green.jpeg" alt="Add New"/>Add New</button>
										<?php } ?>
									</div>	
			 					</div>
		        				<div id="endorsements" class="well">
		        					<h2>Endorsements</h2>
		        					<?php 
		        							$query = "call getEndorsementsByLocationID(" . $id . "," . $tenantID . ")";
											$data = Database::executeQuery($query);
											if (!$data) {
												echo '<p>No endorsements yet.</p>';
												}	
											else {
												$count = 0;
												while ($endorsement=mysqli_fetch_object($data)) {
													$count++;
													?>
													<div class="row">
														<p><a href="#" onclick="deleteEndorsement(<?php echo $endorsement->id ?>);"><img src="img/icons/delete.png" alt="delete"/></a>&nbsp;<b><?php echo $endorsement->endorserName .'</b> (' . $endorsement->date . ')'; ?>
															<?php if (strlen($endorsement->comments)>0) {echo ': "<em>' . $endorsement->comments . '</em>"';} ?>
														</p>		
													</div>
									<?php				
													}													
											}
									?>
									<div class="row">
										<?php if ($id==0) { ?>
											<p>Save record to add endorsements.</p>
										<?php } else { ?>
											<button type="button" onclick="addEndorsement();" class="btn btn-default"><img src="img/icons/addnew_green.jpeg" alt="Add New"/>Add New</button>
										<?php } ?>
									</div>		
		        				</div>
		        				<div id="messageDiv" class="message hidden">
		        					<span id="messageSpan"><p>Your message here!</p></span>
		        				</div>
		        				<div class="btn-group  btn-group-lg" role="group" aria-label="...">
		        					<button name="save" type="button" class="btn btn-primary" onClick="submitForm('locationForm','messageDiv','messageSpan',true);">Save</button>
		        					<button name="addnew" type="button" class="btn btn-default" onClick="submitForm('locationForm','messageDiv','messageSpan',true); addNew();"> Save &amp; New</button>
		        					<button name="cancel" type="button" class="btn btn-default" onClick="setMode('view');" >Cancel</button>
		        				</div>
	        				</form>
	        			</div>
	        			<div id="addEndorsement" class="edit modaldialog">
							<div class="modalinner">
								<form id="frmAddEndorsement" action="service/endorsement.php" method="post">
											<p>Add New Endorsement:	</p>
											<input type="hidden" name="type" value="location" />
											<input type="hidden" name="locationid" value="<?php echo $location->id ?>" />
											<input type="hidden" name="id" value="0" />
											<div class="row">
												<span class="label">Endorsed by: </span>
												<span class="input">
													<select name="userid">
					        							<?php
															$query = "call getEndorsers(" . $tenantID . ")";
															$categories = Database::executeQuery($query);
															if (!$categories) {
																Utility::errorRedirect('Error retrieving endorsing users: ' . mysql_error());
															}
															else {
																while ($r=mysqli_fetch_array($categories)) {
																	echo 	'<option value="' . $r[0].'">' . $r[1] . '</option>';	
																}
															}
														?>
					        						</select>
					        					</span>
			        						</div>
			        						<div class="row">
			        							<span class="label">Date:</span>
			        							<span class="date_input"><input name="date" id="date" type="text" value="<?php echo date('Y-m-d H:i:s'); ?>" /></span>
			        						</div>
			        						<div class="row">
			        							<span class="label">Comments:</span>
			        							<span class="input"><textarea name="comments" placeholder="comments" type="text"></textarea></span>
			        						</div>
			        						<div class="row">
												<input type="button" class="btn" value="Cancel" onClick="hideElement('messageDiv2'); hideElement('addEndorsement');"/>
												<input type="button" class="btn primary_button" value="Save" onClick="submitForm('frmAddEndorsement','messageDiv2','messageSpan2',true);"/>
											</div>
											<div id="messageDiv2" class="message hidden">
		        								<span id="messageSpan2"><p>Your message here!</p></span>
		        							</div>
									</form>
								</div>
						</div>
						<div id="addLink" class="edit modaldialog">
							<div class="modalinner">
								<form id="frmAddLink" action="service/link.php" method="post">
										<p>Add New Link:	</p>
										<input type="hidden" name="type" value="location" />
										<input type="hidden" name="locationid" value="<?php echo $location->id ?>" />
										<input type="hidden" name="id" value="0" />

			        					<div class="row">
			        						<span class="label">Link URL:</span>
			        						<span class="input"><input name="link" id="link" placeholder="link" type="text" value="" /></span>
			        					</div>
			        					<div class="row">
			        						<span class="label">Author:</span>
			        						<span class="input"><input name="author" id="author" placeholder="author" type="text" value="" /></span>
			        					</div>
			        					<div class="row">
			        						<span class="label">Title:</span>
			        						<span class="input"><input name="title" id="title" type="text" placeholder="title" value="" /></span>
			        					</div>
			        					<div class="row">
			        						<span class="label">Publication:</span>
			        						<span class="input"><input name="publication" id="title" type="text" placeholder="publication" value="" /></span>
			        					</div>
			        					<div class="row">
												<input type="button" class="btn" value="Cancel" onClick="hideElement('messageDiv3'); hideElement('addLink');"/>
												<input type="button" class="btn primary_button" value="Save" onClick="submitForm('frmAddLink','messageDiv3','messageSpan3',true);"/>
											</div>
											<div id="messageDiv3" class="message hidden">
		        								<span id="messageSpan3"><p>Your message here!</p></span>
		        							</div>
									</form>
								</div>
							</div>
							
	        			<?php } ?>
	        		</div>
	        		<?php include("footer.php");?>
	        	</div>     		
        	</div>
        </div>
    </body>
</html>