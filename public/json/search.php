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
    $collector = Collector::getInstance();
    $profile = false;
    $headers = apache_request_headers();
    if (isset($headers["GUID"]))
    {
        $guid = $headers["GUID"];
        $profile = $db->getProfile($guid);
    }
    @$version = $headers["Version"];
    
    $today = date("w");
    if ($today == "0")    // Sunday
        $today = "7";
    
    // 匹配关键字: 频道名称
    $result_channels = array();
    $channel_names = $collector->getIdNames();
    foreach ($channel_names as $id => $name)
    {
        if (strpos($name, $keyword) !== FALSE)  // Found
        {
            //echo "You found $keyword in ".$name."<br />";
            $result_channels["$id"] = $name;
        }
    }
    
    // 匹配关键字: 节目列表
    $result_programs = array();
    $search_categories = getSearchCategories($profile);
    foreach ($search_categories as $category_id)
    {
        $channels = $db->getChannelsByCategory($category_id);
        if ($channels == false)
            continue;
        foreach ($channels as $id => $channel)
        {
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
                        $result_programs["$id"] = $tmp;
                    }
                }
            }
        }
    }
    
    // 返回用户结果
    if ($version != null and $version < "1.1.0")
    {
        show_result_v1($result_programs);
    }
    else
    {
        show_result_v2($result_channels, $result_programs);
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
    function getSearchCategories($profile)
    {
        $result = array("cctv", "satellitetv", "hd");
        $local_id = getLocalCategoryIdByUserLocation($profile);
        if ($local_id != false)
            $result[] = $local_id;
        return $result;
    }
    
    function getLocalCategoryIdByUserLocation($profile)
    {
        if (!isset($profile["UL"]))
            return false;
        global $collector;
        $location = $profile["UL"];
        $locals = $collector->getSubCategories("local");
        foreach ($locals as $id => $category)
        {
            // 若发现本地类型类型名称“北京、武汉...”出现在$location中，
            // 则说明用户所在地的本地类型为“北京、武汉...”
            if (strpos($location, $category["name"]) !== FALSE)
            {
//                echo "Found $location contains $category_name"."<br />";
                return $id;
            }
            
            // 有时用户位置只有省，没有市（使用GPRS等网络时），因此还需要比较
            // 地方台所在省份，若包含省份名称，则该省的地方台也能被搜索到
            // 即：该省的用户可以搜索到该省对应的地方台的节目
            $more_locations = getMultiLocationsByCategory($id);
            foreach ($more_locations as $more_location)
            {
                if (strpos($location, $more_location) !== FALSE)
                {
//                    echo "Found $location contains $more_location"."<br />";
                    return true;
                }
            }
        }
    }
    
    function getMultiLocationsByCategory($category_id)
    {
//        echo "getMultiLocationsByCategory id=$category_id"."<br />";
        $provinces = array(
            "changsha"  => "湖南", "wuhan"    => "湖北",  "guangzhou" => "广东", "nanning" => "广西",   
            "zhengzhou" => "河南", "shijiazhuang" => "河北", "jinan"  => "山东", "taiyuan" => "山西", 
            "nanchang"  => "江西", "nanjing"  => "江苏", "hangzhou"  => "浙江", "haerbin" => "黑龙江", 
            "wulumuqi"  => "新疆", "kunming"  => "云南", "guiyang"   => "贵州", "fuzhou"  => "福建", 
            "changchun" => "吉林", "hefei"    => "安徽", "chengdu"   => "四川", "lasa"    => "西藏", 
            "yinchuan"  => "宁夏", "shenyang" => "辽宁", "xining"    => "青海", "lanzhou" => "甘肃", 
            "xian"      => "陕西", "haikou"   => "海南", "huhehaote" => "内蒙");
   
        @$locations[] = $provinces["$category_id"];
        return $locations;
    }
    
    /**
     * 为1.0.x的终端版本显示返回结果
     */
    function show_result_v1($result_programs)
    {
        global $collector;
        if (count($result_programs))
        {
            foreach ($result_programs as $id => $programs) 
            {
                //echo "今日 ".$collector->getNameById($id)."<br />";
                $tmp = array();
                foreach ($programs as $program)
                {
                    //echo $program["time"].": ".$program["title"]."<br />";
                    $tmp[] = array("time" => $program["time"], "title" => $program["title"]);
                }
                $array["id"] = $id;
                $array["name"] = $collector->getNameById("$id");
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
    }
    
    /**
     * 为1.1.0及以上的终端版本返回结果
     */
    function show_result_v2($result_channels, $result_programs)
    {
        global $collector;
        $return = array();
        if (count($result_channels))
        {
            $tmp = array();
            foreach ($result_channels as $id => $name)
            {
                $tmp[] = array("id" => $id, "name" => $name);
            }
            $return["result_channels"] = $tmp;
        }
        else
        {
            $return["result_channels"] = array();
        }
        
        if (count($result_programs))
        {
            foreach ($result_programs as $id => $programs) 
            {
                //echo "今日 ".$collector->getNameById($id)."<br />";
                $tmp = array();
                foreach ($programs as $program)
                {
                    //echo $program["time"].": ".$program["title"]."<br />";
                    $tmp[] = array("time" => $program["time"], "title" => $program["title"]);
                }
                $array["id"] = $id;
                $array["name"] = $collector->getNameById("$id");
                $array["programs"] = $tmp;
                $array2[] = $array;
            }
            $return["result_programs"] = $array2;
        }
        else
        {
            $return["result_programs"] = array();
        }
        
        echo json_encode($return);
    }
?>
