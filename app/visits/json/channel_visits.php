<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../../Collector.php';
    require_once dirname(__FILE__).'/'.'../../../Database.php';
    require_once dirname(__FILE__).'/'.'../../../CacheLock.php';
    require_once dirname(__FILE__).'/'.'../../utils.php';

    if (!isset($_GET["date"]))
        return;
    
    $date = htmlspecialchars($_GET["date"]);
    if (!is_full_date($date, "-"))
        return;
    
    $colletor = Collector::getInstance();
    $db = Database::getInstance();
    $visit_records = $db->getChannelVisitRecords();
    
    $choose_date = date("Y/m/d", strtotime($date));
    if (!isset($visit_records[$choose_date]))
        return;
    
    $one_recored = $visit_records[$choose_date];
    foreach ($one_recored as $channel_id => $visit_info)
    {
//        echo "$channel_id: ".$visit_info["VisitTimes"]."<br />";
        $visit_times[$channel_id] = $visit_info["VisitTimes"];
    }
    array_multisort($visit_times, SORT_NUMERIC , SORT_DESC, $one_recored);
//    var_dump($one_recored);

    $result["date"] = $choose_date;
    foreach ($one_recored as $channel_id => $visit_info) 
    {
        $channel_name = $colletor->getNameById($channel_id);
        $result["channels"]["$channel_name"] = $visit_info["VisitTimes"];
    }
    
    echo json_encode($result);
?>
