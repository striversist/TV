<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Database.php';
    
    $db = Database::getInstance();
    $profiles = $db->getProfiles();
    foreach ($profiles as $guid => $profile) 
    {
        echo "GUID: ".$guid."\t"."First use: ".$profile["first_use"]."<br />";
        var_dump($profile);
    }
    echo "Total count: ".  count($profiles)."<br />";
?>
