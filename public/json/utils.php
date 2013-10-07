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
?>
