<?php

class Database
{
    const DB_FILE = "tv_programs.txt";
    private $_file_path;
    public static function getInstance()
    {
        if(!self::$instance_ instanceof  self)
        {
            self::$instance_ = new self();
        }
        return self::$instance_;
    }
    
    public function store($channels)
    {
        $store = serialize($channels);
        //$file = fopen(self::DB_FILE, "w+") or exit("Unable to open file ".  self::DB_FILE);
        //fwrite($file, $store);
        //fclose($file);
        file_put_contents($this->_file_path, $store);
    }
    
    public function getChannels()
    {
        $string = file_get_contents($this->_file_path);
        $channels = unserialize($string);
        return $channels;
    }

    private static $instance_;
    private function __construct() 
    { 
        $this->_file_path = dirname(__FILE__).'/'.self::DB_FILE;
    }
    private function __clone() {}
}

?>
