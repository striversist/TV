<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'./utils.php';
    require_once dirname(__FILE__).'/'.'../../Collector.php';
    require_once dirname(__FILE__).'/'.'../../Database.php';
    
    $colletor = Collector::getInstance();
    
    $result = array();
    if (isset($_GET["nowtime"]))
    {
        $result["nowtime"] = date("H:i:s");
    }
    else if (isset ($_GET["all_tvmao_id"]))
    {
        $all_tvmao_id = $colletor->getAllTvmaoIds();
        $id_array = array();
        foreach ($all_tvmao_id as $channel_id => $tvmao_id) 
        {
            $tmp = array();
            $tmp["id"] = $channel_id;
            $tmp["tvmao_id"] = $tvmao_id;
            $id_array[] = $tmp;
        }
        $result["all_tvmao_id"] = $id_array;
    }
    else if (isset ($_GET["pop_search"]))
    {
        $num = intval($_GET["pop_search"]);
        $cache = getFromMemcache("pop_search_".$num);
        if ($cache)
        {
            $result["pop_search"] = $cache;
        }
        else
        {
            $searches = Database::getInstance()->getSearchWordsInDays($num);
            $high_words = getHighFrequencyWords($searches, 20);
            foreach ($high_words as $keyword) 
            {
//                echo "$keyword is availabel? => ".isAvailableSearchWord($keyword)."<br/>";
                if (isAvailableSearchWord($keyword))
                {
                    $result["pop_search"][] = $keyword;
                    if (count($result["pop_search"]) >= $num)
                        break;
                }
            }
//            var_dump($result["pop_search"]);
            sendToMemcache("pop_search_".$num, $result["pop_search"], 43200);
        }
    }
    
    echo json_encode($result);
    
    // ------------------------- Functions ---------------------------------
    function sendToMemcache($key, $target, $expire)
    {
        assert($target != null);
        Database::getInstance()->getMemcacheInstance()->set($key, $target, MEMCACHE_COMPRESSED, $expire);
    }
    
    function getFromMemcache($key)
    {
        return Database::getInstance()->getMemcacheInstance()->get($key);
    }
?>
