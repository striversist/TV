<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../Database.php';
    
    $db = Database::getInstance();
    $profiles = $db->getProfiles();
    $feedbacks = array();
    foreach ($profiles as $key => $profile) 
    {
        if (isset($profile["feedbacks"]))
        {
            foreach ($profile["feedbacks"] as $dateTime => $record)
            {
                $feedbacks[$dateTime]["feedbacks"] = $record;
                $feedbacks[$dateTime]["GUID"] = $key;
            }
        }
    }
    krsort($feedbacks);
    
    foreach ($feedbacks as $dateTime => $feedback)
    {
        echo "Time: ".$dateTime."<br/>";
        echo "Feedback: ".$feedback["feedbacks"]."<br/>";
        echo "GUID: "."<a href=../profiles/profile_detail.php?guid=".$feedback["GUID"].">".$feedback["GUID"]."</a><br/>";
        echo "<br/>";
    }
?>
