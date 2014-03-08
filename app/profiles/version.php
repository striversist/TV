<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../Database.php';
    
    $db = Database::getInstance();
    $profiles = $db->getProfiles();
    
    $week_ago = date("Y/m/d",strtotime("-7 day"));
    $month_ago = date("Y/m/d",strtotime("-30 day"));
    
    $versions_week = array();
    $versions_month = array();
    
    $total_week = 0;
    $total_month = 0;
    foreach ($profiles as $profile) 
    {
        if (isset($profile["Version"]))
        {
            $last_login = date("Y/m/d", strtotime($profile["LastLogin"]));
            if ($last_login >= $week_ago)
            {
                $version = $profile["Version"];
                @$versions_week["$version"] += 1;
                $total_week++;
            }
            if ($last_login >= $month_ago)
            {
                $version = $profile["Version"];
                @$versions_month["$version"] += 1;
                $total_month++;
            }
        }
    }
    krsort($versions_week);
    krsort($versions_month);
    
    echo "周活跃度： ".$total_week."<br/><br/>";
    foreach ($versions_week as $version => $num) 
    {
        echo "Version: $version =>  $num"."<br/>";
    }
    echo "<br/><br/>";
    
    echo "月活跃度： ".$total_month."<br/><br/>";
    foreach ($versions_month as $version => $num) 
    {
        echo "Version: $version =>  $num"."<br/>";
    }
?>