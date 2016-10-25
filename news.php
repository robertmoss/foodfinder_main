<?php 
	include dirname(__FILE__) . '/core/partials/pageCheck.php';
    include_once Config::$core_path . '/classes/utility.php';
    include_once Config::$core_path . '/classes/format.php';
    include_once dirname(__FILE__). '/classes/feature.php';
	$thisPage="news";
    
     $class = new feature($userID,$tenantID);
     $filters = array('news'=>'true');
     $newsItems = $class->getEntities($filters,4,0);  
    	
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') ?>: News</title>
        <?php include("partials/includes.php"); ?>
    </head>
    <body>
    	<div id="maincontent">
    		<div id="outer">
	    		<?php include('partials/header.php');?>
    			<div class="container">
                    <div class="news">
        				<h1>BBQ News</h1>
        				<h2>A roundup of the latest news from the barbecue world.</h2>
    
        				<?php
        				if (count($newsItems)==0) {
        				    echo '<p>Hmm. Guess there\'s not much going on right now. Check back later.</p>';
        				}
        				foreach($newsItems as $newsItem) {
        				    echo '<div class="col-sm-8 newsItem">';    
        				    echo '<div class="newsImage"><img src=" ' . $newsItem["coverImage"] . '"></div>';
                            echo '<div class="newsBlock">';
                            echo '<h3 class="headline"><a href="feature.php?id=' . $newsItem["id"] . '">' . $newsItem["headline"]. '</a></h3>';
                            echo '<p class="author">By <a href="author.php?id=' . $newsItem["authorid"] . '">' . $newsItem["author"] . '</a></p>';
                            echo '<p class="dateline">' . Format::formatDateLine($newsItem["datePosted"],true) . '</p>';
                            echo '<p class="subhead">' . $newsItem["subhead"] . '</p>';
                            echo '</div>';
                            echo '</div>';

                            }
                        ?>
                    </div>
	        	</div>	
        		<?php include("partials/footer.php")?>     		
        	</div>
        </div>
    </body>
</html>
    