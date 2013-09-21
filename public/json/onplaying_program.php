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
    $today = date("w");
    if ($today == "0")    // Sunday
    {
        $today = "7";
    }
    $now = date("H:i");
    $result = array("id" => $id);
    
    //echo "id=$id, today=$today, now=$now"."<br />";
    @$programs = $channels[$id]["days"][$today];        // id可能为未知，用@抑制错误

    for($i=0; $i<count($programs); $i++)
    {
        // TODO: 跨天的情况没有考虑，不过考虑转钟时使用的人数非常少，放在将在实现该功能
        if (($now >= $programs[$i]["time"] && $now < @$programs[$i+1]["time"]) or ($i == count($programs) - 1))
        {
//            echo "Found the program now playing: ".$programs[$i]['time'].": ".$programs[$i]['title']."<br />";
            $result["time"] = $programs[$i]['time'];
            $result["title"] = $programs[$i]['title'];
            break;
        }
    }
    
    echo json_encode($result);
?>
