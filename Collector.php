<?php

class Collector
{
    //const CHANNELS_XML = "channels_test.xml";
    const CHANNELS_XML = "channels.xml";
    private $_file_path = null;
    public static function getInstance()
    {
        if(!self::$instance_ instanceof  self)
        {
            self::$instance_ = new self();
        }
        return self::$instance_;
    }

    /*
     * return: array["channel_name"] = channel_url
     * eg. array["cctv1"] = "http://xxx";
     */
    public function getChannelUrls()
    {
        $xml = simplexml_load_file($this->_file_path);
        foreach ($xml->channel as $channel)
        {
            $name = $channel["name"];
            $url = $channel->url;
            $array["$name"] = $url;
        }
        return $array;
    }
 
    private static $instance_;
    private function __construct()
    {
        $this->_file_path = dirname(__FILE__).'/'."xml/".self::CHANNELS_XML;
    }
    private function __clone() { }
}

?>
