<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Database.php';
    
    if(!isset($_POST["channels"]) || htmlspecialchars($_POST["channels"]) === '')
    {
        return;
    }
    
    $json = $_POST["channels"];
    //echo "json = $json"."<br />";
    $request_channels = json_decode($json);
    $db = Database::getInstance();
    $channels = $db->getChannels();
    $today = date("w");
    if ($today == "0")    // Sunday
    {
        $today = "7";
    }
    $now = date("H:i");
    $return = array();
    
    foreach ($request_channels->channels as $id) 
    {
        $tmp = array();
        //echo "id=$id, today=$today, now=$now"."<br />";
        @$programs = $channels[$id][$today];        // id可能为未知，用@抑制错误
        for($i=0; $i<count($programs); $i++)
        {
            // TODO: 跨天的情况没有考虑，不过考虑转钟时使用的人数非常少，放在将在实现该功能
            if ($now >= @$programs[$i]["time"] && $now < @$programs[$i+1]["time"])
            {
                //echo "Found the program now playing: ".$programs[$i]['time'].": ".$programs[$i]['title']."<br />";
                $tmp["id"] = $id;
                $tmp["time"] = $programs[$i]['time'];
                $tmp["title"] = $programs[$i]['title'];
                $result[] = $tmp;
                break;
            }
        }
    }
    $return["result"] = $result;
    echo json_encode($return);
?>
