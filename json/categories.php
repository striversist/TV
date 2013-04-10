<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Collector.php';
    
    $colletor = Collector::getInstance();
    
    $idCategories = $colletor->getIdCategories();
    foreach ($idCategories as $id => $category) 
    {
        $array[] = array("id" => $id, "name" => $category);
    }
    $list["categories"] = $array;
    echo json_encode($list);
    //var_dump($categories);
?>