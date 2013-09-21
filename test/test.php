<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Collector.php';
    require_once dirname(__FILE__).'/'.'../Database.php';

    $colletor = Collector::getInstance();
    $db = Database::getInstance();
//    $result = $colletor->getCategoryNameById("wuhan");
//    $result = $colletor->getCategoriesByChannelId("beijingst");
//    var_dump($result);
    
    $channels = $db->getChannels();
//    $channels["wuhan1"]["categories"] = $colletor->getCategoriesByChannelId("wuhan1");
//    foreach ($channels as $id => &$channel)
//    {
//        $channel["categories"] = $colletor->getCategoriesByChannelId("$id");
//    }
    var_dump($channels);
        
?>
