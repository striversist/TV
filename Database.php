<?php
require_once dirname(__FILE__).'/'.'./Collector.php';

class Database
{
    const DB_CHANNELS_FILE = "tv_programs.txt";
    const DB_PROFILES = "profiles.txt";
    const DB_HOT_INFO = "hot_info.txt";
    const DB_CHANNEL_VISIT_RECORD = "channel_visit_records.txt";
    
    private $channels_file_path_;
    private $hot_info_file_path_;
    private $channels_visit_records_file_path_;
    private $memcache_;
    const MEMCACHE_EXPIRE_TIME = 86400;      // 24 hour
    public static function getInstance()
    {
        if(!self::$instance_ instanceof  self)
        {
            self::$instance_ = new self();
        }
        return self::$instance_;
    }
    
    /*
     * channels[id][date] == programs
     */
    public function storeChannels($channels)
    {
        $store = serialize($channels);
        file_put_contents($this->channels_file_path_, $store);
        
        $this->memcache_->flush();
        $this->prepareMemChannels($channels);
    }
    
    public function getChannels()
    {
        $mem_channels = $this->memcache_->get("channels");
        if ($mem_channels != FALSE)
            return $mem_channels;
        
        $string = file_get_contents($this->channels_file_path_);
        $channels = unserialize($string);
        $this->prepareMemChannels($channels);
        return $channels;
    }
    
    public function getChannelById($id)
    {
        $mem_channel = $this->memcache_->get("channel_".$id);
        if ($mem_channel != FALSE)
            return $mem_channel;
        
        $channels = $this->getChannels();
        if (!isset($channels["$id"]))
            return false;
        $channel = $channels["$id"];
        $this->prepareMemChannels($channels);
        return $channel;
    }
    
    public function getChannelsByCategory($param_category_id)
    {
//        echo "getChannelsByCategory $param_category_id"."<br />";
        $mem_channels = $this->memcache_->get("channels_".$param_category_id);
        if ($mem_channels != FALSE)
            return $mem_channels;
        
        $collector = Collector::getInstance();
        $root_categories = $collector->getRootCategories();
        $local_categories = $collector->getSubCategories("local");
        if (!array_key_exists($param_category_id, $root_categories) 
                and !array_key_exists($param_category_id, $local_categories))
            return false;
        
        $categories = array();
        $channels = $this->getChannels();
        foreach ($channels as $channel_id => $channel)
        {
            foreach ($root_categories as $category_id => $category)
            {
                if (array_key_exists($category_id, $channel["categories"]))
                {
//                    echo "$channel_id belongs to $category_id"."<br />";
                    $categories["$category_id"][$channel_id] = $channel;
                }
            }
            foreach ($local_categories as $category_id => $category)
            {
                if (array_key_exists($category_id, $channel["categories"]))
                {
//                    echo "$channel_id belongs to $category_id"."<br />";
                    $categories["$category_id"][$channel_id] = $channel;
                }
            }
        }

        foreach ($categories as $category_id => $category_channels)
        {
//            echo "memecache set $category_id, count(category_channels)=".count($category_channels)."<br />";
            $this->memcache_->set("channels_".$category_id, $category_channels, false, self::MEMCACHE_EXPIRE_TIME) or die ("Failed to save channels to $category_id at the server");
        }
        
        return $categories["$param_category_id"];
    }
    
    /*
     * 优化：将所有的channel信息放入memcache，以备后续需要
     */
    private function prepareMemChannels($channels)
    {
        $this->memcache_->set("channels", $channels, false, self::MEMCACHE_EXPIRE_TIME) or die ("Failed to save data at the memcached server");
        foreach ($channels as $id => $channel)
        {
            $this->memcache_->set("channel_".$id, $channel, false, self::MEMCACHE_EXPIRE_TIME) or die ("Failed to save channel $id data at the server");
        }
    }
    
    /*
     * $hot_info[channel_name][index] == program
     * program has proerty: name
     */
    public function storeHotInfo($hot_info)
    {
        $store = serialize($hot_info);
        file_put_contents($this->hot_info_file_path_, $store);
    }
    
    public function getHotInfo()
    {
        $string = file_get_contents($this->hot_info_file_path_);
        $hot_info = unserialize($string);
        return $hot_info;
    }
    
    /*
     * profiles: array of profile
     * profile: key: GUID; value: FirstUse, favorite array, search_words array
     */
    public function storeProfiles($profiles)
    {
        foreach ($profiles as $guid => $profile)
        {
            $store = serialize($profile);
            $result = mysql_query("SELECT * FROM profiles WHERE GUID='$guid'");
            $num = mysql_num_rows($result);
            if ($num > 0)   // Found exist record, update
                mysql_query("UPDATE profiles SET INFO='$store' WHERE GUID='$guid'");
            else            // Not found exist record, insert
                mysql_query("INSERT INTO profiles (GUID, INFO) VALUES ('$guid', '$store')");
        }
    }
    
    /*
     * profile: key: GUID; value: FirstUse, favorite array, search_words array
     */
    public function storeProfile($profile)
    {
        $store = serialize($profile);
        $guid = $profile["GUID"];
        $result = mysql_query("SELECT * FROM profiles WHERE GUID='$guid'");
        $num = mysql_num_rows($result);
        if ($num > 0)   // Found exist record, update
            mysql_query("UPDATE profiles SET INFO='$store' WHERE GUID='$guid'");
        else            // Not found exist record, insert
            mysql_query("INSERT INTO profiles (GUID, INFO) VALUES ('$guid', '$store')");
    }
    
    public function getProfiles()
    {
        $result = mysql_query("SELECT * FROM profiles");
        $num = mysql_num_rows($result);
        if ($num == 0)
            return false;
        $profiles = array();
        while($row = mysql_fetch_array($result))
        {
            $profile = unserialize($row['INFO']);
            $profiles[$row['GUID']] = $profile;
        }
        
        return $profiles;
    }
    
    public function getProfile($guid)
    {
        $result = mysql_query("SELECT * FROM profiles WHERE GUID='$guid'");
        $num = mysql_num_rows($result);
        if ($num == 0)
            return false;
        $profile = array();
        while($row = mysql_fetch_array($result))
        {
            $profile = unserialize($row['INFO']);
        }
        return $profile;
    }
    
    public function getLoginRecords()
    {
        $result = mysql_query("SELECT * FROM login_records");
        if (mysql_numrows($result) == 0)
            return false;
        
        $records = array();
        while ($row = mysql_fetch_array($result))
        {
            $record["Date"] = $row["DATE"];
            $record["NewUsers"] = unserialize($row["NEW_USERS"]);
            $record["LoyalUsers"] = unserialize($row["LOYAL_USERS"]);
            $records[] = $record;
        }
        return $records;
    }
    
    public function getLoginRecordByDate($date_str)
    {
        if (!$this->is_date($date_str))
            return false;
        
        $result = mysql_query("SELECT * FROM login_records WHERE DATE='$date_str'");
        if (mysql_numrows($result) == 0)
            return false;
        
        while ($row = mysql_fetch_array($result))
        {
            $record["Date"] = $row["DATE"];
            $record["NewUsers"] = unserialize($row["NEW_USERS"]);
            $record["LoyalUsers"] = unserialize($row["LOYAL_USERS"]);
        }
        return $record;
    }
    
    public function storeLoginRecord($record)
    {
        if (!isset($record["Date"]) || !isset($record["NewUsers"]) || !isset($record["LoyalUsers"]))
            return false;
        
        if (!$this->is_date($record["Date"]))
            return false;
        
        $date = $record["Date"];
        $new_users = serialize($record["NewUsers"]);
        $loyal_users = serialize($record["LoyalUsers"]);
        
        $result = mysql_query("SELECT * FROM login_records WHERE DATE='$date'");
        $num = mysql_num_rows($result);
        if ($num > 0)   // Found exist record, update
            mysql_query("UPDATE login_records SET NEW_USERS='$new_users', LOYAL_USERS='$loyal_users' WHERE DATE='$date'");
        else            // Not found exist record, insert
            mysql_query("INSERT INTO login_records (DATE, NEW_USERS, LOYAL_USERS) VALUES ('$date', '$new_users', '$loyal_users')");
    }
    
    private function is_date($str, $format="Y/m/d")
    {
        $unixTime_1 = strtotime($str);
        if (!is_numeric($unixTime_1)) 
            return false;
        $checkDate = date($format, $unixTime_1);
        $unixTime_2 = strtotime($checkDate);
        if($unixTime_1 == $unixTime_2)
            return true;
        else
            return false;
    }
    
    /*
     * return: key{date} => value{key{channel_id} => value{"VisitTimes"}}
     */
    public function getChannelVisitRecords()
    {
        if (!file_exists($this->channels_visit_records_file_path_))
            return false;
        $string = file_get_contents($this->channels_visit_records_file_path_);
        $records = unserialize($string);
        return $records;
    }
    
    /*
     * 记录每天各channel被访问的次数（查节目）
     */
    public function storeChannelVisitRecords($records)
    {
        $store = serialize($records);
        file_put_contents($this->channels_visit_records_file_path_, $store, LOCK_EX);
    }
    
    private static $instance_;
    private function __construct() 
    { 
        $this->channels_file_path_ = dirname(__FILE__).'/store/'.self::DB_CHANNELS_FILE;
        $this->hot_info_file_path_  = dirname(__FILE__).'/store/'.self::DB_HOT_INFO;
        $this->channels_visit_records_file_path_ = dirname(__FILE__).'/store/'.self::DB_CHANNEL_VISIT_RECORD;
//        $con = mysql_pconnect("localhost", "test", "test") or die('Could not connect: ' . mysql_error());     // mysql_pconnect() 函数打开一个到 MySQL 服务器的持久连接
//        mysql_select_db("test", $con);
        $con = mysql_pconnect("localhost", "tv_guide", "M2m3EDw4sZEzUGya") or die('Could not connect: ' . mysql_error());     // mysql_pconnect() 函数打开一个到 MySQL 服务器的持久连接
        mysql_select_db("tv_guide", $con);
        
        $this->memcache_ = new Memcache();
        $this->memcache_->pconnect('localhost', 11211) or die ("Could not connect memcached server");
    }
    private function __clone() {}
}

?>
