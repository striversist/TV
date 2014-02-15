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
?>
