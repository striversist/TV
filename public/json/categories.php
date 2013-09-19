<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../../Collector.php';
    require_once dirname(__FILE__).'/'.'../../app/utils.php';
    require_once dirname(__FILE__).'/'.'../../Database.php';
    
    $type = null;
    $result = array();
    if (isset($_GET["type"]))
    {
        $type = $_GET["type"];
    }
    
    // Detect GUID header, is not set, generate a GUID header
    $headers = apache_request_headers();
    if (!isset($headers["GUID"]))
    {
        $guid = guid();
        header("GUID: ".$guid);
        $db = Database::getInstance();
        $profile["first_use"] = date("Y/m/d H:i:s");
        $profile["GUID"] = $guid;
        $db->storeProfile($profile);
//        var_dump($profiles);
    }
    
    $colletor = Collector::getInstance();
    
    if ($type == null or $type == "root")  // Default: get root categories
    {
        $categories = $colletor->getRootCategories();
    }
    else if ($type === "local")
    {
        $categories = $colletor->getLocals();
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