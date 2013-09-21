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
    
    $date = date("Y/m/d H:i:s");
    $profile["LastLogin"] = $date;
    
    @$version = $headers["Version"];
    @$user_agent = $headers["UA"];  
    @$user_ip = $headers["UIP"];
    @$user_location = $headers["UL"];
    if ($version != null)
        $profile["Version"] = $version;
    if ($user_agent != null)
        $profile["UA"] = @$user_agent;
    if ($user_ip != null)
        $profile["UIP"] = $user_ip;
    if ($user_location != null)
        $profile["UL"] = $user_location;
    
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
