<?php 
include_once("analyticstracking.php");
include_once("classes/location.php");
$allowIssueLog = false;
if (Utility::getTenantProperty($applicationID, $tenantID, $userID, 'showAds')=='yes') {?>
<div class="adframe">    
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <!-- FoodFinder_Top -->
    <ins class="adsbygoogle"
         style="display:block"
         data-ad-client="ca-pub-0081868233628623"
         data-ad-slot="1225121234"
         data-ad-format="auto"></ins>
    <script>
    (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
</div>
<?php } ?>
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar1" aria-expanded="false">
	        	<span class="sr-only">Toggle navigation</span>
	        	<span class="icon-bar"></span>
	        	<span class="icon-bar"></span>
	        	<span class="icon-bar"></span>
      		</button>
			<a class="navbar-brand" href="index.php"><?php
			  $icon = Utility::getTenantProperty($applicationID, $tenantID, $userID,'icon');
              $title = ucfirst(Utility::getTenantProperty($applicationID, $tenantID, $userID,'title'));
              if (strlen($icon)>0) {
                  echo '<img src="' . $icon . '" alt=""' . $title . '" />';
              }
              else {
                  echo $title;
              }
		    ?></a>
		</div>
		<?php
                    $mapheading = Utility::getTenantProperty($applicationID, $tenantID, $userID, 'BigMapHeading');
                    $maplink = Utility::getTenantProperty($applicationID, $tenantID, $userID, 'BigMapLink');
                    $finditem = Utility::getTenantProperty($applicationID, $tenantID, $userID, 'finditem');
                    if (!$maplink) {
                        $maplink = 'finder.php';
                    }
                    if (!$mapheading) {
                        $mapheading = 'The Big ' . ucwords($finditem) . ' Map';
                    }
                      ?>
		<div class="collapse navbar-collapse" id="navbar1">
			<ul class="nav navbar-nav">
				<li <?php if($thisPage=='bigmap') echo ' class="active"'?>><a href="<?php echo $maplink ?>"><?php echo $mapheading ?></a></li>
				<li <?php if($thisPage=='finder') echo ' class="active"'?>><a href="finder.php">Near Me</a></li>
				<li <?php if($thisPage=='trip') echo ' class="active"'?>><a href="trip.php">Plan a Trip</a></li>
				<li <?php if($thisPage=='search') echo ' class="active"'?>><a href="search.php">Search</a></li>
				<li <?php if($thisPage=='about') echo ' class="active"'?>><a href="about.php">About</a></li>
				<?php if($userID>0 && ($user->hasRole('admin',$tenantID) || $userID==1)) {?><li <?php if($thisPage=='admin') echo ' class="active"'?>><a href="admin.php">Admin</a></li><?php } ?>			
			</ul>
			<?php if($userID>0) {?>
			<!--<span class="nav_text"><?php echo $user->name ?></span>-->
			 <ul class="nav navbar-nav navbar-right">
			    <li class="dropdown">
			        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $user->name; ?> <span class="caret"></span></a>
			         <ul class="dropdown-menu">
			             <li><a href="profile.php">Profile</a></li>
			             <li><a href="logout.php">Logout</a></li>
			         </ul> 
				</li>
				<?php
				    $location = new Location($userID,$tenantID);
				    if ($location->userCanAdd($user)) {
				        $allowIssueLog=true;
				        ?> 
                        <li><button type="button" class="btn btn-default navbar-btn" onclick="logIssue();"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>&nbsp;Log Issue</button>&nbsp;</li>
    				    <li><button type="button" class="btn btn-default navbar-btn" onclick="window.location.href='entityPage.php?type=location&id=0&mode=edit';"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>&nbsp;Add New</button></li>
			    <?php } ?>
			 </ul>		
	         <?php } else { ";" ?>
			<ul class="nav navbar-nav navbar-right">
			     <li><button class="btn btn-default navbar-btn" data-toggle="collapse" data-target="#loginForm">
			         <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
			     </button></li>
    			<form id="loginForm" class="navbar-form navbar-right collapse" role="search" action="login.php" method="post">
                    <div class="form-group">
                        <input type="text" id="txtUsername" class="form-control" name="username" placeholder="Username">
                    </div>
                    <div class="form-group">
                        <input type="password" id="txtPassword" class="form-control" name="password" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-default">Sign In</button>
    	         </form>
	         </ul>	         
	         <?php } 
	         if ($allowIssueLog) {
	            include("partials/issueForm.php");    
	         }
	         
	         ?>
	     </div>
	</div>
</nav>
