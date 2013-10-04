<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../Database.php';
    require_once dirname(__FILE__).'/'.'./utils.php';
    
    if(!isset($_POST["channels"]) || htmlspecialchars($_POST["channels"]) === '')
    {
        return;
    }
    $json = $_POST["channels"];
    //echo "json = $json"."<br />";
    $request_channels = json_decode($json);
    $db = Database::getInstance();
    $today = date("w");
    if ($today == "0")    // Sunday
    {
        $today = "7";
    }
    $return = array();
    foreach ($request_channels->channels as $id) 
    {   
        $channel = $db->getChannelById($id);
        if ($channel == false)
            continue;;
        $onplaying_program = getOnPlayingProgram($channel);
        
        $tmp = array();
        $tmp["id"] = $id;
        $tmp["time"] = $onplaying_program["time"];
        $tmp["title"] = $onplaying_program["title"];
        $tmp["day"] = $onplaying_program["day"];
        $result[] = $tmp;
    }
    $return["result"] = $result;
    echo json_encode($return);
?>
