<?php
	include dirname(__FILE__) . '/partials/pageCheck.php';
	include_once dirname(__FILE__) . '/classes/core/utility.php';
	$thisPage="index";
    $finditem = Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'finditem');
 ?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') ?></title>
		<?php include("partials/includes.php"); ?>
    </head>
    <body>
    	<div id="maincontent">
			<?php include("header.php"); ?>
			<div class="jumbotron">
      				<div class="container">
        			<h1><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') ?></h1>
			        <p><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'welcome') ?><p><a class="btn btn-primary btn-lg" href="about.php" role="button">Learn more &raquo;</a></p>
      			</div>

	  			<div class="container">
			      <div class="row">
			        <div class="col-md-4 homePanel" >
			           <?php
                            $mapheading = Utility::getTenantProperty($applicationID, $tenantID, $userID, 'BigMapHeading');
                            $maplink = Utility::getTenantProperty($applicationID, $tenantID, $userID, 'BigMapLink');
                            $maptext = Utility::getTenantProperty($applicationID, $tenantID, $userID, 'BigMapText');
                            if (!$maplink) {
                                $maplink = 'finder.php';
                            }
                            if (!$maptext) {
                                $maptext = 'View our collection of the top ' . $finditem . ' around. Our Big ' . ucwords($finditem) . ' Map has your choices.';
                            }
                            if (!$mapheading) {
                                $mapheading = 'The Big ' . ucwords($finditem) . ' Map';
                            }
                      ?>
                      <h2><?php echo $mapheading ?></h2>
                      <p><?php echo $maptext ?></p>
                      <p><a class="btn btn-default" href=<?php echo $maplink ?> role="button">Go &raquo;</a></p>
                   </div>
			        <div class="col-md-4 homePanel" >
			          <h2><?php echo ucwords($finditem) ?> Near Me</h2>
			          <p>Looking to find <?php echo strtolower($finditem) ?> near you? Our <?php echo ucwords(strtolower($finditem)) ?> Finder will map it out for you. </p>
			          <p><a class="btn btn-default" href="finder.php" role="button">Go &raquo;</a></p>
			       </div>
			        <div class="col-md-4 homePanel">
			          <h2>Plan a Trip</h2>
			          <p>Heading out on a trip and looking to find great <?php echo $finditem ?> along the way? Use our trip planner to map out your route.</p>
			          <p><a class="btn btn-default" href="trip.php" role="button">Go &raquo;</a></p>
			        </div>
			      </div>
			   </div>
    		</div>        		
        	<?php include("footer.php");?>     		
        	</div>
        </div>
        
    </body>
</html>