<?php 
    include_once dirname(__FILE__) . '/classes/config.php';
	include_once Config::$core_path . '/classes/utility.php';
	session_start();
    // perform all steps to flush user and clear state: right now userID is only remnant
    // do need to keep tenant, though, for branding
    $tenantID = $_SESSION['tenantID'];
    Log::endSession(session_id());
    session_destroy();
	
	// create new session to save tenantID
	session_start();
    session_regenerate_id(true);
    $flushed=true;
    if (Utility::getRequestVariable('flush', 'no') != 'yes') {
        $_SESSION['tenantID'] = $tenantID;
        $flushed=false;
    }
    
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Food Finder: Logout</title>
		<?php include("partials/includes.php"); ?>		
    </head>
    <body>
    	<div id="maincontent">
    		<div id="outer">
	    		<?php 
	    			include('header.php');

	    		?>
	    		<div id="basic">
    				<h2>You have been logged out.</h2>
    				<?php if ($flushed) { echo '<p>Tenant has been flushed.</p>'; }  ?>
    				<p><a href="index.php">Return to Home Page</a></p>
	        	</div>
        		<?php include("footer.php")?>     		
        	</div>
        </div>
    </body>
</html>
    