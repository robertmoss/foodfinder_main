<?php 
/* 
 * Include this partial in any page that can only be accessed via HTTPS/SSL
 * Will redirect to requestd page with https prepended
 */
    if (strtolower($_SERVER['SERVER_NAME'])!=='localhost') {
        if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
            $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $redirect);
            die();
        }
    }