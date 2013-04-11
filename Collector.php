<?php

class Collector
{
    const CHANNELS_XML = "channels_test.xml";
    //const CHANNELS_XML = "channels.xml";
    const CHANNEL_CATEGORIES_XML = "categories.xml";
    private $_channels_xml_path = null;
    private $_category_xml_path = null;
    public static function getInstance()
    {
        if(!self::$instance_ instanceof  self)
        {
            self::$instance_ = new self();
        }
        return self::$instance_;
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
        $xml = simplexml_load_file($this->_channels_xml_path);
        foreach ($xml->channel as $channel)
        {
            $id = $channel["id"];
            $array["$id"] = (string)($channel->urls->url[$day - 1]);
        }
        return $array;
    }
    
    public function getIdNames()
    {
        $xml = simplexml_load_file($this->_channels_xml_path);
        foreach ($xml->channel as $channel)
        {
            $id = $channel["id"];
            $array["$id"] = (string)($channel->name);  // SimpleXMLElement object to string
        }
        return $array;
    }
    
    public function getIdNamesByCategory($category)
    {
        $xml = simplexml_load_file($this->_channels_xml_path);
        foreach ($xml->channel as $channel)
        {
            if ($channel->category == $category)
            {
                $id = $channel["id"];
                $array["$id"] = (string)($channel->name);  // SimpleXMLElement object to string
            }
        }
        return $array;
    }
    
    public function getIdCategories()
    {
        $xml = simplexml_load_file($this->_category_xml_path);
        foreach ($xml->category as $category)
        {
            $id = $category["id"];
            $array["$id"] = (string)($category);
        }
        return $array;
    }
    
    public function getNameById($id)
    {
        $xml = simplexml_load_file($this->_channels_xml_path);
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
        $this->_channels_xml_path = dirname(__FILE__).'/'."xml/".self::CHANNELS_XML;
        $this->_category_xml_path = dirname(__FILE__).'/'."xml/".self::CHANNEL_CATEGORIES_XML;
    }
    private function __clone() { }
}

?>
