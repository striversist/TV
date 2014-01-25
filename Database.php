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
    const CHANNELS_CHUNK_SIZE = 50;
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
        $this->sendToMemcache("channels", $channels);
    }
    
    public function getChannels()
    {
        $mem_channels = $this->getFromMemcache("channels");
        if ($mem_channels != FALSE)
            return $mem_channels;
        
        $string = file_get_contents($this->channels_file_path_);
        $channels = unserialize($string);
        $this->sendToMemcache("channels", $channels);
        return $channels;
    }
       
    public function getChannelById($id)
    {
//        echo "getChannelById id=$id"."<br/>";
        $mem_channel = $this->getFromMemcache("channel_".$id);
        if ($mem_channel != FALSE)
        {
//            var_dump($mem_channel["categories"]);
            return $mem_channel;
        }
        
        $channels = $this->getChannels();
        if (!isset($channels["$id"]))
            return false;
        $target_channel = $channels["$id"];
        
        foreach ($channels as $id => $channel)
            $this->sendToMemcache("channel_".$id, $channel);
        
//        var_dump($target_channel["categories"]);
        
        return $target_channel;
    }
    
    public function getChannelsByCategory($param_category_id)
    {
//        echo "getChannelsByCategory $param_category_id"."<br />";
        $mem_channels = $this->getFromMemcache("channels_".$param_category_id);
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
            $this->sendToMemcache("channels_".$category_id, $category_channels);
        }
        
        if (array_key_exists($param_category_id, $categories))
            return $categories["$param_category_id"];
        else
            return false;
    }
    
    private function sendToMemcache($key, $target_array)
    {
//        echo "sendToMemcache key=$key, count(target_array)=".count($target_array)."<br/>";
        assert($target_array != null);
        $chunks = array_chunk($target_array, self::CHANNELS_CHUNK_SIZE, true);
        if ($this->memcache_->set($key."_chunk_count", count($chunks), false, self::MEMCACHE_EXPIRE_TIME) == FALSE)
            return;
        for ($i=0; $i<count($chunks); $i++)
        {
            $this->memcache_->set($key."_chunk_".$i, $chunks[$i], MEMCACHE_COMPRESSED, self::MEMCACHE_EXPIRE_TIME);
        }
    }
    
    private function getFromMemcache($key)
    {
        $chunk_count = $this->memcache_->get($key."_chunk_count");
        if ($chunk_count == FALSE)
        {
//            echo "getFromMemcache key=$key, Miss"."<br/>";
            return FALSE;
        }
        
        $target_array = array();
        for ($i=0; $i<$chunk_count; $i++)
        {
            $channels_chunk = $this->memcache_->get($key."_chunk_".$i);
            if ($channels_chunk == FALSE)
                break;
            $target_array = array_merge($target_array, $channels_chunk);
        }
//        echo "getFromMemcache key=$key, Hit, count(target_array)=".count($target_array)."<br/>";
        return $target_array;
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
    // @deprecated: delete soon
    public function getChannelVisitRecords()
    {
        if ($this->needTransfer())  // Transfer finish
        {
            // Transer first
            $string = file_get_contents($this->channels_visit_records_file_path_);
            $visit_records = unserialize($string);
            $this->transferData($visit_records);
            
            return $this->getAllVisitRecordsFromDatabase();
        }
        else
        {
            return $this->getAllVisitRecordsFromDatabase();
        }
        
        if (!file_exists($this->channels_visit_records_file_path_))
            return false;
        $string = file_get_contents($this->channels_visit_records_file_path_);
        $records = unserialize($string);
        return $records;
    }
    
    // @deprecated: deleted soon
    private function getAllVisitRecordsFromDatabase()
    {
        // Get from database
        $result = mysql_query("SELECT * FROM channel_visit_records");
        if (mysql_numrows($result) == 0)
            return false;

        $records = array();
        while ($row = mysql_fetch_array($result))
        {
            $record["Date"] = $row["DATE"];
            $record["VisitRecord"] = unserialize($row["VISIT_RECORD"]);
//            echo "getChannelVisitRecords date=".$record["Date"].", count=".count($record["VisitRecord"])."<br/>";
            // TODO: 暂时转换为外面所需的格式，以后直接输出即可
            $records[$record["Date"]] = $record["VisitRecord"];
        }
        return $records;
    }
    
    /*
     * 记录每天各channel被访问的次数（查节目）
     */
    // @deprecated: delete soon
    public function storeChannelVisitRecords($records)
    {
        if (!$this->needTransfer())      // Transfer finish
        {
            $today = date("Y/m/d");
            $record["Date"] = "$today";
            $record["VisitRecord"] = $records["$today"];
            // TODO: 目前只更新今天的节目信息，以后删除这个接口
            $this->storeChannelVisitRecord($record);
            return;
        }
        
        $store = serialize($records);
        file_put_contents($this->channels_visit_records_file_path_, $store, LOCK_EX);
    }
    
    public function needTransfer()
    {
        $today = date("Y/m/d");
        if ($this->getChannelVisitRecordByDate($today) != false)    // Already exist
            return false;   // No need
        return true;
    }
    
    public function transferData($visit_records)
    {        
        foreach ($visit_records as $date => $visit_record) 
        {
//            echo "transferData date=$date, count=".count($visit_record)."<br/>";
            $record = array();
            $record["Date"] = $date;
            $record["VisitRecord"] = $visit_record;
            $this->storeChannelVisitRecord($record);
        }
        return true;
    }
    
    public function getChannelVisitRecordByDate($date)
    {
        if (!$this->is_date($date))
            return false;
        
        $result = mysql_query("SELECT * FROM channel_visit_records WHERE DATE='$date'");
        if (mysql_numrows($result) == 0)
            return false;
        
        while ($row = mysql_fetch_array($result))
        {
            $record["Date"] = $row["DATE"];
            $record["VisitRecord"] = unserialize($row["VISIT_RECORD"]);
        }
//        echo "getChannelVisitRecordByDate:"."<br/>";
//        var_dump($record);
        return $record;
    }
    
    public function storeChannelVisitRecord($record)
    {
        if (!isset($record["Date"]) || !isset($record["VisitRecord"]))
            return false;
        
        if (!$this->is_date($record["Date"]))
            return false;
        
        $date = $record["Date"];
        $visit_record = serialize($record["VisitRecord"]);
//        echo "storeChannelVisitRecord date=$date, count=".count($record["VisitRecord"])."<br/>";
//        var_dump($record["VisitRecord"]);
        
        $result = mysql_query("SELECT * FROM channel_visit_records WHERE DATE='$date'");
        $num = mysql_num_rows($result);
        if ($num > 0)   // Found exist record, update
            mysql_query("UPDATE channel_visit_records SET DATE='$date', VISIT_RECORD='$visit_record' WHERE DATE='$date'");
        else            // Not found exist record, insert
            mysql_query("INSERT INTO channel_visit_records (DATE, VISIT_RECORD) VALUES ('$date', '$visit_record')");
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
