<?php 
    include_once dirname(__FILE__) . '/../classes/config.php';
    include_once Config::$core_path . '/classes/log.php';
    include_once Config::$core_path . '/classes/context.php';
    include_once Config::$root_path . '/classes/application.php';
    
    Log::debug('Rendering footer . . .', 1);
?>
<footer>
<div id="footer"><p>© 2016 Palmetto New Media</p></div>
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
