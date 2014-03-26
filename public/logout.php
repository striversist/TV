<?php
    header("Content-type: text/html; charset=utf8");
    require_once dirname(__FILE__).'/'.'../Database.php';
    require_once dirname(__FILE__).'/'.'../CacheLock.php';
       
    $db = Database::getInstance();
    
    if (isset($_GET["init_uninstall_db"]))  // 将卸载记录写入database
    {
        $profiles = $db->getProfiles();
        $result = array();
        foreach ($profiles as $key => $profile) 
        {
            if (isset($profile["Uninstall"]))
            {
                if ($profile["Uninstall"] === true) // 兼容之前只设置为true的情况
                    $uninstall_date = date("Y/m/d", strtotime($profile["LastLogin"]));
                else
                    $uninstall_date = date("Y/m/d", strtotime($profile["Uninstall"]));
                
                $first_login = date("Y/m/d", strtotime($profile["FirstUse"]));
                if ($uninstall_date == $first_login)
                    $result["$uninstall_date"]["NewUsers"][] = $key;
                else
                    $result["$uninstall_date"]["LoyalUsers"][] = $key;
            }
        }
        
        ksort($result); // 日期从小到大的排列
        foreach ($result as $date => $guids) 
        {
            echo "Date: ".$date."<br/>";
            if (isset($guids["NewUsers"]))
            {
                foreach ($guids["NewUsers"] as $guid)
                {
                    echo "NewUsers:  $guid"."<br/>";
                }
            }
            if (isset($guids["LoyalUsers"]))
            {
                foreach (@$guids["LoyalUsers"] as $guid)
                {
                    echo "LoyalUsers: $guid"."<br/>";
                }
            }
            echo "<br/>";
            
            $uninstall_date = array();
            $uninstall_record["Date"] = $date;
            $uninstall_record["NewUsers"] = isset($guids["NewUsers"]) ? $guids["NewUsers"] : array();
            $uninstall_record["LoyalUsers"] = isset($guids["LoyalUsers"]) ? $guids["LoyalUsers"] : array();
            $db->storeUninstallRecord($uninstall_record);
        }
        
        return;
    }
    
    $headers = apache_request_headers();
    $guid = null;
    if (isset($headers["GUID"]))     // 老版本放入http header中
    {
        $guid = $headers["GUID"];
    }
    else if (isset($_GET["guid"]))   // 新版本放入url请求中
    {
        $guid = $_GET["guid"];
    }
    if ($guid == null)
        return;
    
    $profile = $db->getProfile($guid);
    if ($profile == false)
        return;
    
    if (isset($_GET["uninstall"]))
    {
        $profile["Uninstall"] = date("Y/m/d H:i:s");
        $lock = new CacheLock("uninstall_records");
        $lock->lock();
        $today = date("Y/m/d");
        $uninstall_record = $db->getUninstallRecordByDate($today);
        if ($uninstall_record == false)
        {
            $uninstall_record = array();
            $uninstall_record["Date"] = "$today";
            $uninstall_record["NewUsers"] = array();
            $uninstall_record["LoyalUsers"] = array();
        }
        
        $first_login = date("Y/m/d", strtotime($profile["FirstUse"]));
        if ($first_login == $today)
        {
            if (!in_array($guid, $uninstall_record["NewUsers"]))
                $uninstall_record["NewUsers"][] = $guid;
        }
        else
        {
            if (!in_array($guid, $uninstall_record["LoyalUsers"]))
                $uninstall_record["LoyalUsers"][] = $guid;
        }
        $db->storeUninstallRecord($uninstall_record);
        $lock->unlock();
        
        $db->storeProfile($profile);
    }
   
?>
