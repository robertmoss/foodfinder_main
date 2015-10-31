<?php 
	include dirname(__FILE__) . '/partials/pageCheck.php';	
	include_once dirname(__FILE__) . '/classes/core/utility.php';
	include_once dirname(__FILE__) . '/classes/core/user.php';
	
	$id=0;
	if (isset($_GET["id"])) {
		$id=$_GET["id"];
	}
	
	
	if (isset($_GET["return"])) {
		// allows calling pages to specify page to return to.
		$returnurl=$_GET["return"];
	}
	else {
		$returnurl=$_SERVER['HTTP_REFERER'];
	}	

	// if ID = 0, new user
	$user = new User($id);
	
	
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Food Finder</title>
        <link rel="stylesheet" type="text/css" href="static/css/styles.css" />	
    
		<!--<script src="js/jquery-1.10.2.js"></script>	-->	
		<!--<script src="js/mustache.js"></script> -->
		<script src="js/core.js"></script>
		
    </head>
    <body>
    	<div id="maincontent">
    		<div id="outer">
	    		<?php include('header.php');?>
    			<div id="main">
    				<div class="row">
						<span class="label">ID: </span>
			        	<span class="input"><?php echo $user->id; ?></span>
					</div>
					<div class="row">
						<span class="label">Name: </span>
			        	<span class="input"><input id="txtName" name="Name" type="text" placeholder="Name" value="<?php echo $user->name; ?>"></span>
					</div>
    				<div class="row">
						<span class="label">Email: </span>
			        	<span class="input"><input id="txtEmail" name="Email" type="text" placeholder="Email" value="<?php echo $user->email; ?>"></span>
					</div>
					<div class="row">
						<span class="label">Password: </span>
			        	<span class="input"><input id="txtEmail" name="Email" type="password" placeholder="**********" value="<?php echo $user->email; ?>"></span>
					</div>
	        	</div>	
        		<?php include("footer.php")?>     		
        	</div>
        </div>
    </body>
</html>
    