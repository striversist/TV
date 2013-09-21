<?php
    header("Content-type: text/html; charset=utf8");
    
    $memcache = new Memcache();
    $memcache->pconnect('localhost', 11211) or die ("Could not connect");
    $version = $memcache->getVersion();
    echo "Server's version: ".$version."<br/>\n";
    
    $profile["GUID"] = "2F0F4874-BF3C-2258-4E54-3B1D99164B40";
    $profile["FirstUse"] = "2013/09/20 14:35:28";
    $profile["LastLogin"] = "2013/09/20 14:40:51";
    $profile["UA"] = "Mozilla/5.0 (Linux; U; Android 4.0.3; zh-cn; OPPOX907 Build/IML74K) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30";

    // Try to get data, if data not exist or expired, return false
    $get_result = $memcache->get('key');
    echo "Data from the cache:<br/>\n";
    var_dump($get_result);
    
    $result = $memcache->set('key', $profile, false, 10) or die ("Failed to save data at the server");
    echo "Store data in the cache (data will expire in 10 seconds)<br/>\n";
    var_dump($result);
    
    $get_result = $memcache->get('key');
    echo "Data from the cache:<br/>\n";
    var_dump($get_result);
?>
