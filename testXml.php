<?php
    $filename = "channels.xml";
    $xml = simplexml_load_file($filename);
    foreach ($xml->channel as $channel)
    {
        $name = $channel["name"];
        $url = $channel->url;
        $array["$name"] = $url;
    }
    var_dump($array);
?>
