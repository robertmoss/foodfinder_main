<?php 
    include_once dirname(__FILE__) . '/core/partials/pageCheck.php';
    include_once dirname(__FILE__) . '/partials/permissionCheck.php';
    include_once dirname(__FILE__) . '/classes/media.php';
    $thisPage="mediaManager";

    $return = 20;
    $offset = Utility::getRequestVariable('offset', 0);
    $filters = ""; // should we get from param?
    
    $class = new Media($userID,$tenantID);
    $count = $class->getEntityCount($filters);
    $media = $class->getEntities($filters, $return, $offset)
     
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') ?></title>
        <?php include("partials/includes.php"); ?>
        <link rel="stylesheet" type="text/css" href="static/css/mediaManager.css" />
        <script type="text/javascript" src="js/jquery.form.min.js"></script>
        <script type="text/javascript" src="js/bootpag.min.js"></script>
        <script src="js/mediaManager.js" type="text/javascript"></script>
        <script src="js/workingPanel.js" type="text/javascript"></script>          
    </head>
    <body>
        <div id="maincontent">
            <div id="outer">
                <?php include('partials/header.php');?>
                <?php include('partials/mediaModal.php');?>
                <?php include('partials/mediaUploadModal.php');?>
                <div class="container mediaManager">
                    <h1>Manage Media</h1>
                    <div class="mediaButtons">
                        <input type="hidden" id="mediaItemCount" value="<?php echo $count ?>">
                        <input type="hidden" id="currentOffset" value="<?php echo $offset ?>">
                        <div id="mediaPageSelectorTop" class="pageSelector"></div>
                        <div class="pad-left">
                            <button type="button" class="btn btn-default" onclick="showUploadModal();"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload Media File</button>
                            <button type="button" class="btn btn-default" onclick="showAddMedia();"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Media Manually</button>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($media as $item) {
                            if (!is_null($item["thumbnailurl"]) && strlen($item["thumbnailurl"])>0) {
                                $thumbnail = $item["thumbnailurl"];
                            }
                            else {
                                $thumbnail = "img/icons/nothumb.png";
                            }
                            ?>
                        <div id="media<?php echo $item["id"];?>" class="col-md-3 mediaItem">
                            <div class="thumb" onclick="showMedia(<?php echo $item['id']?>,'<?php echo $item['url']?>','<?php echo $item['name']?>');">
                                <img src="<?php echo $thumbnail;?>">
                                <span class="description"><?php echo $item["name"];?> (<?php echo $item["id"];?>)</span>
                            </div>
                        </div>        
                        <?php } ?>
                    </div>
                    <div class="mediaButtons">
                            <div id="mediaPageSelectorBottom" class="pageSelector"></div>
                            <div class="pad-left">
                            <button type="button" class="btn btn-default" onclick="showUploadModal();"><span class="glyphicon glyphicon-upload" aria-hidden="true"></span> Upload Media File</button>
                            </div>
                    </div>
                </div>  
                <?php include("partials/footer.php")?>          
            </div>
        </div>
    </body>
</html>
    