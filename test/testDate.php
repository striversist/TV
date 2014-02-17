<?php
    echo date("Y/m/d")."<br />";
    echo date("l")."<br />";
    echo date("w")."<br />";
    echo date("H:i:s")."<br />";
    
    echo date("Y/m/d H:i:s")."<br />";
    
    $today = date("Y/m/d");
    echo "today=$today, yestoday=".(date("Y/m/d",strtotime("-1 day")))."<br/>";
?>
