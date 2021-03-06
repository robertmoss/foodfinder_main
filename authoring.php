<?php 
	include dirname(__FILE__) . '/core/partials/pageCheck.php';
	include_once dirname(__FILE__) . '/partials/permissionCheck.php';
	$thisPage="authoring";
    	
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') ?> - Authoring</title>
        <?php include("partials/includes.php"); ?>
        <link rel="stylesheet" type="text/css" href="static/css/authoring.css" />
        <script type="text/javascript" src="<?php echo Config::$site_root?>/js/validator.js"></script>
        <script type="text/javascript" src="<?php echo Config::$site_root?>/js/bootpag.min.js"></script>
        <script type="text/javascript" src="<?php echo Config::$site_root?>/js/jquery.form.min.js"></script>
        <script type="text/javascript" src="<?php echo Config::$site_root?>/js/jquery-ui.js"></script>
        <script type="text/javascript" src="<?php echo Config::$site_root?>/js/authoring.js"></script>
    </head>
    <body>
    	<div id="maincontent">
    		<div id="outer">
	    		<?php include('partials/header.php');?>
    			<div class="container">
                    <div>
                        <ul class="nav nav-pills" role="tablist">
                            <li role="presentation" class="active"><a href="#assignments" aria-controls="assignments" role="tab" data-toggle="tab" onclick="loadAssignmentList()">Schedule</a></li>
                            <li role="presentation"><a href="#features" aria-controls="features" role="tab" data-toggle="tab" onclick="loadFeatureList()">Features</a></li>
                            <li role="presentation"><a href="#locations" aria-controls="locations" role="tab" data-toggle="tab" onclick="loadLocations()">Locations</a></li>
                            <li role="presentation"><a href="#lists" aria-controls="lists" role="tab" data-toggle="tab" onclick="loadEntityListList()">Lists</a></li>
                            <li role="presentation"><a href="#products" aria-controls="products" role="tab" data-toggle="tab" onclick="loadProductList()">Products</a></li>
                            <li role="presentation"><a href="#productcollections" aria-controls="productcollections" role="tab" data-toggle="tab" onclick="loadProductCollectionList()">Product Collections</a></li>
                        </ul>                   
                    </div>
                    <div class="tab-content">
                        <div id="assignments" role="tabpanel" class="tab-pane active">
                            <?php
                                $entityType="assignment";
                                $setName = "assignments";
                                $friendlyName = "Schedule Assignment";
                                $columns="Status,AssignedToName,TargetDate";
                                $columnLabels="Status,Assigned To,Target Date";
                                $afterLoad="afterAssignmentListLoad"; 
                                include('core/partials/entityList.php');
                            ?>
                        </div>  
                        <div id="features" role="tabpanel" class="tab-pane">
                            <?php
                                $entityType="feature";
                                $setName = "features";
                                $modalSize = "large";
                                $filters = "extended=true";
                                $columns="Status,Post Date,Views"; 
                                include('core/partials/entityList.php');
                                $modalSize="";
                                $columns="";
                                $filters = "";
                            ?>
                        </div>    
                        <div id="locations" role="tabpanel" class="tab-pane">
                            <h1>Locations</h1>
                            <p>Not yet implemented.</p>
                        </div>
                        <div id="lists" role="tabpanel" class="tab-pane">
                            <?php
                                $entityType="entityList";
                                $friendlyName="List";
                                $setName = "entityLists";
                                $customEditHandler = true;  
                                include('core/partials/entityList.php');
                                $customEditHandler = false; 
                            ?>
                        </div>
                        <div id="products" role="tabpanel" class="tab-pane">
                            <?php
                                $entityType="product";
                                $friendlyName="Product";
                                $setName = "products";
                                include('core/partials/entityList.php');
                            ?>
                        </div>
                        <div id="productcollections" role="tabpanel" class="tab-pane">
                            <?php
                                $entityType="productCollection";
                                $friendlyName="Product Collection";
                                $setName = "productCollections";
                                include('core/partials/entityList.php');
                            ?>
                        </div>
                    </div>
                </div>
        		<?php include("partials/footer.php")?>     		
        	</div>
        </div>
    </body>
</html>
    