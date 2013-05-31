<?php

class Collector
{
    //const CHANNELS_XML = "channels.xml";
    const CHANNELS_XML = "channels_test.xml";
    //const CHANNELS_XML = "channels_error.xml";
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
    
    /*
     * 目前只支持二级类别遍历
     * TODO: 以后支持多级类别遍历
     */
    public function getIdNamesByCategory($category)
    {
        $xml = simplexml_load_file($this->_channels_xml_path);
        $array = array();
        foreach ($xml->channel as $channel)
        {
            for ($i=0; $i<count($channel->category); $i++)
            {
                if ($channel->category[$i] == $category)
                {
                    $id = $channel["id"];
                    $array["$id"] = (string)($channel->name);  // SimpleXMLElement object to string
                }
            }
        }
        return $array;
    }
    
    public function getRootCategories()
    {
        $xml = simplexml_load_file($this->_category_xml_path);
        $result = array();
        foreach ($xml->category as $category)
        {
            $id = $category["id"];
            $array["name"] = (string)($category->name);
            if (isset($category->category))
            {
                $array["has_sub_category"] = 1;
            }
            else
            {
                $array["has_sub_category"] = 0;
            }
            $result["$id"] = $array;
        }
        return $result;
    }
    
    public function getLocals()
    {
        return $this->getLocalSubCategories();
    }
    
    public function getLocalSubCategories()
    {
       $array = array();
       $xml = simplexml_load_file($this->_category_xml_path);
       for ($i=0; $i<$xml->count(); $i++)
       {
           if ($xml->category[$i]["id"] == "local")
           {
               foreach ($xml->category[$i]->category as $subcategory)
               {
                   $id = $subcategory["id"];
                   $arr1["name"] = (string)($subcategory->name);
                   if (isset($subcategory->category))
                   {
                       $arr1["has_sub_category"] = 1;
                   }
                   else
                   {
                       $arr1["has_sub_category"] = 0;
                   }
                   $arr2["$id"] = $arr1;
               }
           }
       }
       return $arr2;
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
