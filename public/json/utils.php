<?php
    // --------------------------- Functions --------------------------------
    function getOnPlayingProgram($channel)
    {
        $program = array();
        $today = date("w");
        if ($today == "0")    // Sunday
            $today = "7";
        $now = date("H:i");

        $programs = $channel["days"][$today];

        for($i=0; $i<count($programs); $i++)
        {
            // TODO: 跨周的情况没有考虑，不过考虑跨周转钟时使用的人数非常少，放在将来实现该功能
            // 跨天情况：正在播放前一天的最后一个节目
            if ($now < $programs[0]["time"])
            {
//                echo "today=$today, now=$now"."<br />";
                $last_day = $today == 1 ? 7 : $today - 1;
                $last_day_programs = $channel["days"][$last_day];
                $program["time"] = $last_day_programs[count($last_day_programs)-1]["time"];
                $program["title"] = $last_day_programs[count($last_day_programs)-1]["title"];
                $program["day"] = $last_day;
//                var_dump($program);
                break;
            }
            if (($now >= $programs[$i]["time"] && $now < @$programs[$i+1]["time"]) or ($i == count($programs) - 1))
            {
    //            echo "Found the program now playing: ".$programs[$i]['time'].": ".$programs[$i]['title']."<br />";
                $program["time"] = $programs[$i]['time'];
                $program["title"] = $programs[$i]['title'];
                $program["day"] = $today;
                break;
            }
        }
        return $program;
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
    
    function getMatchedChannels($keyword)
    {
        $collector = Collector::getInstance();
        $result_channels = array();
        $channel_names = $collector->getIdNames();
        foreach ($channel_names as $id => $name)
        {
            if (stripos($name, $keyword) !== FALSE)  // Found
            {
                //echo "You found $keyword in ".$name."<br />";
                $result_channels["$id"] = $name;
            }
        }
        return $result_channels;
    }
    
    function getMatchedPrograms($keyword, $search_categories, $target_day)
    {
        $db = Database::getInstance();
        $result_programs = array();
        foreach ($search_categories as $category_id)
        {
            $channels = $db->getChannelsByCategory($category_id);
            if ($channels == false)
                continue;
            foreach ($channels as $id => $channel)
            {
                foreach ($channel["days"] as $day => $programs) 
                {
                    if ($day == $target_day)
                    {
                        $tmp = array();
                        foreach ($programs as $program)
                        {
                            if (stripos($program["title"], $keyword) !== FALSE)
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
        return $result_programs;
    }
    
    function getHighFrequencyWords($array, $num)
    {
        $similarThreshold = 90;
        $hits = array();
        $result = array();
        foreach ($array as $target)
        {
            $hit = 0;
            foreach ($array as $word)
            {
                $percent = 0;
                similar_text($target, $word, $percent);
//                echo "target=$target, word=$word, percent=$percent"."<br/>";
                if ($percent > $similarThreshold)
                    $hit++;
            }
            $hits[$hit][] = $target;
//            echo "target=$target, hit=$hit"."<br/>";
        }
        krsort($hits);  // 倒序重排: 频率从高到低
        foreach ($hits as $hit => $words)   // 按照频率从高到低的顺序
        {
//            echo "hit=$hit, count=".count($words)."<br/>";
            for ($i=0; $i<count($words); $i++)
            {
                if (count($result) >= $num)
                    break;
                if (!in_arrayi($words[$i], $result))
                    $result[] = $words[$i];
            }
        }
        
        return $result;
    }
    
    function isAvailableSearchWord($keyword)
    {
        $channels = getMatchedChannels($keyword);
        if (count($channels) > 0)
            return true;
        
        $today = date("w") == "0" ? "7" : date("w");
        $search_categories = getRootSearchCategories();
        $programs = getMatchedPrograms($keyword, $search_categories, $today);
        if (count($programs) > 0)
            return true;
        
        return false;
    }
    
    function getRootSearchCategories()
    {
        $collector = Collector::getInstance();
        // 不想被搜索的分类：local，直接搜索耗时大，且其它省份无法看
        $exclude_root_categories = array("local");
        
        $root_categories = $collector->getRootCategories();
        $result = array();
        foreach ($root_categories as $category_id => $category) 
        {
            if (!in_array($category_id, $exclude_root_categories))
                $result[] = $category_id;
        }
        return $result;
    }
    
    // case-insensitive in_array
    function in_arrayi($needle, $haystack) 
    {
        return in_array(strtolower($needle), array_map('strtolower', $haystack));
    }
?>
