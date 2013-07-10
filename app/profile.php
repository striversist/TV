<?php
    require_once dirname(__FILE__).'/'.'../Database.php';
    
    $db = Database::getInstance();
    $profiles = $db->getProfiles();
    foreach ($profiles as $guid => $profile) 
    {
        echo "GUID: ".$guid."\t"."First use: ".$profile["first_use"]."<br />";
    }
    echo "Total count: ".  count($profiles)."<br />";
?>
