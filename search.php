<?php 
	include dirname(__FILE__) . '/partials/pageCheck.php';
	$thisPage="search";	
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Food Finder</title>
        <?php include("partials/includes.php"); ?>
        <link rel="stylesheet" type="text/css" href="static/css/search.css" />
        <script src="js/search.js"></script>
    </head>
    <body>
		<?php include('header.php');?>
		<div id="main" class="container">
			<div class="panel panel-default">
				<div class="panel-heading">Find a Location</div>
				<div class="panel-body">
					<form id="searchForm" onsubmit="retrieveResults();return false;">
  						<div class="form-group">
  							<div class="col-lg-6">
							    <div class="input-group">
								    <span class="input-group-addon glyphicon glyphicon-search" aria-hidden="true"></span>
								    <input type="text" class="form-control" id="txtSearch" placeholder="Search for ...">
								</div>
							</div>
						</div>
						<div class="form-group">
							<button type="button" class="btn btn-primary" onclick="retrieveResults()">Search</button>
						</div>
					</form>
				</div>
			</div>
			<div id="results" class="hidden">
				<!-- results go here -->
			</div>
    	</div>
	    <?php include("partials/locationModal.php")?>
		<?php include("partials/locationEditModal.php")?>	
        <?php include("footer.php")?>     		
    </body>
</html>
    