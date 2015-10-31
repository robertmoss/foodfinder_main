<footer>
<div id="footer"><p><!--© 2015, Palmetto New Media--></p></div>
<?php if ($debug==1) { ?>
<div id="debug">
	<?php 
		echo 'tenantID=' . $tenantID . '<br/>';
		echo 'userID=' . $userID . '<br/>';
		echo 'Debug Level=' . $debug . '<br/>';
		$inipath = php_ini_loaded_file();
		echo 'php.ini path=' . $inipath . '<br/>';
	?>
</div> 
<?php } ?>
</footer>