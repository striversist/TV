<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Collector.php';
    require_once dirname(__FILE__).'/'.'../Database.php';
    require_once dirname(__FILE__).'/'.'../ProgramFilter.php';
    require_once dirname(__FILE__).'/'.'./utils.php';

    echo "start collecting..."."(".date("Y/m/d H:i:s").")"."<br />";
    $t = getTime();
    
    $colletor = Collector::getInstance();
    $filter = ProgramFilterFactory::createProgramFilter();
    $db = Database::getInstance();
    
    // ------------------ 收集节目信息 -------------------------
    $channels = array();
    for($day = 1; $day <= 7; $day++)
    {
        $map = $colletor->getIdUrlsByDay($day);
        foreach ($map as $id => $url)
        {
            echo "collecting $id day=$day url=$url"."<br />";
            //$dom = file_get_html($url);
            for ($i=0; $i<3; $i++)
            {
                $html = file_get_contents($url);
                if (!empty($html))
                    break;
            }
            if (get_html_charset($html) === "gb2312")
            {
                $html = gb2312_to_utf8($html);
            }
            $dom = str_get_html($html);
            $channels["$id"]["days"]["$day"] = $filter->getProgramList($dom);
            usleep(10 * 1000);  // sleep 10ms
        }
        //dump($channels);
    }
    
    foreach ($channels as $id => &$channel)
    {
        $channel["categories"] = $colletor->getCategoriesByChannelId("$id");
    }
//    var_dump($channels);
    $db->storeChannels($channels);
    
    // ------------------ 收集热门节目 -------------------------
    $url = $colletor->getHotUrl();
    echo "<br />collecting hot TV series url=$url"."<br />";
    for ($i=0; $i<3; $i++)
    {
        $html = file_get_contents($url);
        if (!empty($html))
            break;
    }
    if (get_html_charset($html) === "gb2312")
    {
        $html = gb2312_to_utf8($html);
    }
    $dom = str_get_html($html);
    $hot_info = $filter->getHotInfo($dom);
    $db->storeHotInfo($hot_info);
    
    echo "collect finished..."."(".date("Y/m/d H:i:s").")"."<br />";
    echo "total time: ".runTime($t)."s"."<br />";
    
    // ----------- calculate time function-------------  
    function getTime()
    {
        $time = explode(" ",microtime());
        $time = $time[1].substr($time[0],1);
        return $time;
    }
    
    function runTime($t,$l=3)
    {
        $dif = getTime()-$t;
        return ' '.number_format($dif,$l);
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
