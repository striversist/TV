<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Collector.php';
    require_once dirname(__FILE__).'/'.'../Database.php';
    require_once dirname(__FILE__).'/'.'../ProgramFilter.php';
    require_once dirname(__FILE__).'/'.'./utils.php';

    echo "start collecting..."."<br />";
    $t = getTime();
    
    define('MAX_CONCURRENT_TASKS', '30');
    
    $colletor = Collector::getInstance();
    $filter = ProgramFilterFactory::createProgramFilter();
    $db = Database::getInstance();
    
    //$map = $colletor->getIdUrls();
    $channels = array();
    for($day = 1; $day <= 7; $day++)
    {
        $map = $colletor->getIdUrlsByDay($day);
        $num = 0;
        foreach ($map as $id => $url)
        {
            $num++;
            $urls["$id"] = $url;
            if (count($urls) === MAX_CONCURRENT_TASKS or $num === count($map))
            {
                $htmls = get_urls_content($urls);
                if (count($htmls))
                {
                    foreach ($htmls as $id => $html) 
                    {
                        echo "collecting $id day=$day url=$urls[$id]"."<br />";
                        if (empty($html))   // The get_urls_contents fail to get the url
                        {
                            $html = file_get_contents($url);
                        }
                        if (get_html_charset($html) === "gb2312")
                        {
                            $html = gb2312_to_utf8($html);
                        }
                        $dom = str_get_html($html);
                        $channels["$id"]["$day"] = $filter->getProgramList($dom);
                    }
                }
                unset($urls);
            }
        }
        //dump($channels);
    }
    //var_dump($channels);
    $db->storeChannels($channels);
    
    echo "collect finished..."."<br />";
    runTime($t);
    
    function get_urls_content($urls)
    {
        $mh = curl_multi_init();  
        foreach ($urls as $i => $url) 
        {
            $conn[$i] = curl_init($url);
            curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, 1);  // 设置返回do.php页面输出内容
            curl_setopt($conn[$i], CURLOPT_TIMEOUT, 5);
            curl_multi_add_handle ($mh, $conn[$i]);              // 添加线程
        }
        
        #----------------执行线程----------------  
        $active = null;
        do { $n = curl_multi_exec($mh,$active); usleep(1000);} while ($active); // 网上说些方法有时会让CPU达100%
        sleep(1);
        /*
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($mh) != -1) {
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }
         * 
         */
        #-----------------------------------------
        $res = array();
        foreach ($urls as $i => $url)
        {
            $res[$i] = curl_multi_getcontent($conn[$i]);    // 得到页面输入内容
            curl_multi_remove_handle($mh, $conn[$i]);
            curl_close($conn[$i]);
            usleep(10000);
        }
        curl_multi_close($mh);
        return $res;
    }
    
    #----------- calculate time function-------------  
    function getTime()
    {
        $time = explode(" ",microtime());
        $time = $time[1].substr($time[0],1);
        return $time;
    }
    
    function runTime($t,$l=3)
    {
        $dif = getTime()-$t;
        echo ' '.number_format($dif,$l);
    }
    
    function dump($channels)
    {
        foreach ($channels as $channel => $list)
        {
            echo $channel.": <br />";
            foreach ($list as $program)
            {
                echo $program["time"].": ".$program["title"]."<br />";
            }
        }
    }
    
?>
