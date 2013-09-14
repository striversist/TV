<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Database.php';
    
    $db = Database::getInstance();
    $profiles = $db->getProfiles();    
    foreach ($profiles as $key => $profile) 
    {
        $firstUse[$key] = $profile["first_use"];
        $guid[$key] = $profile["GUID"];
    }
    array_multisort($firstUse, SORT_STRING, SORT_ASC, $guid, SORT_STRING, SORT_ASC, $profiles);
    foreach ($profiles as $key => $profile) 
    {
//        echo "GUID: ".$key."\t"."First use: ".$profile["first_use"]."<br />";
        var_dump($profile);
    }
    
    echo "Total count: ".  count($profiles)."<br />";
?>
