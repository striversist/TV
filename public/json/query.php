<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'./utils.php';
    require_once dirname(__FILE__).'/'.'../../Collector.php';
    
    $colletor = Collector::getInstance();
    
    $result = array();
    if (isset($_GET["nowtime"]))
    {
        $result["nowtime"] = date("H:i:s");
    }
    else if (isset ($_GET["all_tvmao_id"]))
    {
        $all_tvmao_id = $colletor->getAllTvmaoIds();
        $id_array = array();
        foreach ($all_tvmao_id as $channel_id => $tvmao_id) 
        {
            $tmp = array();
            $tmp["id"] = $channel_id;
            $tmp["tvmao_id"] = $tvmao_id;
            $id_array[] = $tmp;
        }
        $result["all_tvmao_id"] = $id_array;
    }
    
    echo json_encode($result);
    
    // ------------------------- Functions ---------------------------------
?>
