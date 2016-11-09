<?php 
    include_once("classes/config.php");
    // to do: right now fb:admins defaulting to robert moss — probably should be a tenant setting for this
    ?>
        <meta property="og:url"           content="<?php echo Config::getSiteRoot() . '' . $_SERVER['REQUEST_URI']?>" />
        <meta property="og:locale"        content="en_US">
        <meta property="fb:app_id"        content="1852840705002388">
        <meta property="fb:admins"        content="robert.moss.3720">

