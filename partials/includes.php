<?php
        include_once dirname(__FILE__) . '/../classes/config.php'; ?>
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo Config::$site_root?>/img/icons/ff_favicon.ico" />
        <link rel="stylesheet" type="text/css" href="<?php echo Config::$site_root?>/core/css/styles.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Config::$site_root?>/core/css/bootstrap.css" />	
        <link rel="stylesheet" type="text/css" href="<?php echo Config::$site_root?>/static/css/foodfinder.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Config::$site_root . '/' . Utility::getTenantProperty($applicationID, $tenantID, $userID, 'css'); ?>" />	
    
		<script src="<?php echo Config::$site_root?>/js/jquery-1.10.2.js"></script>
		<script src="<?php echo Config::$site_root?>/js/mustache.js"></script>
		<script src="<?php echo Config::$site_root?>/js/bootstrap.min.js"></script>
		<script src="<?php echo Config::$site_root?>/js/core.js"></script>
		<script src="<?php echo Config::$site_root?>/js/foodfinder.js"></script>
