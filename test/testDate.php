<?php
    echo date("Y/m/d")."<br />";
    echo date("l")."<br />";
    echo date("w")."<br />";
    echo date("H:i:s")."<br />";
    
    echo date("Y/m/d H:i:s")."<br />";
    
    $today = date("Y/m/d");
    $yestoday = date("Y/m/d",strtotime("-1 day"));
    echo "today=$today, yestoday=".$yestoday."<br/>";
    
    $date = date("Y/m/d", strtotime("2014/02/17"));
    echo "$date"."<br/>";
?>
