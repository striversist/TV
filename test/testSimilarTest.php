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

?>
