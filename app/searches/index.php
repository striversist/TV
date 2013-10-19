<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../Database.php';
    
    $db = Database::getInstance();
    $profiles = $db->getProfiles();
    $searches = array();
    foreach ($profiles as $key => $profile) 
    {
        if (isset($profile["SearchRecords"]))
        {
            foreach ($profile["SearchRecords"] as $date => $records)
            {
                foreach ($records as $record)
                    $searches[$date][] = $record;
            }
        }
    }
    ksort($searches);

    echo "<pre>";var_dump($searches);echo "</pre>";
?>

