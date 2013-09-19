<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../Database.php';
    require_once dirname(__FILE__).'/'.'../../Collector.php';
    
    if(!isset($_GET["keyword"]) || htmlspecialchars($_GET["keyword"]) === '')
    {
        //echo "You should input search text"."<br />";
        return;
    }
    
    $keyword = htmlspecialchars($_GET["keyword"]);
    //echo "You input keyword: ".$keyword." type = ".$type."<br />";
    
    $db = Database::getInstance();
    $colletor = Collector::getInstance();
    $channels = $db->getChannels();
    
    $today = date("w");
    if ($today == "0")    // Sunday
    {
        $today = "7";
    }
    $result = array();
    foreach ($channels as $id => $days)
    {
        foreach ($days as $day => $programs) 
        {
            if ($day == $today)
            {
                $tmp = array();
                foreach ($programs as $program)
                {
                    if (strpos($program["title"], $keyword) !== FALSE)
                    {
                        //echo "You found $keyword in ".$program["title"]."<br />";
                        $tmp[] = $program;
                    }
                }
                if (count($tmp))
                {
                    $result["$id"] = $tmp;
                }
            }
        }
    }
    if (count($result))
    {
        foreach ($result as $id => $programs) 
        {
            //echo "今日 ".$colletor->getNameById($id)."<br />";
            $tmp = array();
            foreach ($programs as $program)
            {
                //echo $program["time"].": ".$program["title"]."<br />";
                $tmp[] = array("time" => $program["time"], "title" => $program["title"]);
            }
            $array["id"] = $id;
            $array["name"] = $colletor->getNameById("$id");
            $array["programs"] = $tmp;
            $array2[] = $array;
        }
        $return["result"] = $array2;
        echo json_encode($return);
    }
    else 
    {
        //echo "对不起，没有匹配的结果"."<br />";
        $return["result"] = array();
        echo json_encode($return);
    }
    
    // 收集用户搜索记录
    $headers = apache_request_headers();
    if (!isset($headers["GUID"]))
        return;
    $guid = $headers["GUID"];
    $profile = $db->getProfile($guid);
    if ($profile == false)
        return;
    
    $date = date("Y/m/d");
    // SearchRecords: key(date) => value(keywords); 
    // keywords: array of keyword
    if (!isset($profile["SearchRecords"]))
    {
        $keywords[] = $keyword;
        $search_records["$date"] = $keywords;
    }
    else
    {
        $search_records = $profile["SearchRecords"];
        $search_records["$date"][] = $keyword;
    }
    $profile["SearchRecords"] = $search_records;
    $db->storeProfile($profile);
?>
