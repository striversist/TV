<?php

class Database
{
    const DB_CHANNELS_FILE = "tv_programs.txt";
    const DB_PROFILES = "profiles.txt";
    private $channels_file_path_;
    private $profiles_file_path_;
    public static function getInstance()
    {
        if(!self::$instance_ instanceof  self)
        {
            self::$instance_ = new self();
        }
        return self::$instance_;
    }
    
    /*
     * channels[id][date] = programs
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
        $con = mysql_connect("localhost", "test", "test");
        if (!$con)
            die('Could not connect: ' . mysql_error());
        mysql_select_db("test", $con);
    }
    private function __clone() {}
}

?>
