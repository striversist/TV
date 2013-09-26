<?php

class Database
{
    const DB_CHANNELS_FILE = "tv_programs.txt";
    const DB_PROFILES = "profiles.txt";
    const DB_HOT_INFO = "hot_info.txt";
    const DB_CHANNEL_VISIT_RECORD = "channel_visit_records.txt";
    
    private $channels_file_path_;
    private $hot_info_file_path_;
    private $channels_visit_record_file_path_;
    private $memcache_;
    const MEMCACHE_EXPIRE_TIME = 43200;      // 12 hour
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
        //$file = fopen(self::DB_CHANNELS_FILE, "w+") or exit("Unable to open file ".  self::DB_CHANNELS_FILE);
        //fwrite($file, $store);
        //fclose($file);
        file_put_contents($this->channels_file_path_, $store);
        $this->memcache_->set("channels", $channels, false, self::MEMCACHE_EXPIRE_TIME) or die ("Failed to save data at the memcached server");
    }
    
    public function getChannels()
    {
        $mem_channels = $this->memcache_->get("channels");
        if ($mem_channels != FALSE)
            return $mem_channels;
        
        $string = file_get_contents($this->channels_file_path_);
        $channels = unserialize($string);
        $this->memcache_->set("channels", $channels, false, self::MEMCACHE_EXPIRE_TIME) or die ("Failed to save data at the memcached server");
        return $channels;
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
    
    /*
     * return: key{date} => value{key{channel_id} => value{"VisitTimes"}}
     */
    public function getChannelVisitRecords()
    {
        if (!file_exists($this->channels_visit_record_file_path_))
            return false;
        $string = file_get_contents($this->channels_visit_record_file_path_);
        $records = unserialize($string);
        return $records;
    }
    
    /*
     * 记录每天各channel被访问的次数（查节目）
     */
    public function storeChannelVisitRecords($records)
    {
        $store = serialize($records);
        file_put_contents($this->channels_visit_record_file_path_, $store);
    }

    private static $instance_;
    private function __construct() 
    { 
        $this->channels_file_path_ = dirname(__FILE__).'/store/'.self::DB_CHANNELS_FILE;
        $this->hot_info_file_path_  = dirname(__FILE__).'/store/'.self::DB_HOT_INFO;
        $this->channels_visit_record_file_path_ = dirname(__FILE__).'/store/'.self::DB_CHANNEL_VISIT_RECORD;
        $con = mysql_pconnect("localhost", "test", "test") or die('Could not connect: ' . mysql_error());     // mysql_pconnect() 函数打开一个到 MySQL 服务器的持久连接
        mysql_select_db("test", $con);
        
        $this->memcache_ = new Memcache();
        $this->memcache_->pconnect('localhost', 11211) or die ("Could not connect memcached server");
    }
    private function __clone() {}
}

?>
