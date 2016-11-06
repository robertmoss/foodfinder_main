<?php 

    /*
     * The idea of a region page is that it encapsulates a bunch of locations within a particular region, which could be a state, a region of a state, a city, etc.
     * It will have a map of locations (defined by a query/list) as well as a headnote introducing and a series of pages/features hanging off of it 
     */
    include dirname(__FILE__) . '/core/partials/pageCheck.php';
    include dirname(__FILE__) . '/core/classes/propertyBag.php';
    include_once dirname(__FILE__) . '/core/classes/log.php';
    include_once Config::$root_path . '/classes/productCollection.php';

    $thisPage="region";
    $errMessage = "";  
    $region = Utility::getRequestVariable('region', 'none');
    if ($region=='none') {
        $errMessage = "Hmm. Something went wrong. No valid region specified.";
    }
    else { 
        $stateList = Utility::getTenantProperty($applicationID,$tenantID,$userID,'enabledStates');
        if (!is_null($stateList)) {
            $stateArray=explode(",",strtoupper($stateList));
            if (!in_array(strtoupper($region),$stateArray)) {
                $errMessage = "That is not a valid region.";
            }
            else {
                Log::logPageView('region', 0,$region);
            }
        }
      }
    
    
    // retrieve properties for this region
    $propertyBag = new PropertyBag($userID,$tenantID);
    $bagName = 'region' . $region . 'Properties';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') ?></title>
        <?php include("partials/includes.php"); ?>
        <link rel="stylesheet" type="text/css" href="static/css/feature.css" />
        <link rel="stylesheet" type="text/css" href="static/css/regionMap.css" />
        <script src="js/jquery-ui.js"></script>
        <script src="js/content.js" type="text/javascript"></script>
        <script src="js/pageEditor.js" type="text/javascript"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB9Zbt86U4kbMR534s7_gtQbx-0tMdL0QA&libraries=places" type="text/javascript" ></script>
        <script src="js/collection.js" type="text/javascript"></script> 
        <script src="js/regionMap.js" type="text/javascript"></script>
    </head>
    <body>
        <div id="maincontent">
            <div id="outer">
                <?php   include('partials/header.php');
                        include('core/partials/contentControls.php');
                        include("partials/locationModal.php");
                        include("partials/locationEditModal.php");
                        if (strlen($errMessage)>0) {
                            echo '<div class="container"><h2>Oops.</h2><p>' . $errMessage . '</p></div>';
                        }
                        else {
                        ?>
                <div class="container">
                    
                   <h1><?php echo Utility::renderContent('region:title' . $region, $_SESSION['tenantID'],$user); ?></h1>
                   <div class="col-md-8"><p><?php echo Utility::renderContent('region:welcomeText' . $region, $_SESSION['tenantID'],$user); ?></p></div>
                <?php
                    $pageClass="";
                    $sortable=""; 
                    if ($user->hasRole('admin',$tenantID)) {
                        $pageClass=" editable";
                        $sortable = " sortable";
                        ?>
                <div class="col-md-4">
                    <a class="btn btn-default btn-lg" href="#" role="button" onclick="addPage();"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Page</a>
                    <a class="btn btn-default btn-lg" href="#" role="button" onclick="mapSettings();"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Region Settings</a>
                </div>
                <div id="floatingPageButtons" class="floatingControl hidden">
                    <a id="editPageButton" class="btn btn-default btn-sm" href="#" role="button" ><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                    <a id="deletePageButton" class="btn btn-default btn-sm" href="#" role="button" ><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                </div>
                <?php } ?>
                </div>
                <?php if ($user->hasRole('admin',$tenantID)) { ?>
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
                    <div id="mapSettingsModal" class="modal fade" role="dialog">
                      <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 id="pageHeader" class="modal-title">Region Settings</h4>
                              </div>
                              <div class="modal-body">
                                  <form id="mapSettingsForm" class="form-horizontal">
                                      <div class="form-group">
                                          <label class="col-sm-3 control-label" for="txtMapSettingCenter">Map Center:</label>
                                          <div class="col-sm-6">
                                              <input id="txtMapSettingCenter" type="text" class="form-control" placeholder="center of map" />
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          <label class="col-sm-3 control-label" for="txtMapSettingZoom">Map Zoom:</label>
                                          <div class="col-sm-2">
                                              <input id="txtMapSettingZoom" type="text" class="form-control" placeholder="zoom level (0-20)" />
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          <label class="col-sm-3 control-label" for="txtMapSettingZoom">Map Filter String:</label>
                                          <div class="col-sm-4">
                                              <input id="txtMapFilterString" type="text" class="form-control" placeholder="filter query string for service" />
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          <div class="col-sm-3">
                                              <button id="btnUseCurrentMapSettings" type="button" class="btn btn-primary" onclick="useCurrent();">Use Current Map Settings</button>
                                          </div>
                                      </div>
                                        <div class="form-group">
                                          <label class="col-sm-3 control-label" for="txtProductCollectionId">Product Collection ID:</label>
                                          <div class="col-sm-2">
                                              <input id="txtProductCollectionId" type="text" class="form-control" placeholder="product collection id" />
                                          </div>
                                      </div>

                                </form>
                              </div>
                              <div class="modal-footer">
                                    <div id="mapSettings-message" class="alert alert-danger hidden">
                                        <a class="close_link" href="#" onclick="hideElement('message');"></a>
                                        <span id='mapSettings-message_text'>Message goes here.</span>
                                    </div>
                                    <button id="btnSettingsModalCancel" type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                   <button id="btnSettingsModalSave" type="button" class="btn btn-default" onclick="saveMapSettings();">Save</button>
                             </div>
                        </div>
                      </div>
                    </div>
                <?php } ?>
                <div class="container">
                <?php 
                 // render page collection
                 
                 $pageCollection = Utility::getTenantPageCollection($applicationID, $userID, $tenantID, "region" . $region); 
                if (is_array($pageCollection)) {
                    echo '<input id="pageCollectionId" type="hidden" value="region' . $region .'" />';
                    echo '<div id="pageContainer" class="row ' . $sortable . '">';
                    $seq=0;    
                    foreach($pageCollection as $item) { 
                            $seq++;
                            $image = $item["imageurl"];
                            if (strlen($image)<1) {
                                $image = 'img/placeholder.jpg';
                            }
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
                    echo('</div>');
                    }
                ?>
                    <p><?php echo Utility::renderContent('region:mapText' . $region, $_SESSION['tenantID'],$user); ?></p>
                    <input id="mapSettingCenter" type="hidden" value="<?php echo $propertyBag->getProperty($bagName, 'mapSettingCenter', 0); ?>" />
                    <input id="mapSettingZoom" type="hidden" value="<?php echo $propertyBag->getProperty($bagName, 'mapSettingZoom', 0); ?>" />
                    <input id="mapFilterString" type="hidden" value="<?php echo $propertyBag->getProperty($bagName, 'mapFilterString', ''); ?>" /> 
                    <input id="productCollectionId" type="hidden" value="<?php echo $propertyBag->getProperty($bagName, 'productCollectionId', '');?>" />
                    <input id="mapSettingPropertyBagName" type="hidden" value="<?php echo $bagName; ?>" />
                    <input id="coreServiceUrl" type="hidden" value="<?php echo Config::getCoreServiceRoot();?>" />
                    <div id="mapwrapper" class="mapWrapper">
                        <div id="mapcanvas"></div>
                        <div id="loading" class="modal"><!-- Place inside div to cover --></div>
                    </div>
                </div>
                <?php } 
                    $productListId = $propertyBag->getProperty($bagName, 'productListId', 0);
                    if ($productListId>0) {
                        $class = new ProductCollection($userID,$tenantID);
                        $collection = $class->getEntity($productListId);
                        ?>
                        <div class="container featureContainer condensed">
                            <h3><?php echo Utility::renderContent('region:productListTitle' . $region, $_SESSION['tenantID'],$user); ?></h3>
                            <p><?php echo Utility::renderContent('region:productListDescription' . $region, $_SESSION['tenantID'],$user); ?></p>
                            <input id="queryParams" type="hidden" value="<?php echo $collection["queryParams"]; ?>" />
                            <div id="collectionAnchor" class="collectionAnchor"></div>    
                        </div>
                        <?php
                    }
                ?>
                 <?php include("partials/footer.php")?>          
            </div>
        </div>
    </body>
</html>
    