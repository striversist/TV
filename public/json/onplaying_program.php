<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../Database.php';
    
    if(!isset($_GET["channel"]) || htmlspecialchars($_GET["channel"]) === '')
    {
        return;
    }
    
    $id = htmlspecialchars($_GET["channel"]);
    $db = Database::getInstance();
    $channels = $db->getChannels();
    $result = array();
    $program = getOnPlayingProgram($channels[$id]);
    $result["id"] = $id;
    $result["time"] = $program["time"];
    $result["title"] = $program["title"];
    
    echo json_encode($result);
    
    // ------------------------- Functions ---------------------------------
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
?>
