<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'./app/utils.php';
    
    $url = "http://localhost/projects/TV/test.html";
    $html = file_get_contents($url);
    //$pos = strpos($html, "甄嬛传");
    $pos = strpos($html, "\x53\x71");
    var_dump($pos);
    
    $html = gb2312_to_utf8($html);;
    echo $html;
    
    $pos = strpos($html, "甄");
    var_dump($pos)
?>