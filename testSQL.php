<?php

    $con = mysql_connect("localhost","test","test");
    if (!$con)
    {
        die('Could not connect: ' . mysql_error());
    }
    
    mysql_select_db("test", $con);
    
    $result = mysql_query("SELECT * FROM profiles");
    while($row = mysql_fetch_array($result))
    {
        echo "ID=".$row['ID'] ."<br />";
        echo "INFO=".$row['INFO']."<br />";
        $profile = unserialize($row['INFO']);
        var_dump($profile);
    }
    
//    $profile = array();
//    $profile["ID"] = "BF976871-AE67-A1F7-37EA-F159D56158E5";
//    $profile["first_use"] = "2013/07/08 22:03:16";
//    $store = serialize($profile);
//    echo "store=".$store."<br />";
//    mysql_query("INSERT INTO profiles (ID, INFO) VALUES ('BF976871-AE67-A1F7-37EA-F159D56158E5', '$store')");
    
    mysql_close($con);
?>
