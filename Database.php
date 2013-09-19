<?php

class Database
{
    const DB_CHANNELS_FILE = "tv_programs.txt";
    const DB_PROFILES = "profiles.txt";
    const DB_HOT_INFO = "hot_info.txt";
    private $channels_file_path_;
    private $profiles_file_path_;
    private $hot_info_file_path_;
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
    }
    
    public function getChannels()
    {
        $string = file_get_contents($this->channels_file_path_);
        $channels = unserialize($string);
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
     * profile: key: GUID; value: first_use, favorite array, search_words array
     */
    public function storeProfiles($profiles)
    {
        // Way I: use file to store
//        $store = serialize($profiles);
//        file_put_contents($this->profiles_file_path_, $store);
        
        // Way II: use MySQL
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
     * profile: key: GUID; value: first_use, favorite array, search_words array
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
        // Way I: use file to store
//        if (!file_exists($this->profiles_file_path_))
//            return false;
//        $string = file_get_contents($this->profiles_file_path_);
//        $profiles = unserialize($string);
        
        // Way II: use MySQL
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

    private static $instance_;
    private function __construct() 
    { 
        $this->channels_file_path_ = dirname(__FILE__).'/store/'.self::DB_CHANNELS_FILE;
        $this->profiles_file_path_ = dirname(__FILE__).'/store/'.self::DB_PROFILES;
        $this->hot_info_file_path_  = dirname(__FILE__).'/store/'.self::DB_HOT_INFO;
        $con = mysql_pconnect("localhost", "test", "test");     // mysql_pconnect() 函数打开一个到 MySQL 服务器的持久连接
        if (!$con)
            die('Could not connect: ' . mysql_error());
        mysql_select_db("test", $con);
    }
    private function __clone() {}
}

?>
