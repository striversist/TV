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
    
    // ------------------ 收集热门节目 -------------------------
    collectHot();  
    
    echo "<br />collect finished..."."(".date("Y/m/d H:i:s").")"."<br />";
    echo "total time: ".runTime($t)."s"."<br />";
    
    // --------------------------- Functions --------------------------------  
    function collectHot()
    {
        global $colletor, $filter, $db;
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
    
?>
