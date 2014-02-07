<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Database.php';
       
    $db = Database::getInstance();
    $headers = apache_request_headers();
    if (!isset($headers["GUID"]))
        return;
    $guid = $headers["GUID"];
    $profile = $db->getProfile($guid);
    if ($profile == false)
        return;
    
    if (isset($_GET["uninstall"]))
    {
        $profile["Uninstall"] = true;
    }
    
    $db->storeProfile($profile);
    
   
?>
