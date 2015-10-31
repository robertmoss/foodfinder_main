<?php 

	include dirname(__FILE__) . '/partials/pageCheck.php';
	include_once dirname(__FILE__) . '/classes/core/database.php';
	include_once dirname(__FILE__) . '/classes/core/utility.php';
	include_once dirname(__FILE__) . '/classes/core/dataentity.php';
	$thisPage = "entityPage";
	
	$id=0;
	$parentid=0; // used to default parent when creating child entities
	$returnurl = '';
	if (isset($_GET["id"])) {
		$id=$_GET["id"];
	}
		
	if (isset($_GET["type"])) {
		$type=$_GET["type"];
	}
	else {
		echo 'Type is required';
		header(' ', true, 400);
		die();
	}
	
	$knowntypes = array("user","location");
	if(!in_array($type,$knowntypes,false)) {
		// unrecognized type requested can't do much from here.
		echo 'Unknown type: ' . $type;
		header(' ', true, 400);
		die();
	}
	
	$classname = ucfirst($type); 	// class names start with uppercase
	$coretypes = array("user");
	$path = "";
	if(in_array($type,$coretypes,false)) {
		// coretypes in core folder
		$path = 'core/';
	}
	//echo dirname(__FILE__) . '/classes/' . strtolower($path) . strtolower($type) . '.php<br/>';
		
	// include appropriate dataEntity class & then instantiate it
	include_once dirname(__FILE__) . '/classes/' .strtolower($type) . '.php';
	if ($classname=='User') {
		// hack around fact that user object requires ID to instantiate
		$class = new $classname($id);
	}
	else {
		$class = new $classname;
	}
	
	$returnurl='';
	if (isset($_GET["return"])) {
		// allows calling pages to specify page to return to.
		$returnurl=$_GET["return"];
	}
	else {
		if (array_key_exists('$HTTP_REFERER', $_SERVER)) {
			$returnurl=$_SERVER['HTTP_REFERER'];
		}
	}	
	
	$mode = "view"; // the default mode	
	if (isset($_GET["mode"]))
		{
			$mode = $_GET["mode"];
		}
	

	if (!$id) {
		// assume creating a new entity
		$id=0;
		$entity=null;
		if (isset($_GET["parentid"])) {
			$parentid = $_GET["parentid"];
			}
		}
	 elseif ($id>0) {
			$entity = $class->getEntity($id,$tenantID,$userID);
	 		}
	
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php if ($mode=='edit') { echo "Edit " . $class->getName(); } else { echo $class->getName(); } ?></title>
		<?php include("partials/includes.php"); ?>
		<script type="text/javascript" src="js/validator.js"></script>
        <script type="text/javascript" src="js/jquery.form.min.js"></script>
		<script src="js/entityPage.js"></script>
		<?php echo $class->getJavaScript(); ?>
		
    </head>
    <body>
    	<div id="maincontent">
    		<div id="outer">
	    		<?php include dirname(__FILE__) . '/header.php';?>
    			<div id="main">
    				<?php if ($id>0 && count($entity)==0) {?>
    					<h1>Not found.</h1>
    					<p>The <?php echo $type ?> requested was not found.</p>
    				<?php } elseif ($mode!='edit') { ?>
    				<input type="hidden" id="id" name="id" value="<?php echo $id; ?>"/>
    				<input type="hidden" name="tenantid" value="<?php echo $tenantID; ?>"/>
				    <input type="hidden" id="type" name="type" value="<?php echo $type; ?>"/>
				    <div class="container">
				    	<?php echo $class->renderView($entity,$userID,$returnurl)?><button class="btn btn-default" type="button" onclick="history.back();" ><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Back</button>
						<?php if($user && $user->canEdit($type, $tenantID)) {?>
							<button class="btn btn-default" type="button" onclick="setMode('edit');" ><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit</button>
						<?php } ?>
					</div>
    				<?php }	
    					else { ?>
	    				<div class="container">
	    					<h2><?php if($id>0) {echo 'Edit ';} else {echo'Add ';} echo ucfirst($type); ?> </h2>
		        			<form id="entityForm" class="form-horizontal" action="<?php echo $class->getDataServiceURL(); ?>" method="post" role="form">
				        		<div class="edit">
				        			<input type="hidden" id="id" name="id" value="<?php echo $id; ?>"/>
				        			<input type="hidden" name="tenantid" value="<?php echo $tenantID; ?>"/>
				        			<input type="hidden" id="type" name="type" value="<?php echo $type; ?>"/>
									<input id="txtCurrentLatitude" type="hidden" value="<?php echo Utility::getSessionVariable('latitude', ''); ?>"/>
									<input id="txtCurrentLongitude" type="hidden" value="<?php echo Utility::getSessionVariable('longitude', '');; ?>"/>
				        			<div id="mapcanvas" class="hidden">Placeholder map canvas.</div>
				        			<?php
										 Utility::renderForm($class, $entity, $id, $tenantID, $parentid);
				        			?>
				        			
			        			</div>
				        	</form>
				        	<div class="edit">
					        	<div id="messageDiv" class="message hidden">
				        			<span id="messageSpan"><p>Your message here!</p></span>
				        		</div>
				        		<div class="form-group">
    								<div class="col-sm-offset-2 col-sm-10">
				        				<button class="btn btn-primary" name="save" type="input" onClick="saveEntity();	">Save</button>
				        			</div>
				        		</div>
			        		</div>
				       </div>
    				
							
					<?php	}
					
    				?>
    				<div id="childEditModal" class="modal fade" role="dialog">
						<div class="modal-dialog">
						    <!-- Modal content-->
						    <div class="modal-content">
						      <div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal">&times;</button>
						        <h4 id="childEditHeader" class="modal-title">Modal Header</h4>
						        <input id="childType" type="hidden" value=""/>
						      </div>
						      <div id="childEditBody" class="modal-body">
						      	<div id="childEditLoading" class="ajaxLoading">
						      	</div>
						      	<div id="childEditContainer" class="container-fluid">
						        	<p></p>
						        </div>
						      </div>
						      <div class="modal-footer">
								<div id="childMessageDiv" class="message hidden">
							    	<span id="childMessageSpan"><p>Your message here!</p></span>
								</div>
								<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cancel</button>
						      	<button id="childEditSaveButton" type="button" class="btn btn-primary" onclick="saveChild();" disabled>
						      		<span class="glyphicon glyphicon-save" aria-hidden="true"></span> Save
						      	</button>
						      </div>
						   </div>
						</div>
					</div>	
				</div>	
        		<?php include dirname(__FILE__) . '/footer.php';?>    		
        	</div>
        </div>
    </body>
</html>
    