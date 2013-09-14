<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'./Database.php';
    
    if(!isset($_POST["feedback"]) || htmlspecialchars($_POST["feedback"]) === '')
    {
        return;
    }
    
    $db = Database::getInstance();
    $headers = apache_request_headers();
    if (!isset($headers["GUID"]))
        return;
    $guid = $headers["GUID"];
    $profile = $db->getProfile($guid);
    $feedback = $_POST["feedback"];
    $date = date("Y/m/d H:i:s");
    
    if (!isset($profile["feedbacks"]))
    {
        $feedbacks["$date"] = $feedback;
    }
    else
    {
        $feedbacks = $profile["feedbacks"];
        $feedbacks["$date"] = $feedback;
    }
    $profile["feedbacks"] = $feedbacks;
    $db->storeProfile($profile);
    
//    var_dump($profiles[$guid]);
?>
