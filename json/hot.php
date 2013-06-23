<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Collector.php';
    
    $result = array();
    $arr1 = array();
    $arr2 = array();
    $colletor = Collector::getInstance();
    $hots = $colletor->getHot();
    
    foreach ($hots as $id => $hot) 
    {
        $arr1["id"] = $id;
        $arr1["name"] = $hot["name"];
        $arr1["programs"] = $hot["programs"];
        $arr2[] = $arr1;
    }

    $result["hot"] = $arr2;
    echo json_encode($result);
?>
