<?php 
	session_start();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Food Finder: Error</title>
        <link rel="stylesheet" type="text/css" href="static/css/styles.css" />	
    
		<script src="js/jquery-1.10.2.js"></script>		
		<script src="js/mustache.js"></script>
		<script src="js/core.js"></script>
		<script src="js/foodfinder.js"></script>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB9Zbt86U4kbMR534s7_gtQbx-0tMdL0QA&sensor=true"></script>
    </head>
    <body>
    	<div id="maincontent">
    		<div id="outer">
	    		<?php include('header.php');?>
    			<div id="main">
    				<h1>Uh oh.</h1>
    				<p>Something appears to have gone wrong.</p>
    				<?php
    				// if error message set on session, display to user.
	    				if (isset($_SESSION['errorMessage'])) {
	    					echo "<p>" . $_SESSION['errorMessage'] . "</p>";
					}
					?>
	        	</div>	
        		<?php include("footer.php")?>     		
        	</div>
        </div>
    </body>
</html>
    