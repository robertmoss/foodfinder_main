<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar1" aria-expanded="false">
	        	<span class="sr-only">Toggle navigation</span>
	        	<span class="icon-bar"></span>
	        	<span class="icon-bar"></span>
	        	<span class="icon-bar"></span>
      		</button>
			<a class="navbar-brand" href="index.php"><?php echo ucfirst(Utility::getTenantProperty($applicationID, $tenantID, $userID,'title')); ?></a>
		</div>
		<div class="collapse navbar-collapse" id="navbar1">
			<ul class="nav navbar-nav">
				<li <?php if($thisPage=='finder') echo ' class="active"'?>><a href="finder.php">Explore</a></li>
				<li <?php if($thisPage=='trip') echo ' class="active"'?>><a href="trip.php">Plan a Trip</a></li>
				<li <?php if($thisPage=='search') echo ' class="active"'?>><a href="search.php">Search</a></li>
				<li <?php if($thisPage=='about') echo ' class="active"'?>><a href="about.php">About</a></li>
				<?php if($userID>0) {?><li <?php if($thisPage=='admin') echo ' class="active"'?>><a href="admin.php">Admin</a></li><?php } ?>			
			</ul>
			<?php if($userID>0) {?>
			<!--<span class="nav_text"><?php echo $user->name ?></span>-->
			 <ul class="nav navbar-nav navbar-right">
				<li><button type="button" class="btn btn-default navbar-btn" onclick="window.location.href='entityPage.php?type=location&id=0&mode=edit';"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>&nbsp;Add New</button></li>
				<li><a href="logout.php">Logout</a></li>
			 </ul>		
	         <?php } else { ";" ?>
			<form class="navbar-form navbar-right" role="search" action="login.php" method="post">
                <div class="form-group">
                    <input type="text" id="txtUsername" class="form-control" name="username" placeholder="Username">
                </div>
                <div class="form-group">
                    <input type="password" id="txtPassword" class="form-control" name="password" placeholder="Password">
                </div>
                <button type="submit" class="btn btn-default">Sign In</button>
	         </form>	         
	         <?php } ?>
	     </div>
	</div>
</nav>
<!--
	
	<div id="top_ad">
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script> -->
<!-- Responsive Ad Top 
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-0081868233628623"
     data-ad-slot="1730879237"
     data-ad-format="auto"></ins>
<!-- slow ad serving can delay map load; maybe move ad script to page .js files to trigger after map load -->
<!--<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>';  
					</div>    					

    -->