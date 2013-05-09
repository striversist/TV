<?php
    
    $return["result"] = array("channels" => array("cctv1", "cctv2"));
    echo json_encode($return);
    
    $test = '{"channels":["cctv1","cctv2"]}';
    $channels = json_decode($test);
    var_dump($channels);
?>