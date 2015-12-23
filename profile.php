<?php 
	include dirname(__FILE__) . '/partials/pageCheck.php';
	$thisPage="profile";
    if ($userID==0) {
        header('Location: 403.php');
        die();
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>User Profile</title>
        <?php include("partials/includes.php"); ?>
    </head>
    <body>
    	<div id="maincontent">
    		<div id="outer">
	    		<?php include('header.php');?>
    			<div id="main" class="container">
    				<h1><?php echo $user->name; ?></h1>
    				<p><?php foreach($user->getTenantRoles($tenantID) as $role) {?>
    				    <span class="badge">
    				        <?php echo ucwords($role); ?>
    				    </span>
    				    <?php } ?>
    				</p>
	        	</div>	
        		<?php include("footer.php")?>     		
        	</div>
        </div>
    </body>
</html>
    