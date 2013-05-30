<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Collector.php';
    
    $type = null;
    $result = array();
    if (isset($_GET["type"]))
    {
        $type = $_GET["type"];
    }
    
    $colletor = Collector::getInstance();
    
    if ($type == null)  // Default: get root categories
    {
        $categories = $colletor->getRootCategories();
    }
    else if ($type === "local")
    {
        $categories = $colletor->getLocals();
    }
    
    foreach ($categories as $id => $category)
    {
        $array[] = array("id" => $id, "name" => $category);
    }
    $result["categories"] = $array;
    echo json_encode($result);
    //var_dump($categories);
?>