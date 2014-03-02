<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../Database.php';
    
    $db = Database::getInstance();
    $profiles = $db->getProfiles();
    
    $threshold_day = date("Y/m/d",strtotime("-30 day"));
    $versions = array();
    $total = 0;
    foreach ($profiles as $profile) 
    {
        if (isset($profile["Version"]))
        {
            $last_login = date("Y/m/d", strtotime($profile["LastLogin"]));
            if ($last_login >= $threshold_day)
            {
                $version = $profile["Version"];
                @$versions["$version"] += 1;
                $total++;
            }
        }
    }
    krsort($versions);
    
    echo "月活跃度： ".$total."<br/><br/>";
    foreach ($versions as $version => $num) 
    {
        echo "Version: $version =>  $num"."<br/>";
    }
?>