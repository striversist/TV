<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../Database.php';
    require_once dirname(__FILE__).'/'.'./utils.php';
    
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
    $result["day"] = $program["day"];
    
    echo json_encode($result);
    
    // ------------------------- Functions ---------------------------------
?>
