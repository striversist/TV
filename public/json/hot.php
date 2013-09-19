<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../Database.php';
    
    $result = array();   
    $db = Database::getInstance();
    $hot_info = $db->getHotInfo();
    $channel_list = array();
    foreach ($hot_info as $channel_name => $programes_list)
    {
        $program_name_list = array();
        foreach ($programes_list as $i => $programs)
            $program_name_list[] = (string)$programs["name"];
        $channel["name"] = $channel_name;
        $channel["programs"] = $program_name_list;
        $channel_list[] = $channel;
    }
    $result["hot"] = $channel_list;
    
    echo json_encode($result);
?>
