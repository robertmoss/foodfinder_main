<?php 

	include dirname(__FILE__) . '/partials/pageCheck.php';
	include_once dirname(__FILE__) . '/classes/database.php';
	include_once dirname(__FILE__) . '/classes/utility.php';
	include_once dirname(__FILE__) . '/classes/dataentity.php';
    include_once dirname(__FILE__) . '/classes/forms.php';
    include_once Config::$root_path . '/classes/application.php';
    
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
		header(' ', true, 400);
		echo 'Type is required';
		die();
	}
	
	$coretypes = array('tenant','tenantSetting','tenantProperty','category','menuItem','page');
    
	if(!in_array($type,$coretypes,false) && !in_array($type, Application::$knowntypes,false)) {
		// unrecognized type requested can't do much from here.
		echo 'Unknown type: ' . $type;
		header(' ', true, 400);
		die();
	}
	
	$classname = ucfirst($type); 	// class names start with uppercase
	$coretypes = array("user");
	$path = "";
    $errorLoading = "";
		
	// include appropriate dataEntity class & then instantiate it
	if (in_array($type,$coretypes,false)) {
	    $classpath = '/classes/';
	}
    else {
    	$classpath = Config::$root_path . '/classes/';
    }
	include_once $classpath . strtolower($type) . '.php';
	if ($classname=='User') {
		// hack around fact that user object requires ID to instantiate
		$class = new $classname($id);
	}
	else {
		$class = new $classname($userID,$tenantID);
	}
        
	$returnurl='';
	if (isset($_GET["return"])) {
		// allows calling pages to specify page to return to.
		$returnurl=urldecode($_GET["return"]);
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
        if (!$class->userCanAdd($user)) {
            Log::debug('User without create permissions attempted to add new user. (userid=' . $userID . ', entity=' . $type . ', tenant=' . tenantID , 9);
            header("Location: 403.php");
            die();
        }
		$entity=null;
		if (isset($_GET["parentid"])) {
			$parentid = $_GET["parentid"];
			}
		}
	 elseif ($id>0) {
	     if ($mode=="edit") {
    	    if (!$class->userCanEdit($id,$user)) {
                Log::debug('User without edit permissions attempted to edit entity. (id=' . $id .', userid=' . $userID . ', entity=' . $type . ', tenant=' . tenantID , 9); 
             header("Location: 403.php");
                die();
            }
         }
         elseif (!$class->userCanRead($id,$user)) {
            Log::debug('User without read permissions attempted to view entity. (id=' . $id .', userid=' . $userID . ', entity=' . $type . ', tenant=' . tenantID , 9); 
            header("Location: 403.php");
            die();
            }
	     
	     try {
			$entity = $class->getEntity($id,$tenantID,$userID);
            $entity["editable"] = $class->userCanEdit($id,$user);
         }
         catch(Exception $ex) {
            $errorLoading = 'Unable to load ' . $type . ': ' . $ex->getMessage();
            $entity=null;             
         }
	 	}
	
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php if ($mode=='edit') { echo "Edit " . $class->getName(); } else { echo $class->getName(); } ?></title>
		<?php include Config::$root_path . '/partials/includes.php'; ?>
		<script type="text/javascript" src="<?php echo Config::$site_root?>/js/validator.js"></script>
        <script type="text/javascript" src="<?php echo Config::$site_root?>js/jquery.form.min.js"></script>
		<script src="<?php echo Config::$site_root?>/js/modalDialog.js"></script>
		<script src="<?php echo Config::$site_root?>/js/entityPage.js"></script>
		<?php echo $class->getJavaScript(); ?>
		
    </head>
    <body>
    	<div id="maincontent">
    		<div id="outer">
	    		<?php include Config::$root_path .  '/partials/header.php';?>
    			<div id="main" class="container">
                    <input type="hidden" id="mode" name="mode" value="<?php echo $mode; ?>"/>
    				<?php if ($id>0 && count($entity)==0) {?>
    					<h1>Not found.</h1>
    					<p>The <?php echo $type ?> requested was not found.</p>
    				<?php } elseif ($mode!='edit') { ?>
    				<input type="hidden" id="id" name="id" value="<?php echo $id; ?>"/>
    				<input type="hidden" name="tenantid" value="<?php echo $tenantID; ?>"/>
				    <input type="hidden" id="type" name="type" value="<?php echo $type; ?>"/>
                    <input type="hidden" id="<?php echo$type ?>id" name="<?php echo$type ?>id" value="<?php echo $id; ?>"/>
				    <div class="container">
				    	<?php echo $class->renderView($entity,$userID,$returnurl);
				    	       if($returnurl && strlen($returnurl)>0) { ?>
    				    	<a class="btn btn-default" type="button" href="<?php echo $returnurl ?>" ><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Back</a>
    				    	
						<?php }
						  if($user && $class->userCanEdit($id,$user)) {?>
							<button id="editEntity" class="btn btn-default" type="button" onclick="setMode('edit');" ><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit</button>
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
				        			<input type="hidden" id="returnUrl" name="returnUrl" value="<?php echo $returnurl; ?>"/>
                                    <input type="hidden" id="<?php echo$type ?>id" name="<?php echo$type ?>id" value="<?php echo $id; ?>"/>
									<input id="txtCurrentLatitude" type="hidden" value="<?php echo Utility::getSessionVariable('latitude', ''); ?>"/>
									<input id="txtCurrentLongitude" type="hidden" value="<?php echo Utility::getSessionVariable('longitude', '');; ?>"/>
				        			<div id="mapcanvas" class="hidden">Placeholder map canvas.</div>
				        			<?php
										 Forms::renderForm($class, $entity, $id, $tenantID, $parentid);
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
                    <?php include("partials/childEditModal.php")?>
                    <?php include("partials/modalDialog.php")?>
				</div>	
        		<?php include Config::$root_path . '/partials/footer.php';?>    		
        	</div>
        </div>
    </body>
</html>
    