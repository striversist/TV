<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'./Database.php';
    
    if(!isset($_POST["feedback"]) || htmlspecialchars($_POST["feedback"]) === '')
    {
        return;
    }
    
    $db = Database::getInstance();
    $profiles = $db->getProfiles();
    $headers = apache_request_headers();
    $guid = $headers["GUID"];
    $profile = $profiles[$guid];
    $feedback = $_POST["feedback"];
    $date = date("Y/m/d H:i:s");
    
    if (!isset($profile["feedbacks"]))
    {
        $feedbacks["$date"] = $feedback;
        $profile["feedbacks"] = $feedbacks;
    }
    else
    {
        $feedbacks = $profile["feedbacks"];
        $feedbacks["$date"] = $feedback;
        $profile["feedbacks"] = $feedbacks;
    }
    $profiles[$guid] = $profile;
    $db->storeProfiles($profiles);
    
//    var_dump($profiles[$guid]);
?>
