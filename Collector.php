<?php

class Collector
{
    const CHANNELS_XML = "channels_test.xml";
    //const CHANNELS_XML = "channels.xml";
    private $_file_path = null;
    public static function getInstance()
    {
        if(!self::$instance_ instanceof  self)
        {
            self::$instance_ = new self();
        }
        return self::$instance_;
    }

    /* @Deprecated
     * return: array["channel_name"] = channel_url
     * eg. array["cctv1"] = "http://xxx";
     */
    public function getIdUrls()
    {
        $xml = simplexml_load_file($this->_file_path);
        foreach ($xml->channel as $channel)
        {
            $id = $channel["id"];
            $array["$id"] = (string)($channel->url);  // SimpleXMLElement object to string
        }
        return $array;
    }
    
    /*
     * param: $day: 1-7 means Monday to Sunday of this week
     * return: array["channel_name"] = channel_url
     * eg. array["cctv1"] = "http://xxx";
     */
    public function getIdUrlsByDay($day)
    {
        if($day < 0 || $day > 7)
        {
            echo "day $day out limit";
            return null;
        }
        $xml = simplexml_load_file($this->_file_path);
        foreach ($xml->channel as $channel)
        {
            $id = $channel["id"];
            $array["$id"] = (string)($channel->urls->url[$day - 1]);
        }
        return $array;
    }
    
    public function getIdNames()
    {
        $xml = simplexml_load_file($this->_file_path);
        foreach ($xml->channel as $channel)
        {
            $id = $channel["id"];
            $array["$id"] = (string)($channel->name);  // SimpleXMLElement object to string
        }
        return $array;
    }
    
    public function getNameById($id)
    {
        $xml = simplexml_load_file($this->_file_path);
        foreach ($xml->channel as $channel)
        {
            if($id == $channel["id"])
            {
                return (string)($channel->name);  // SimpleXMLElement object to string
            }
        }
        return null;
    }

    private static $instance_;
    private function __construct()
    {
        $this->_file_path = dirname(__FILE__).'/'."xml/".self::CHANNELS_XML;
    }
    private function __clone() { }
}

?>
