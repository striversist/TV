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
    //echo "You input keyword: ".$keyword." type = ".$type."<br />";
    
    $db = Database::getInstance();
    $colletor = Collector::getInstance();
    $channels = $db->getChannels();
    
    $today = date("w");
    if ($today == "0")    // Sunday
    {
        $today = "7";
    }
    $result = array();
    foreach ($channels as $id => $days)
    {
        foreach ($days as $day => $programs) 
        {
            if ($day == $today)
            {
                $tmp = array();
                foreach ($programs as $program)
                {
                    if (strpos($program["title"], $keyword) !== FALSE)
                    {
                        //echo "You found $keyword in ".$program["title"]."<br />";
                        $tmp[] = $program;
                    }
                }
                if (count($tmp))
                {
                    $result["$id"] = $tmp;
                }
            }
        }
    }
    if (count($result))
    {
        foreach ($result as $id => $programs) 
        {
            echo $colletor->getNameById($id)."<br />";
            foreach ($programs as $program)
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
