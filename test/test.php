<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Collector.php';
    require_once dirname(__FILE__).'/'.'../Database.php';
    require_once dirname(__FILE__).'/'.'../CacheLock.php';

    $version1 = "1.0.x";
    $version2 = "1.1.0";
    
    if ($version1 < $version2)
    {
        echo "$version1 < $version2"."<br />";
    }
    else
    {
        echo "$version1 > $version2"."<br />";
    }
        
?>
