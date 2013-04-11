<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Collector.php';
    require_once dirname(__FILE__).'/'.'../Database.php';
    require_once dirname(__FILE__).'/'.'../ProgramFilter.php';

    echo "start collecting..."."<br />";
    
    $colletor = Collector::getInstance();
    $filter = ProgramFilter::getInstance();
    $db = Database::getInstance();
    
    //$map = $colletor->getIdUrls();
    $channels = array();
    for($day = 1; $day <= 7; $day++)
    {
        $map = $colletor->getIdUrlsByDay($day);
        foreach ($map as $id => $url)
        {
            echo "collecting $id day=$day url=$url"."<br />";
            $dom = file_get_html($url);
            $channels["$id"]["$day"] = $filter->getProgramList($dom);
            usleep(10 * 1000);  // sleep 10ms
        }
        //dump($channels);
    }
    //var_dump($channels);
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
