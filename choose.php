<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'Database.php';
    
    if(isset($_GET["channel"]))
    {
        //echo "You select channel: ".$_GET["channel"]."<br />";
        $channel = $_GET["channel"];
    }
    $db = Database::getInstance();
    
    /*
    $pairs = $colletor->getChannelUrls();
    foreach ($pairs as $pair => $url)
    {
        $channels[$pair] = $filter->getChannel($url);
        //dump($channels);
    }
    $db->store($channels);
    */
    echo "$channel 今天的节目单："."<br />";
    $channels = $db->getChannels();
    foreach ($channels[$channel] as $program)
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

