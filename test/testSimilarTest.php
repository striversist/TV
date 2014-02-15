<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Database.php';
    require_once dirname(__FILE__).'/'.'../public/json/utils.php';
    
    $db = Database::getInstance();
    $searches = $db->getSearchWordsInDays(8);
    $high_words = getHighFrequencyWords($searches, 20);
    var_dump($high_words);
   
    foreach ($high_words as $keyword) 
    {
        echo "$keyword is availabel? => ".isAvailableSearchWord($keyword)."<br/>";
    }
        
    // ------------------ Functions ------------------------
    function getHighFrequencyWords($array, $num)
    {
        $similarThreshold = 90;
        $hits = array();
        $result = array();
        foreach ($array as $target)
        {
            $hit = 0;
            foreach ($array as $word)
            {
                $percent = 0;
                similar_text($target, $word, $percent);
//                echo "target=$target, word=$word, percent=$percent"."<br/>";
                if ($percent > $similarThreshold)
                    $hit++;
            }
            $hits[$hit][] = $target;
//            echo "target=$target, hit=$hit"."<br/>";
        }
        krsort($hits);  // 倒序重排: 频率从高到低
        foreach ($hits as $hit => $words)   // 按照频率从高到低的顺序
        {
//            echo "hit=$hit, count=".count($words)."<br/>";
            for ($i=0; $i<count($words); $i++)
            {
                if (count($result) >= $num)
                    break;
                if (!in_arrayi($words[$i], $result))
                    $result[] = $words[$i];
            }
        }
        
        return $result;
    }
    
    function isAvailableSearchWord($keyword)
    {
        $channels = getMatchedChannels($keyword);
        if (count($channels) > 0)
            return true;
        
        $today = date("w") == "0" ? "7" : date("w");
        $search_categories = getRootSearchCategories();
        $programs = getMatchedPrograms($keyword, $search_categories, $today);
        if (count($programs) > 0)
            return true;
        
        return false;
    }
    
    function getRootSearchCategories()
    {
        $collector = Collector::getInstance();
        // 不想被搜索的分类：local，直接搜索耗时大，且其它省份无法看
        $exclude_root_categories = array("local");
        
        $root_categories = $collector->getRootCategories();
        $result = array();
        foreach ($root_categories as $category_id => $category) 
        {
            if (!in_array($category_id, $exclude_root_categories))
                $result[] = $category_id;
        }
        return $result;
    }
    
    // case-insensitive in_array
    function in_arrayi($needle, $haystack) 
    {
        return in_array(strtolower($needle), array_map('strtolower', $haystack));
    }

?>
