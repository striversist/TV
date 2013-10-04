<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../Database.php';
    require_once dirname(__FILE__).'/'.'./utils.php';
    
    if(!isset($_GET["channel"]) || htmlspecialchars($_GET["channel"]) === '')
    {
        return;
    }
    
    $result = array();
    $id = htmlspecialchars($_GET["channel"]);
    $db = Database::getInstance();
    $channel = $db->getChannelById($id);
    
    if ($channel != false)
    {
        $program = getOnPlayingProgram($channel);
        $result["id"] = $id;
        $result["time"] = $program["time"];
        $result["title"] = $program["title"];
        $result["day"] = $program["day"];
    }
    
    echo json_encode($result);
    
    // ------------------------- Functions ---------------------------------
?>
