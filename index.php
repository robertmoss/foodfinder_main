<?php
	include_once dirname(__FILE__) . '/classes/config.php';
	include_once Config::$core_path . '/partials/pageCheck.php';
	include_once Config::$core_path . '/classes/utility.php';
    include_once Config::$core_path . '/classes/format.php';
    include_once dirname(__FILE__). '/classes/feature.php';
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
        <script src="js/home.js"></script>
        <script src="js/pageEditor.js"></script>
        <script src="js/content.js"></script>  
    </head>
    <body>
    	<div id="maincontent">
			<?php include("partials/header.php"); ?>
            <?php include('core/partials/contentControls.php');?>
			<div class="jumbotron">
      				<div class="container">
      				    <?php
      				      $logoUrl =  Utility::getTenantPropertyEx($applicationID, $_SESSION['tenantID'],$userID,'logo','');
                          if ($logoUrl == '') {
                              // show title instead
                               echo '<h1>' . Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') . '</h1>';
                               }
                          else {
                              echo '<img class="logo" src="' . Config::getSiteRoot()  . $logoUrl . '"/>';
                          }
                          ?>
		  	           <h3><?php echo Utility::renderContent('home:welcomeText', $_SESSION['tenantID'],$user); ?></h3>
          			</div>
      			 <?php
      			   $pageClass="";
                   $sortable="";  
      			   if ($user->hasRole('admin',$tenantID)) { 
      			       $pageClass=" editable";
                       $sortable = " sortable";    			     
      			     ?>
      			 <div id="pageEditModal" class="modal fade" role="dialog">
                      <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 id="pageHeader" class="modal-title">Edit Page</h4>
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
                    ?>
                    <div class="">
                        <input id="pageCollectionId" type="hidden" value="home" />
                        <div id="pageContainer" class="row<?php echo $sortable;?>">
                        <?php
                        $seq=0;    
                        foreach($pageCollection as $item) { 
                            $seq++;
                            $image = $item["imageurl"];
                            if (strlen($image)<1) {
                                $image = 'img/placeholder.jpg';
                            }
                            if (strtolower($item["name"])=="news") {
                            ?>
                           <div id="page<?php echo $seq ?>" class="col-md-4 homePanel <?php echo $pageClass ?>" >
                              <div class="homePanelInner">  
                                <p class="hidden"><?php echo $item["id"]?></p>
                                <div class="news">
                                    <h2>Latest News</h2>
                                    <?php 
                                        // spin up a features collection with latest news
                                        $class = new feature($userID,$tenantID);
                                        $filters = array('news'=>'true','status'=>'Published');
                                        $newsItems = $class->getEntities($filters,4,0);  
                                        foreach($newsItems as $newsItem) {
                                            echo '<h3 class="headline"><a href="feature.php?id=' . $newsItem["id"] . '">' . $newsItem["headline"]. '</a></h3>';
                                            echo '<p class="dateline">' . Format::formatDateLine($newsItem["datePosted"],true) . '</p>';
                                        }
                                    ?>
                                    <p class="more"><a href="news.php">More <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></a></p>
                                </div>
                                <div class="buttonPane"></div>
                              </div>
                           </div>                               
                            <?php    
                            }
                            else {
                            ?>
                           <div id="page<?php echo $seq ?>" class="col-md-4 homePanel <?php echo $pageClass ?>" onclick="window.location='<?php echo $item["url"] ?>';">
                              <div class="homePanelInner" style="background-image:url('<?php echo $image?>');">  
                                <p class="hidden"><?php echo $item["id"]?></p>
                                <div class="homePanelImage"><img src="<?php echo $image?>"/></div>
                                <div class="overlay">
                                    <h2><?php echo $item["name"]?></h2>
                                    <p class="description"><?php echo $item["shortdesc"] ?></p>
                                </div>
                                <div class="buttonPane"></div>
                              </div>
                           </div>
                            <?php
                            }
                           }
                           ?>
                        </div>
                    <?php
                    }
                    $pageClass="";
                    $sortable=""; 
                    if ($user->hasRole('admin',$tenantID)) {
                        $pageClass=" editable";
                        $sortable = " sortable";
                        ?>
                       <p><a class="btn btn-default btn-lg" href="#" role="button" onclick="addPage();">Add Page</a></p>
                       <div id="floatingPageButtons" class="floatingControl hidden">
                           <a id="editPageButton" class="btn btn-default btn-sm" href="#" role="button" ><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                           <a id="deletePageButton" class="btn btn-default btn-sm" href="#" role="button" ><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                       </div>
                    <?php } ?>
			     
                    
			      </div>
			   </div>
    		</div>        		
        	<?php include("partials/footer.php");?>     		
        	</div>
        </div>
        
    </body>
</html>