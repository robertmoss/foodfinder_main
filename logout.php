<?php 
	include_once dirname(__FILE__) . '/classes/core/utility.php';
	session_start();
    // perform all steps to flush user and clear state: right now userID is only remnant
    session_destroy();
	
	// do we need this on logout? Do we preserve tenant ID?
	//include dirname(__FILE__) . '/partials/pageCheck.php';
	
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
    				<p><a href="index.php">Return to Home Page</a></p>
	        	</div>
        		<?php include("footer.php")?>     		
        	</div>
        </div>
    </body>
</html>
    