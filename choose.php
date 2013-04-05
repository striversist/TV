<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'Database.php';
    require_once dirname(__FILE__).'/'.'Collector.php';
    
    if(isset($_GET["channel"]))
    {
        //echo "You select channel: ".$_GET["channel"]."<br />";
        $id = $_GET["channel"];
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
    echo $colletor->getNameById("$id")." 今天的节目单："."<br />";
    $channels = $db->getChannels();
    foreach ($channels[$id] as $program)
    {
        echo $program["time"].": ".$program["title"]."<br />";
    }
    //dump($channels);
    
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

