<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../Collector.php';
    require_once dirname(__FILE__).'/'.'../../Database.php';
    
    $type = null;
    $result = array();
    if (isset($_GET["type"]))
    {
        $type = $_GET["type"];
    }
        
    $colletor = Collector::getInstance();
    
    if ($type == null or $type == "root")  // Default: get root categories
    {
        $categories = $colletor->getRootCategories();
    }
    else
    {
        $root_categories = $colletor->getRootCategories();
        foreach ($root_categories as $id => $root_category)
        {
            if ($root_category["has_sub_category"] == 1)
            {
                $categories = $colletor->getSubCategories($type);
                break;
            }
        }
    }
    
    foreach ($categories as $id => $category)
    {
        $arr1["id"] = $id;
        foreach ($category as $key => $value)
        {
            $arr1["$key"] = $value;
        }
        $arr2[] = $arr1;
    }
    $result["categories"] = $arr2;
    echo json_encode($result);
    //var_dump($categories);
?>