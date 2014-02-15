<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Database.php';
    
    $db = Database::getInstance();
    $searches = $db->getSearchWordsInDays(3);
    $high = getHighFrequencyWords($searches, 6);
    var_dump($high);
        
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
                if (!in_array($words[$i], $result))
                    $result[] = $words[$i];
            }
        }
        
        return $result;
    }

?>
