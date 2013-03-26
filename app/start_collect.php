<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Collector.php';
    require_once dirname(__FILE__).'/'.'../Database.php';
    require_once dirname(__FILE__).'/'.'../ProgramFilter.php';

    echo "start collecting..."."<br />";
    
    $colletor = Collector::getInstance();
    $filter = ProgramFilter::getInstance();
    $db = Database::getInstance();
    
    $pairs = $colletor->getChannelUrls();
    foreach ($pairs as $pair => $url)
    {
        echo "collecting $pair:$url"."<br />";
        $channels[$pair] = $filter->getChannel($url);
    }
    //dump($channels);
    $db->store($channels);
    
    echo "collect finished..."."<br />";
    
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
