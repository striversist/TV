<?php
    header("Content-type: text/html; charset=utf8");
    echo strstr("Hello world!","world")."<br />";
    echo strstr("中文搜索","搜索")."<br />";
    if(!isset($_GET["keyword"]) || htmlspecialchars($_GET["keyword"]) === '')
    {
        echo "You should input search text"."<br />";
        return;
    }
    
    $keyword = htmlspecialchars($_GET["keyword"]);
    echo "You input keyword: ".$keyword."<br />";
    
    $content[] = "中文";
    $content[] = "英文";
    
    foreach ($content as $key => $value)
    {
        $pos = strpos($value, $keyword);
        if($pos !== FALSE)
        {
            echo "You found $keyword"." in $value"."<br />";
        }
    }
    
?>
