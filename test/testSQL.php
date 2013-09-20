<?php

    $con = mysql_connect("localhost","test","test");
    if (!$con)
    {
        die('Could not connect: ' . mysql_error());
    }
    
    mysql_select_db("test", $con);
    
    $result = mysql_query("SELECT * FROM profiles");
    $num = mysql_num_rows($result);
    echo "Found records: ".$num."<br />";
    while($row = mysql_fetch_array($result))
    {
        echo "GUID=".$row['GUID'] ."<br />";
        echo "INFO=".$row['INFO']."<br />";
        $profile = unserialize($row['INFO']);
        var_dump($profile);
    }
    
//    $profile = array();
//    $profile["GUID"] = "BF976871-AE67-A1F7-37EA-F159D56158E5";
//    $profile["FirstUse"] = "2013/07/08 22:03:16";
//    $store = serialize($profile);
//    echo "store=".$store."<br />";
//    mysql_query("INSERT INTO profiles (GUID, INFO) VALUES ('BF976871-AE67-A1F7-37EA-F159D56158E5', '$store')");
    
    mysql_close($con);
?>
