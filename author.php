<?php 
	include_once dirname(__FILE__) . '/core/partials/pageCheck.php';
    include_once dirname(__FILE__) . '/core/classes/user.php';
    include_once dirname(__FILE__) . '/classes/feature.php';
	$thisPage="author";	
    
    $id = Utility::getRequestVariable('id', 0);
    $errorMsg = "";
    if ($id==0) {
        $errorMsg ="You must specify a valid author id.";
    }
    else {
        try {
            $class = new User($id,$tenantID);
            $author = $class->getEntity($id);
            Log::logPageView('author', $id,'');
            $feature = new Feature($userID,$tenantID);
            $filters = array("author"=>$author["id"],"status"=>"Published");
            $features = $feature->getEntities($filters, 9, 0);
        }
        catch(Exception $ex) {
            $errorMsg="Unable to load requested author: " . $ex->getMessage();
        }
    }   
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') ?></title>
        <?php include("partials/includes.php"); ?>
        <link rel="stylesheet" type="text/css" href="static/css/product.css" />
         <link rel="stylesheet" type="text/css" href="static/css/author.css" />
        <script type="text/javascript" src="js/product.js"></script>
    </head>
    <body>
    	<div id="maincontent">
    		<div id="outer">
	    		<?php include('partials/header.php');?>
	    		<?php include('partials/productModal.php');?>
    			<div class="container">
                    <?php if (strlen($errorMsg)>0) {
                        echo '<br/><p>' . $errorMsg . '<p>';
                    }
                    else {
                        ?>
                    <h3>Author Profile</h3>
                    <h1><?php echo $author["name"]?></h1>
                    <p class="col-sm-8"><?php echo Utility::renderWebContent($author["bio"])?></p>
                    <?php if (count($features)>0) {
                        echo '<div class="articleList"><h3>Stories by ' . $author["name"] . '</h3>';
                        foreach($features as $feature) {
                            echo '<div class="col-sm-4">';
                            echo '<div class="imageWrapper"><img class="coverImage" src="' . $feature["coverImage"] . '"></div>';
                            echo '<h3><a href="feature.php?id=' . $feature["id"] . '">' . $feature["headline"] . '</a></h3>';
                            echo '<p>' . $feature["subhead"] . "</p>";
                            echo '</div>';
                        }
                        echo '</div>';
                    }?>
                    <?php } ?>
	        	</div>	
        		<?php include("partials/footer.php")?>     		
        	</div>
        </div>
    </body>
</html>
    