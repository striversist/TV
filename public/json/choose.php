<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../Database.php';
    
    if(isset($_GET["channel"]) && isset($_GET["day"]))
    {
        $id = $_GET["channel"];
        $day = $_GET["day"];
        //echo "You select channel: ".$id." on day ".$day."<br />";
    }
    else
    {
        //echo "You should select channel and day!!"."<br />";
        $result["result"] = array();
        echo json_encode($result);
        return;
    }
    $needOnPlaying = false;
    if (isset($_GET["onplaying"]))
        $needOnPlaying = true;
    $db = Database::getInstance();
    $channels = $db->getChannels();
    
    foreach ($channels[$id]["days"][$day] as $program)
    {
        //echo $program["time"].": ".$program["title"]."<br />";
        $array[] = array("time" => $program["time"], "title" => $program["title"]);
    }
    
    $result["result"] = $array;
    $result["id"] = $id;
    $result["day"] = $day;
    if ($needOnPlaying)
    {
        $program = getOnPlayingProgram($channels[$id]);
        $result["onplaying"] = $program;
    }
    
    echo json_encode($result);
    //dump($channels);
    
    // --------------------------- Functions --------------------------------
    function getOnPlayingProgram($channel)
    {
        $program = array();
        $today = date("w");
        if ($today == "0")    // Sunday
            $today = "7";
        $now = date("H:i");

        //echo "id=$id, today=$today, now=$now"."<br />";
        @$programs = $channel["days"][$today];        // id可能为未知，用@抑制错误

        for($i=0; $i<count($programs); $i++)
        {
            // TODO: 跨天的情况没有考虑，不过考虑转钟时使用的人数非常少，放在将来实现该功能
            if (($now >= $programs[$i]["time"] && $now < @$programs[$i+1]["time"]) or ($i == count($programs) - 1))
            {
    //            echo "Found the program now playing: ".$programs[$i]['time'].": ".$programs[$i]['title']."<br />";
                $program["time"] = $programs[$i]['time'];
                $program["title"] = $programs[$i]['title'];
                break;
            }
        }
        return $program;
    }
    
    function getNameOfDay($day)
    {   
        $weekarray = array("一","二","三","四","五","六","日");  
        return "星期".$weekarray[intval($day) - 1]; 
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

