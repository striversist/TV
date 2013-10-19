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

//    echo "<pre>";var_dump($searches);echo "</pre>";
    foreach ($searches as $date => $records)
    {
        echo "<h3>$date: (".count($records).")</h3>";
        for ($i=0; $i<count($records); $i++)
        {
            echo "$records[$i] ";
            if ($i == 20) echo "<br/>";
        }
        echo "<br/><br/>";
    }
?>

