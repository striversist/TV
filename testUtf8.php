<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'./app/utils.php';
    
    $url = "http://localhost/projects/TV/test.html";
    //$url = "http://localhost/projects/TV/test.txt";
    $html = file_get_contents($url);
    //echo"orign html:"."<br />".$html."<br />";
    $gb_text = "\xD5\xE7\x8B\xD6\xB4\xAB\r";     // gbk/gb2312: 甄嬛传
    $utf8_text = "甄嬛传";
    
    $pos = strpos($html, $gb_text);
    var_dump($pos);
    
    //$html = str_replace($gb_text, "aaaaaaaaa", $html);
    $html = gb2312_to_utf8($html);
    echo $html."<br />";
    
    //echo "<br /><br />";
    //$converted = gb2312_to_utf8($gb_text);
    //echo $converted;
    
    //$pos = strpos($html, $converted);
    //var_dump($pos);
    
    //$html = str_replace($converted, $utf8_text, $html);
    //echo $html."<br />";
    
    echo "strlen(utf8 $utf8_text) = ".strlen($utf8_text)."<br />";
?>