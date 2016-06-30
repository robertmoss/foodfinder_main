<?php 
	include dirname(__FILE__) . '/core/partials/pageCheck.php';
	$thisPage="contribute";	
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Food Finder</title>
        <?php include "partials/includes.php"; ?>
    </head>
    <body>
    	<div id="maincontent">
    		<div id="outer">
	    		<?php include('partials/header.php');?>
    			<div id="main" class="container">
    			    <div class="panel panel-default centered-column">
    				    <div class="panel-heading">
    				        <h2>Welcome, Contributor!</h2>
    				    </div>
    				    <div class="panel-body">
    				        <p>Thank you for being part of this web project. It’s called FoodFinder and its purpose is simple: 
    				            to help serious eaters find good food. The idea grew out of my travels over recent years and my ongoing
    				             frustration with the fact that&mdash;despite Google and Yelp and Eater and all that&mdash;it’s still really hard to find
    				             those must-visit places when visiting a new city.</p>
    				         <h3>The Challenge</h3>
    				         <p>There's no shortage of information out there when it comes to finding restaurants. In fact, the problem is just the opposite:
    				              there's <strong>way too much</strong> content to sift through. Food media offers endless top 10 listicles, breathless roundups of most lifechanging tacos and 
    				             things you absolutely must put in your mouth before you die. The great majority were written from afar about the same
    				             short list of trendy places every one else is writing about, and few can be trusted to guide you a place that's really
    				             unique and top quality. There are certainly <u>some</u> writers and semi-pro eaters whose tastes and expderience you can trust,
    				             but their knowledge tends to be sprinkled among any number of prose text articles that are hard to pull together when you,
    				             say, find out you have to make a last minute trip to Pittsburgh (poor soul!).</p>
    				             <p> Google and Tripadvisors and Yelp aren't much help, either. Yes, they can show you just about place
                                 to eat that's near you and give you lots of stars and quotes and comments from zillions of diners, but that's exactly the problem: 
    				             it's way too much to filter through, and crowd-sourced opinions reflect the tastes of the crowds themselves. In the aggregrate, their views revert to the
    				             mean, and the Olive Garden ends up the top ranked local Italian and Five Guys the best burgers.</p>
                             <h3>So What's the Big Idea?</h3>
                             <p>The idea here is different. It's to build a moderated, informed list of great places to eat, and to give you the tools&mdash;particularly
                                 the mapping and geolocation tools&mdash;to make it easy to navigate through the suggestions and find a good place to eat. To 
                                 the extent there is a criteria for the locations selected, it’s that the place is somehow an essential spot that a serious eater needs to try when visiting
                                  the particular area. There is a bit of a bias toward old classics (anything over 50 years old), but there are a lot of brand new places, too.</p>
    				            <p>This site is fully mobile enabled, so it should work equally well whether you are planning a trip on your laptop
    				                at home or out on the road using your smartphone to try to find something good to eat nearby. Though we can create other
    				                 versions in the future for specific things (e.g. I already have a barbecue finder version and a cocktail bar finder 
    				                 in the works), this particular site is not focused on fine dining or any one particular category of food, but rather it’s
    				                  “don’t miss” food.</p>
    				            <p>To use my home city of Charleston, South Carolina, as an example: I would put Bowen’s Island, Martha Lou’s, Husk, 
    				                and Edmund’s Oast on the list, but not Xiao Bao Biscuit or Oak because, as good as those latter places are, I wouldn’t 
    				                say there’s anything particularly Charleston about them. Serious eaters can quibble about all day about such choices, but 
    				                I look at it this way: if I am going to be in Lincoln, Nebraska, for a couple of days, what are the handful of restaurants
    				                 that I want to make sure I go visit? Food-Finder, when it's fully built out, will tell me just that.</p>
    				         <h3>And That's Where You Come In</h3>
    				         <p>As a contributor, you can add new locations into the database (just click the <a href="core/entityPage.php?type=location&id=0&mode=edit">Add New</a> button 
    				             in the menu bar). If you love typing (and who doesn't?), you can key all the information in about a place, but there's a much 
    				             easier way. Just type in the name of the restaurant in the Name field and click the “Check Google Places” button. This will look up the location
    				              in Google Places and, if it finds it, populate all the relevant information for you. 
    				              (Note: it helps find the right place to enter both the name and location info in the Name box. 
    				              For example, “Cannon’s Barbecue Little Mountain” will make sure you get the right one.)</p>
    				              <p>For now, when you add a new location, it goes into a “Pending” status—meaning that you can see and edit it but it won’t be visible 
    				                  to other users until I review and activate it.</p>
    				              <p>As to what kind of places to add: add in the ones that you think need to be included in this collections. The places you
    				                  love to visit, the places you recommend others go to when they are on the road, that odd little wonderful diner you 
    				                  stumbled across that no one else seems to know about.</p>
    				           <p>Since this site is still very much a work in progress, there’s a <a onclick="logIssue();">Log an Issue”</a> button, too. 
    				               If you hit a bug or just have a question or idea for a new feature that would be useful, just click that
    				                and log the details. It will go into the development database and get addressed whenever I get around to it.</p>
    				          <p>Thanks again for being a contributor, and I look forward for us being help each other find the very best places
    				              to eat when we are dining all over America.</p>



    				    </div>
    				</div>
	        	</div>	
        		<?php include("partials/footer.php")?>     		
        	</div>
        </div>
    </body>
</html>
    