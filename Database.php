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
        $store = serialize($profiles);
        file_put_contents($this->profiles_file_path_, $store);
    }
    
    public function getProfiles()
    {
        if (!file_exists($this->profiles_file_path_))
            return false;
        $string = file_get_contents($this->profiles_file_path_);
        $profiles = unserialize($string);
        return $profiles;
    }

    private static $instance_;
    private function __construct() 
    { 
        $this->channels_file_path_ = dirname(__FILE__).'/store/'.self::DB_CHANNELS_FILE;
        $this->profiles_file_path_ = dirname(__FILE__).'/store/'.self::DB_PROFILES;
    }
    private function __clone() {}
}

?>
