<?php
    header("Content-type: text/html; charset=utf8");
    $filename = "xml/channels_test.xml";
    $xml = simplexml_load_file($filename);
    if (!isset($xml->my_channel))
    {
        echo "not exist node my_channel"."<br />";
    }
    foreach ($xml->channel as $channel)
    {
        $name = $channel->name;
        $url = $channel->url;
        $array["$name"] = $url;
    }
    var_dump($array);
?>
