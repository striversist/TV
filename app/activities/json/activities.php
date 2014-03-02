<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../../Collector.php';
    require_once dirname(__FILE__).'/'.'../../../Database.php';
    require_once dirname(__FILE__).'/'.'../../../CacheLock.php';
    require_once dirname(__FILE__).'/'.'../../utils.php';

    $db = Database::getInstance();
    $activities = $db->getLoginRecords();
    $uninstalls = $db->getUninstallRecords();
    
    foreach ($activities as &$activity) 
    {
        foreach ($uninstalls as &$uninstall)
        {
            if ($activity["Date"] == $uninstall["Date"])
            {
                $activity["Uninstalls"] = array_merge (@$uninstall["NewUsers"] + @$uninstall["LoyalUsers"]);
            }
        }
    }
    
    $result = array();
    foreach ($activities as $index => $activity)  // index为数字角标0,1,2...
    {
        $dates[$index] = $activity["Date"];
    }
    array_multisort($dates, SORT_STRING, SORT_ASC, $activities);    // 按日期排序
    
    foreach ($activities as $activity)
    {
//        echo "Date:".$activity["Date"]."<br/>";
//        echo "new_users: ".count($activity["NewUsers"])." loyal_users:".count($activity["LoyalUsers"])." uninstalls:".@count($activity["Uninstalls"])."<br/><br/>";
        $date = (strtotime($activity["Date"])+(3600*24)) * 1000;  // 需要加一天时间
        $num = count($activity["NewUsers"]) + count($activity["LoyalUsers"]);
        
//        $result[] = array($date, $num);
        $result["Activities"][] = array($date, $num);
        $result["NewUsers"][] = array($date, count($activity["NewUsers"]));
        $result["LoyalUsers"][] = array($date, count($activity["LoyalUsers"]));
        $result["Uninstalls"][] = array($date, isset($activity["Uninstalls"]) ? count($activity["Uninstalls"]) : 0);
    }
    
    echo json_encode($result);
?>
