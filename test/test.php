<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Collector.php';
    require_once dirname(__FILE__).'/'.'../Database.php';
    require_once dirname(__FILE__).'/'.'../CacheLock.php';

    $colletor = Collector::getInstance();
    $db = Database::getInstance();
    
    $profiles = $db->getProfiles();
    $today = date("Y/m/d");
    $daily_activity = 0;
    $daily_new_user = 0;
    foreach ($profiles as $profile)
    {
        $first_use = date("Y/m/d", strtotime($profile["FirstUse"]));
        $last_login = date("Y/m/d", strtotime($profile["LastLogin"]));
        if ($last_login == $today)
            $daily_activity += 1;
        if ($first_use == $today)
            $daily_new_user += 1;
    }

    // 记录到数据库中
    $lock = new CacheLock(__FILE__);
    $lock->lock();
    $daily_records = $db->getDailyProfileRecords();
    if ($daily_records == false)
        $daily_records = array();
    $daily_records["$today"]["DailyActivity"] = $daily_activity;
    $daily_records["$today"]["NewUsers"] = $daily_new_user;
    $db->storeDailyProfileRecords($daily_records);
    $lock->unlock();
    
//    $daily_records = $db->getDailyProfileRecords();
//    var_dump($daily_records);
//    if ($daily_records == false)
//    {
//        $daily_records = array();
//    }
//    $yestoday = date("Y/m/d",strtotime("-1 day"));
//    $daily_records["$yestoday"]["DailyActivity"] = 23;
//    $daily_records["$yestoday"]["NewUsers"] = 21;
//    $db->storeDailyProfileRecords($daily_records);
    
//    $result = $colletor->getCategoryNameById("wuhan");
//    $result = $colletor->getCategoriesByChannelId("beijingst");
//    var_dump($result);
    
//    $channels = $db->getChannels();
//    $channels["wuhan1"]["categories"] = $colletor->getCategoriesByChannelId("wuhan1");
//    foreach ($channels as $id => &$channel)
//    {
//        $channel["categories"] = $colletor->getCategoriesByChannelId("$id");
//    }
//    var_dump($channels);
        
?>
