<?php 
    include_once dirname(__FILE__) . '/../classes/config.php';
    include_once Config::$core_path . '/classes/log.php';
    include_once Config::$core_path . '/classes/context.php';
    include_once Config::$root_path . '/classes/application.php';
    
    Log::debug('Rendering footer . . .', 1);
?>
<footer>
    <?php if (Utility::getTenantProperty($applicationID, $tenantID, $userID, 'showAds')=='yes') {?>
    <div class="adframe">    
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- FoodFinder_Top -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-0081868233628623"
             data-ad-slot="1225121234"
             data-ad-format="auto"></ins>
        <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
<?php } ?>
    <div id="footer">
        <div class="footer-nav">
            <ul class="nav nav-pills">
                <li role="presentation"><a href="<?php echo Config::getSiteRoot();?>/about.php">About <?php echo Utility::getTenantProperty($applicationID, $_SESSION['tenantID'],$userID,'title') ?></a></li>
                <li role="presentation"><a href="mailto:<?php echo Utility::getTenantPropertyEx($applicationID, $_SESSION['tenantID'],$userID,'contactEmail','mossr19@gmail.com') ?>">Contact Us</a></li>
            </ul>
        </div>
        <p>© 2016 Palmetto New Media. All Rights Reserved.</p>
    </div>
    <?php if (Config::$debugMode || $userID == 1) { ?>
    <div id="debug">
    	<?php 
    		echo 'tenantID=' . $tenantID . '<br/>';
    		echo 'userID=' . $userID . '<br/>';
    		echo 'Debug Level=' . Config::$debugLevel . '<br/>';
    		$inipath = php_ini_loaded_file();
    		echo 'php.ini path=' . $inipath . '<br/>';
            echo 'version=' . Application::$version . '<br/>';
            echo 'deviceType=' . Context::deviceType() . '<br/>';
            echo 'OS Class=' . Context::deviceOSClass() . '<br/>';
            echo 'OS Type=' . Context::deviceOSType() . '<br/>';
            echo 'Server Name=' . $_SERVER['SERVER_NAME'] . '<br/>';
    	?>
    </div> 
    <?php } 
        Log::debug('Footer complete for ' . $_SERVER["SCRIPT_FILENAME"] . ' - sessionid=' . session_id(), 1);
    ?>
</footer>
