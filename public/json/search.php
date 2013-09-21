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
    $profile = false;
    $headers = apache_request_headers();
    if (isset($headers["GUID"]))
    {
        $guid = $headers["GUID"];
        $profile = $db->getProfile($guid);
    }  
    
    $today = date("w");
    if ($today == "0")    // Sunday
    {
        $today = "7";
    }
    
    // 匹配关键字
    $result = array();
    foreach ($channels as $id => $channel)
    {
        if (!needSearch($profile, $channel))
            continue;
        foreach ($channel["days"] as $day => $programs) 
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
    
    // 返回用户结果
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
    if (!isset($headers["GUID"]))
        return;
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
    
    // ------------------------- Functions -----------------------------------
    function needSearch($profile, $channel)
    {
        // 传入的无效信息，无法判断，统一需要search
        if ($profile == false || $channel == false)
            return true;
        // 若不在local类别中，则为全国频道，需要search
        if (!isInOnlyLocalCategory($channel))
            return true;
        // 若在local类别中，则需匹配用户所在位置，匹配则需要search
        if (isMatchUserLocation($profile, $channel))
            return true;
        return false;
    }
    
    function isInOnlyLocalCategory($channel)
    {
        // 若节目的类别属性中有local，则为属于本地节目
        $count = count(@$channel["categories"]);
        foreach (@$channel["categories"] as $category_id => $category_name)
        {
            // 若含有local类别，且该节目具有的类别总数为2
            // （一个local，一个具体的city或province），则该节目只属于本地节目
            if ($category_id == "local" and $count == 2)
                return true;
        }
        return false;
    }
    
    function isMatchUserLocation($profile, $channel)
    {
        if (!isset($profile["UL"]))
            return false;
        
        $location = $profile["UL"];
        foreach ($channel["categories"] as $category_id => $category_name)
        {
            // 若发现本地类型类型名称“北京、武汉...”出现在$location中，
            // 则说明用户所在地的本地类型为“北京、武汉...”
            if (strpos($location, $category_name) !== FALSE)
            {
//                echo "Found $location contains $category_name"."<br />";
                return true;
            }
        }
        return false;
    }
?>
