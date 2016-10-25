<?php
        include_once dirname(__FILE__) . '/../classes/config.php';
        include_once dirname(__FILE__) . '/../core/classes/utility.php';  
        
        $icon = Utility::getTenantPropertyEx($applicationID, $tenantID, $userID, 'icon', '/img/icons/ff_favicon.ico');
        
        ?>
        <meta name="google-site-verification" content="EVseykSDcywsuXfI_yxXRBoOfQ5ijEKZdrTHQmvldD8" />
        
        
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo Config::getSiteRoot() . $icon; ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo Config::getSiteRoot();?>/core/css/styles.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Config::getSiteRoot();?>/core/css/bootstrap.css" />	
        <link rel="stylesheet" type="text/css" href="<?php echo Config::getSiteRoot();?>/static/css/foodfinder.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Config::getSiteRoot() . '/' . Utility::getTenantProperty($applicationID, $tenantID, $userID, 'css'); ?>" />	
    
		<script src="<?php echo Config::getSiteRoot();?>/js/jquery-1.10.2.js"></script>
		<script src="<?php echo Config::getSiteRoot();?>/js/mustache.js"></script>
		<script src="<?php echo Config::getSiteRoot();?>/js/bootstrap.min.js"></script>
		<script src="<?php echo Config::getSiteRoot();?>/js/core.js"></script>
		<script src="<?php echo Config::getSiteRoot();?>/js/foodfinder.js"></script>
