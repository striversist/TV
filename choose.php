<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'Database.php';
    require_once dirname(__FILE__).'/'.'Collector.php';
    
    if(isset($_GET["channel"]) && isset($_GET["day"]))
    {
        $id = $_GET["channel"];
        $day = $_GET["day"];
        //echo "You select channel: ".$id." on day ".$day."<br />";
    }
    else
    {
        echo "You should select channel and day!!"."<br />";
        return;
    }
    $db = Database::getInstance();
    $colletor = Collector::getInstance();
    /*
    $pairs = $colletor->getChannelUrls();
    foreach ($pairs as $pair => $url)
    {
        $channels[$pair] = $filter->getChannel($url);
        //dump($channels);
    }
    $db->store($channels);
    */
    echo $colletor->getNameById("$id").getNameOfDay($day)."的节目单："."<br />";
    $channels = $db->getChannels();
    foreach ($channels[$id][$day] as $program)
    {
        echo $program["time"].": ".$program["title"]."<br />";
    }
    //dump($channels);
    
    function getNameOfDay($day)
    {   
        $weekarray = array("一","二","三","四","五","六","日");  
        return "星期".$weekarray[intval($day) - 1]; 
    }
    
    function dump($channels)
    {
        foreach ($channels as $channel => $list)
        {
            echo $channel.": <br />";
            foreach ($list as $program)
            {
                echo $program["time"].": ".$program["title"]."<br />";
            }
        }
    }
    
?>

