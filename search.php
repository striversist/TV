<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'Database.php';
    require_once dirname(__FILE__).'/'.'Collector.php';
    
    if(!isset($_GET["keyword"]) || htmlspecialchars($_GET["keyword"]) === '')
    {
        echo "You should input search text"."<br />";
        return;
    }
    
    $keyword = htmlspecialchars($_GET["keyword"]);
    //echo "You input keyword: ".$keyword."<br />";
    
    $db = Database::getInstance();
    $colletor = Collector::getInstance();
    $channels = $db->getChannels();
    foreach ($channels as $channel => $programes) 
    {
        $tmp = array();
        foreach ($programes as $program)
        {
            $pos = strpos($program["title"], $keyword);
            if($pos !== FALSE)
            {
                //echo "You found $keyword in ".$program["title"]."<br />";
                $tmp[] = $program;
            }
        }
        if(count($tmp))
        {
            $result["$channel"] = $tmp;
        }
    }
    
    if(count($result))
    {
        foreach ($result as $id => $programes) 
        {
            echo $colletor->getNameById($id)."<br />";
            foreach ($programes as $program)
            {
                echo $program["time"].": ".$program["title"]."<br />";
            }
            echo "<br />";
        }
    }
    else 
    {
        echo "对不起，没有匹配的结果"."<br />";
    }
    
?>
