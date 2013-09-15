<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'./Database.php';
    
    $db = Database::getInstance();
    $headers = apache_request_headers();
    if (!isset($headers["GUID"]))
        return;
    $guid = $headers["GUID"];
    @$ua = $headers["UA"];
    $profile = $db->getProfile($guid);
    if ($profile == false)
        return;
    
    $date = date("Y/m/d H:i:s");
    $profile["LastLogin"] = $date;
    if ($ua != null)
        $profile["UA"] = $ua;
    $db->storeProfile($profile);
?>
