<?php
    var_dump($_POST);

    if(!isset($_POST["channels"]) || htmlspecialchars($_POST["channels"]) === '')
    {
        return;
    }
    
    //$channels_json = htmlspecialchars($_POST["channels"]);
    $channels_json = $_POST["channels"];
    echo "POST channels = $channels_json"."<br />";
    var_dump($channels_json);
    
    //$channels_json = '{"channels":["cctv1","cctv2"]}';
    //var_dump($channels_json);
    
    $channels = json_decode("$channels_json");
    var_dump($channels);
    
?>