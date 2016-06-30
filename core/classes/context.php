<?php

class Context{
 
    public static $currentUser;
    public static $tenantid;
    
    
    
    public static function getUser($userid) {

        // returns user object for specified user; right now, we just cache the currentUser object
        // if that's what caller is looking for, we return it; otherwise instantiate new user object
            
        if ($currentUser && $currentUser->id=$userid) {
            return $currentUser;
        }
        else {
            return new User($userid,self::$tenantid);
        }
    }
    
    /* 
     * Returns the type of device that is acccessing the page
     */
    public static function deviceType() {
        
        $userAgent = $_SERVER["HTTP_USER_AGENT"];
        $devicesTypes = array(
            "computer" => array("msie 10", "msie 9", "msie 8", "windows.*firefox", "windows.*chrome", "x11.*chrome", "x11.*firefox", "macintosh.*chrome", "macintosh.*firefox", "opera"),
            "tablet"   => array("tablet", "android", "ipad", "tablet.*firefox"),
            "smartphone"   => array("mobile ", "android.*mobile", "iphone", "ipod", "opera mobi", "opera mini"),
            "bot"      => array("googlebot", "mediapartners-google", "adsbot-google", "duckduckbot", "msnbot", "bingbot", "ask", "facebook", "yahoo", "addthis")
            );
        
        foreach($devicesTypes as $deviceType => $devices) {           
            foreach($devices as $device) {
                if(preg_match("/" . $device . "/i", $userAgent)) {
                    $deviceName = $deviceType;
                }
            }
        }
        
        return ucfirst($deviceName);
    }
    
    public static function deviceOSClass() {
        $os = Context::deviceOS();
        return $os['class'];
    }
    
    public static function deviceOSType() {
        $os = Context::deviceOS();
        return $os['type'];
    }
    
    public static function deviceOS() {
    
            $userAgent = $_SERVER["HTTP_USER_AGENT"];
            $return = array(
                'class' => 'unknown',
                'type'=>'unknown');
            $os_array = array(
                            'Windows' => array ('/windows nt 10/i'     =>  'Windows 10',
                                '/windows nt 6.3/i'     =>  'Windows 8.1',
                                '/windows nt 6.2/i'     =>  'Windows 8',
                                '/windows nt 6.1/i'     =>  'Windows 7',
                                '/windows nt 6.0/i'     =>  'Windows Vista',
                                '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                                '/windows nt 5.1/i'     =>  'Windows XP',
                                '/windows xp/i'         =>  'Windows XP',
                                '/windows nt 5.0/i'     =>  'Windows 2000',
                                '/windows me/i'         =>  'Windows ME',
                                '/win98/i'              =>  'Windows 98',
                                '/win95/i'              =>  'Windows 95',
                                '/win16/i'              =>  'Windows 3.11'),
                            'Mac' => array ('/macintosh|mac os x/i' =>  'Mac OS X',
                                '/mac_powerpc/i'       =>  'Mac OS 9',
                                '/ipod/i'               =>  'iPod',
                                '/ipad/i'               =>  'iPad',
                                '/iphone/i'             =>  'iPhone'),
                            'Linux/Unix' => array (
                                '/linux/i'              =>  'Linux',
                                '/ubuntu/i'             =>  'Ubuntu'),
                            'Android' => array (
                                '/android/i'            =>  'Android'),
                            'Blackberry' => array('/blackberry/i'         =>  'BlackBerry')
                        );
            
            foreach ($os_array as $os_class => $os_types) 
            foreach ($os_types as $os_name=> $os_type) { 
            if (preg_match($os_name, $userAgent)) {
                 $return = array(
                    'class' => $os_class,
                    'type'=>$os_type);
                 break;
                }
             }   
        return $return;
    
    }
        
               
}
    