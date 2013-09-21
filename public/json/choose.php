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
    echo json_encode($result);
    //dump($channels);
    
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

