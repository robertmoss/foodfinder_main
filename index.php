<?php
	include_once dirname(__FILE__) . '/classes/config.php';
	include_once Config::$core_path . '/partials/pageCheck.php';
	include_once Config::$core_path . '/classes/utility.php';
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
        <script src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/home.js"></script>  
    </head>
    <body>
    	<div id="maincontent">
			<?php include("partials/header.php"); ?>
			<div class="jumbotron">
      				<div class="container">
        			<h1><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') ?></h1>
			        <p><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'welcome') ?></p>
                    <p><a class="btn btn-primary btn-lg" href="about.php" role="button">Learn more &raquo;</a>
                    <?php
                        $pageClass="";
                        $sortable=""; 
                        if ($user->hasRole('admin',$tenantID)) {
                            $pageClass=" editable";
                            $sortable = " sortable";
                        ?>
                       <a class="btn btn-default btn-lg" href="#" role="button" onclick="addPage();">Add Page</a>
                       <div id="floatingButtons" class="floatingControl hidden">
                           <a id="editButton" class="btn btn-default btn-sm" href="#" role="button" ><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                           <a id="deleteButton" class="btn btn-default btn-sm" href="#" role="button" ><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                       </div>
                                    <?php } ?></p>
      			</div>
      			 <?php if ($user->hasRole('admin',$tenantID)) { ?>
      			 <div id="pageEditModal" class="modal fade" role="dialog">
                      <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 id="pageHeader" class="modal-title">Edit Tenant</h4>
                              </div>
                              <div id="pageFormAnchor" class="modal-body">Loading form . . .</div>
                              <div class="modal-footer">
                                    <div id="page-message" class="alert alert-danger hidden">
                                        <a class="close_link" href="#" onclick="hideElement('message');"></a>
                                        <span id='page-message_text'>Message goes here.</span>
                                    </div>
                                    <button id="btnPageCancel" type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                   <button id="btnPageSave" type="button" class="btn btn-default" onclick="savePage();">Save</button>
                             </div>
                        </div>
                      </div>
                    </div>
                <?php } ?>
	  			<div class="container">
                <?php if ($user->hasRole('contributor',$tenantID)) { ?>
                    <div class='alert alert-info'><h3>Welcome contributor!</h3> To learn more about the Food Finder platform and how you can
                        contribute, <a href="contribute.php">please visit the Contribute page.</a></div>
                <?php } 
                
                $pageCollection = Utility::getTenantPageCollection($applicationID, $userID, $tenantID, "home"); 
                if (is_array($pageCollection)) {
                    echo '<div id="pageContainer" class="row ' . $sortable . '">';
                    $seq=0;    
                    foreach($pageCollection as $item) { 
                            $seq++;
                            $image = $item["imageurl"];
                            if (strlen($image)<1) {
                                $image = 'img/placeholder.jpg';
                            }
                            ?>
                           <div id="page<?php echo $seq ?>" class="col-md-4 homePanel <?php echo $pageClass ?>" style="background-image:url('<?php echo $image?>');" onclick="window.location='<?php echo $item["url"] ?>';">
                                    <p class="hidden"><?php echo $item["id"]?></p>
                                    <div class="overlay">
                                        <h2><?php echo $item["name"]?></h2>
                                        <p class="description"><?php echo $item["shortdesc"] ?></p>
                                    </div>
                                    <div class="buttonPane"></div>
                                </div>
                        <?php
                        }
                    echo('</div>');
                    }
                
                ?>
                
			     
                    
			      </div>
			   </div>
    		</div>        		
        	<?php include("partials/footer.php");?>     		
        	</div>
        </div>
        
    </body>
</html>