<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Database.php';
    require_once dirname(__FILE__).'/'.'../app/utils.php';
    
    // Detect GUID header, is not set, generate a GUID header
    $headers = apache_request_headers();
    $db = Database::getInstance();
    $profile = array();
    if (!isset($headers["GUID"]))
    {
        $guid = guid();
        header("GUID: ".$guid);
        $profile["GUID"] = $guid;
        $profile["FirstUse"] = date("Y/m/d H:i:s");
    }
    else
    {
        $guid = $headers["GUID"];
        $profile = $db->getProfile($guid);
        if ($profile == false)
            return;
    }
    
    @$ua = $headers["UA"];  
    @$version = $headers["Version"];
    $date = date("Y/m/d H:i:s");
    $profile["LastLogin"] = $date;
    if ($ua != null)
        $profile["UA"] = $ua;
    if ($version != null)
        $profile["Version"] = $version;
    $profile["RemoteIP"] = get_remote_ip();
    $db->storeProfile($profile);
    
    // ------------------------ Functions --------------------------------------
    function get_remote_ip() 
    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) 
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) 
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        else if(!empty($_SERVER["REMOTE_ADDR"])) 
            $cip = $_SERVER["REMOTE_ADDR"];
        else 
            $cip = '';

        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);
        return $cip;
    }

?>
