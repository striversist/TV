<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'./utils.php';
    
    $result = array();
    if (isset($_GET["nowtime"]))
    {
        $result["nowtime"] = date("H:i:s");
    }
    
    
    echo json_encode($result);
    
    // ------------------------- Functions ---------------------------------
?>
