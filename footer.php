<?php 
    include_once('classes/core/config.php'); 
?>
<footer>
<div id="footer"><p>© 2015, Palmetto New Media</p></div>
<?php if (Config::$debugLevel<10) { ?>
<div id="debug">
	<?php 
		echo 'tenantID=' . $tenantID . '<br/>';
		echo 'userID=' . $userID . '<br/>';
		echo 'Debug Level=' . Config::$debugLevel . '<br/>';
		$inipath = php_ini_loaded_file();
		echo 'php.ini path=' . $inipath . '<br/>';
	?>
</div> 
<?php } ?>
</footer>