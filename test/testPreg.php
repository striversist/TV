<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Collector.php';
    require_once dirname(__FILE__).'/'.'../Database.php';
    require_once dirname(__FILE__).'/'.'../CacheLock.php';
        
    $url = "http://m.tvmao.com/program/CCTV-CCTV1-w";
    $pattern = '/.+\/(.+)-w.*/';
    preg_match($pattern, $url, $matches);
    if (key_exists(1, $matches))
        echo "filter id=$matches[1]"."<br/>";
?>