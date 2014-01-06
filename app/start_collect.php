<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Collector.php';
    require_once dirname(__FILE__).'/'.'../Database.php';
    require_once dirname(__FILE__).'/'.'../ProgramFilter.php';
    require_once dirname(__FILE__).'/'.'./utils.php';

    echo "start collecting..."."(".date("Y/m/d H:i:s").")"."<br />";
    $t = getTime();
    
    $colletor = Collector::getInstance();
    $db = Database::getInstance();
    
    // ------------------ 收集热门节目 -------------------------
    collectHot();
    if (isset($_GET["only_hot"]))
        goto Label_Finish;
    
    // ------------------ 收集节目信息 -------------------------
    if (onlyPolishSomeChannels())
        $channels = $db->getChannels();
    else
        $channels = array();
    for($day = 1; $day <= Config::MAX_COLLECT_DAYS; $day++)
    {
        $map = $colletor->getCollectInfoByDay($day);
        foreach ($map as $id => $info)
        {
            if (onlyPolishSomeChannels())
            {
                if (!in_array("$id", getSpecialChannelIds()))
                    continue;
            }
            echo "collecting $id day=$day url=".$info["url"]."<br />";
            for ($i=0; $i<3; $i++)
            {
                $html = file_get_contents($info["url"]);
                if (!empty($html))
                    break;
            }
            if (get_html_charset($html) === "gb2312")
            {
                $html = gb2312_to_utf8($html);
            }
            $dom = str_get_html($html);
            
            $filter = ProgramFilterFactory::createProgramFilter($info["src"]);
            $programList = $filter->getProgramList($dom);
            if (empty($programList))
            {
                echo "filter nothing, skip $id, day=$day"."<br/>";
                continue;
            }
            $channels["$id"]["days"]["$day"] = $programList;
            unset($programList);
            usleep(10 * 1000);  // sleep 10ms
        }
    }
    
    foreach ($channels as $id => &$channel)
    {
        $channel["categories"] = $colletor->getCategoriesByChannelId("$id");
    }
    $db->storeChannels($channels);

Label_Finish:
    echo "<br />collect finished..."."(".date("Y/m/d H:i:s").")"."<br />";
    echo "total time: ".runTime($t)."s"."<br />";
    
    // --------------------------- Functions --------------------------------  
    function collectHot()
    {
        global $colletor, $db;
        $filter = ProgramFilterFactory::createProgramFilter();
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
    }
    
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

    function onlyPolishSomeChannels()
    {
        if (count(array_filter(getSpecialChannelIds())) > 0)
            return true;
        return false;
    }
    
    function getSpecialChannelIds()
    {
        $special_channels = array("");
        return $special_channels;
    }
    
?>
