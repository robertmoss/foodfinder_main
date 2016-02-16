<?php 
    include_once('classes/core/config.php'); 
    include_once('classes/core/log.php');
    
    Log::debug('Rendering footer . . .', 1);
?>
<footer>
<div id="footer"><p>© 2016 Palmetto New Media</p></div>
<?php if (Config::$debugLevel<1) { ?>
<div id="debug">
	<?php 
		echo 'tenantID=' . $tenantID . '<br/>';
		echo 'userID=' . $userID . '<br/>';
		echo 'Debug Level=' . Config::$debugLevel . '<br/>';
		$inipath = php_ini_loaded_file();
		echo 'php.ini path=' . $inipath . '<br/>';
	?>
</div> 
<?php } 
    Log::debug('Footer complete for ' . $_SERVER["SCRIPT_FILENAME"] . ' - sessionid=' . session_id(), 1);
?>
</footer>
