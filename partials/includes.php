<?php
        include_once dirname(__FILE__) . '/../classes/config.php'; ?>
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo Config::getSiteRoot();?>/img/icons/ff_favicon.ico" />
        <link rel="stylesheet" type="text/css" href="<?php echo Config::getSiteRoot();?>/core/css/styles.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Config::getSiteRoot();?>/core/css/bootstrap.css" />	
        <link rel="stylesheet" type="text/css" href="<?php echo Config::getSiteRoot();?>/static/css/foodfinder.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Config::getSiteRoot() . '/' . Utility::getTenantProperty($applicationID, $tenantID, $userID, 'css'); ?>" />	
    
		<script src="<?php echo Config::getSiteRoot();?>/js/jquery-1.10.2.js"></script>
		<script src="<?php echo Config::getSiteRoot();?>/js/mustache.js"></script>
		<script src="<?php echo Config::getSiteRoot();?>/js/bootstrap.min.js"></script>
		<script src="<?php echo Config::getSiteRoot();?>/js/core.js"></script>
		<script src="<?php echo Config::getSiteRoot();?>/js/foodfinder.js"></script>
