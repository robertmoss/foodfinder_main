<?php
		include dirname(__FILE__) . '/core/partials/pageCheck.php';
		include_once dirname(__FILE__) . '/core/classes/utility.php';
        include_once dirname(__FILE__) . '/classes/application.php';
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
        <title><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') ?>: About</title>
        <?php include("partials/includes.php"); ?>
        <script src="js/content.js"></script>
    </head>
    <body>
    	<div>
 			<?php include('partials/header.php');?>
            <?php include('core/partials/contentControls.php');?>
 			<div id="main" class="container">
	    		<div class="jumbotron">
	    			<h2>About <?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') ?></h2>
	    			<p><?php echo Utility::renderContent('about:aboutText', $_SESSION['tenantID'],$user); ?></p>
	    			<p><a class="btn btn-primary" href="mailto:<?php echo Utility::getTenantPropertyEx($applicationID, $_SESSION['tenantID'],$userID,'contactEmail','mossr19@gmail.com') ?>">Contact Us</a></p>
	    		</div>
	    		<p>Version <?php echo Application::$version ?></p>
	    	</div>	
        	<?php include("partials/footer.php")?>
        </div>
    </body>
</html>
    