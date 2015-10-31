<?php
		include dirname(__FILE__) . '/partials/pageCheck.php';
		include_once dirname(__FILE__) . '/classes/core/utility.php';
		//ini_set('display_errors', 'On');
		//require_once 'System.php';
		//var_dump(class_exists('System', false));
		$thisPage="about";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],'title') ?>: About</title>
        <?php include("partials/includes.php"); ?>
		
    </head>
    <body>
    	<div>
 			<?php include('header.php');?>
 			<div id="main" class="container">
	    		<div class="jumbotron">
	    			<h2>About <?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],'title') ?></h2>
	    			<p><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],'title') ?>: is a prototype site built on 
	    				Palmetto New Media's Foodfinder platform.</p>
	    			<p><a class="btn btn-primary" href="mailto:mossr19@gmail.com">Contact Us</a></p>
	    		</div>
	    	</div>	
        	<?php include("footer.php")?>
        </div>
    </body>
</html>
    