<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Database.php';
    
    $type = null;
    if (isset($_GET["type"]))
        $type = $_GET["type"];
    else
        return;
    
    $db = Database::getInstance();
    $headers = apache_request_headers();
    if (!isset($headers["GUID"]))
        return;
    $guid = $headers["GUID"];
    $profile = $db->getProfile($guid);
    if ($profile == false)
        return;
    
    if ($type == "collect")
        on_report_collect ();
    if ($type == "search")
        on_report_search ();
    
    $db->storeProfile($profile);
    
    // ----------------- functions -------------------
    function on_report_collect()
    {
        global $profile;
        $json = $_POST["channels"];
//        echo "on_report_collect: json=".$json."<br />";
        $collect_channels = json_decode($json);
        $id_array = array();
        foreach ($collect_channels->channels as $id)
        {
            $id_array[] = $id;
        }
        $profile["Collect"] = $id_array;
    }
    
    function on_report_search()
    {
        global $profile;
        $keyword = $_GET["keyword"];
        
        $date = date("Y/m/d");
        // SearchRecords: key(date) => value(keywords); 
        // keywords: array of keyword
        if (!isset($profile["SearchRecords"]))
        {
            $keywords[] = $keyword;
            $search_records["$date"] = $keywords;
        }
        else
        {
            $search_records = $profile["SearchRecords"];
            $search_records["$date"][] = $keyword;
        }
        $profile["SearchRecords"] = $search_records;
    }
    
?>
