<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'Collector.php';
    
    $colletor = Collector::getInstance();
    $idNames = $colletor->getIdNames();
    foreach ($idNames as $id => $name) 
    {
        $array[] = array("id" => $id, "name" => $name);
    }
    $list["channel_list"] = $array;
    echo json_encode($list);
    //var_dump($array);
?>
