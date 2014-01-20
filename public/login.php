<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Config.php';
    require_once dirname(__FILE__).'/'.'../Database.php';
    require_once dirname(__FILE__).'/'.'../CacheLock.php';
    require_once dirname(__FILE__).'/'.'../app/utils.php';
    
    // Detect GUID header, is not set, generate a GUID header
    $headers = apache_request_headers();
    $db = Database::getInstance();
    $profile = array();
    $is_new_user = false;
    $last_login = false;
    if (!isset($headers["GUID"]))
    {
        $guid = guid();
        header("GUID: ".$guid);
        $profile["GUID"] = $guid;
        $profile["FirstUse"] = date("Y/m/d H:i:s");
        $is_new_user = true;    // 新用户的判断标准：分配新的GUID
    }
    else
    {
        $guid = $headers["GUID"];
        $profile = $db->getProfile($guid);
        if ($profile == false)
            return;
    }
    
    // 返回配置
    $configure = array();
    $configure["config"]["channel_detail_from_web"] = "0";
    if (Config::$ChannelDetailFromWeb)
        $configure["config"]["channel_detail_from_web"] = "1";
    echo json_encode($configure);
    
    // ---------------------- 后台处理 --------------------------
    // 用户登陆记录处理
    // 针对老用户
    if (!$is_new_user)
    {
        if (isset($profile["LastLogin"]))
            $last_login = $profile["LastLogin"];
        else
            $last_login = false;
    }
    
    $date = date("Y/m/d H:i:s");
    $profile["LastLogin"] = $date;
    
    @$version = $headers["Version"];
    @$user_agent = $headers["UA"];  
    @$user_ip = $_GET["UIP"];
    @$user_location = $_GET["UL"];
    @$app_channel = $_GET["APP_CHANNEL"];
    if ($version != null)
        $profile["Version"] = $version;
    if ($user_agent != null)
        $profile["UA"] = @$user_agent;
    if ($user_ip != null)
        $profile["UIP"] = $user_ip;
    if ($user_location != null)
        $profile["UL"] = $user_location;
    if ($app_channel != null)
        $profile["APP_CHANNEL"] = $app_channel;
    
    $profile["RemoteIP"] = get_remote_ip();
    $db->storeProfile($profile);
    
    // 记录新用户、日活等信息
    $today = date("Y/m/d");
    $new_users_added = 0;
    $loyal_users_added = 0;
    if ($is_new_user)
    {
        $new_users_added = 1;
    }
    else if ($last_login != false)
    {
        $last_login = date("Y/m/d", strtotime($last_login));
        if ($last_login != $today)
            $loyal_users_added = 1;
    }
    
//    echo "new_users_added=".$new_users_added."<br />";
//    echo "loyal_users_added=".$loyal_users_added."<br />";
    if ($new_users_added || $loyal_users_added)
    {
        $lock = new CacheLock("login_records");
        $lock->lock();
        $daily_records = $db->getLoginRecordByDate($today);
//        var_dump($daily_records);
        if ($daily_records == false)
        {
            $daily_records = array();
            $daily_records["Date"] = "$today";
            $daily_records["NewUsers"] = array();
            $daily_records["LoyalUsers"] = array();
        }
        if ($new_users_added)
            $daily_records["NewUsers"][] = $guid;
        else
            $daily_records["LoyalUsers"][] = $guid;

        $db->storeLoginRecord($daily_records);
        $lock->unlock();
    }
    
    // ------------------------ Functions --------------------------------------
    function get_remote_ip() 
    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) 
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) 
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        else if(!empty($_SERVER["REMOTE_ADDR"])) 
            $cip = $_SERVER["REMOTE_ADDR"];
        else 
            $cip = '';

        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);
        return $cip;
    }

?>
