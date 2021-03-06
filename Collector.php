<?php
require_once (dirname(__FILE__).'/'.'./Config.php');

class Collector
{
    const CHANNEL_CATEGORIES_XML = "categories.xml";
    const HOT_XML = "hot.xml";
    private $_channels_xml_path = null;
    private $_category_xml_path = null;
    private $_hot_xml_path = null;
    private $_channels_xml;
    private $_categories_xml;
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
     * return: array["channel_id"] = channel_url
     * eg. array["cctv1"] = "http://xxx";
     */
    public function getCollectInfoByDay($day)
    {
        if($day < 0 || $day > Config::MAX_COLLECT_DAYS)
        {
            echo "day $day out limit";
            return null;
        }
        $array = null;
        $xml = $this->_channels_xml;
        foreach ($xml->channel as $channel)
        {
            $id = $channel["id"];
            foreach ($channel->urls->url as $url)
            {
                if ($day <= 7)  // 一周内的节目按Config配置信息来取
                {
                    if ($url["src"] == Config::$DATA_SRC)
                    {
                        $suffix = $url["suffix"];
                        $array["$id"]["url"] = (string)($url.strval($day).$suffix);
                        $array["$id"]["src"] = $url["src"];
    //                    echo "getIdUrlsByDay suffix=".$suffix." url=".$array["$id"]["url"]."<br/>";
                    }
                }
                else    // 下周的信息目前只能从tvmao中取（tvsou中下周信息时错误的）
                {
                    if ($url["src"] == Config::DATA_SRC_TVMAO)  // 如果存在tvmao的配置
                    {
                        $suffix = $url["suffix"];
                        $array["$id"]["url"] = (string)($url.strval($day).$suffix);
                        $array["$id"]["src"] = $url["src"];
    //                    echo "getIdUrlsByDay suffix=".$suffix." url=".$array["$id"]["url"]."<br/>";
                    }
                }
            }
        }
        return $array;
    }
    
    public function getAllTvmaoIds()
    {
        $result = array();
        $xml = $this->_channels_xml;
        foreach ($xml->channel as $channel)
        {
            $id = $channel["id"];
            foreach ($channel->urls->url as $url)
            {
                if ($url["src"] == Config::DATA_SRC_TVMAO)  // 如果存在tvmao的配置
                {
                    $tvmao_id = $this->filterTvmaoId($url);
                    $result["$id"] = $tvmao_id;
                }
            }
        }
        return $result;
    }
    
    public function getTvmaoId($channel_id)
    {
        $tvmao_id = null;
        $xml = $this->_channels_xml;
        foreach ($xml->channel as $channel)
        {
            $id = $channel["id"];
            if ($id != $channel_id)
                continue;
            foreach ($channel->urls->url as $url)
            {
                if ($url["src"] == Config::DATA_SRC_TVMAO)  // 如果存在tvmao的配置
                {
                    $tvmao_id = $this->filterTvmaoId($url);
//                    echo "getTvmaoId id=".$tvmao_id."<br/>";
                }
            }
        }
        return $tvmao_id;
    }
    
    private function filterTvmaoId($url)
    {
        $matches = null;
        $pattern = '/.+\/(.+)-w.*/';
        preg_match($pattern, $url, $matches);
        if (key_exists(1, $matches))
            return $matches[1];
        return null;
    }
    
    /*
     * return: The url of hot TV series
     */
    public function getHotUrl()
    {
        return "http://m.tvsou.com/juqing.asp";
    }
    
    public function getIdNames()
    {
        $xml = $this->_channels_xml;
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
        $xml = $this->_channels_xml;
        $array = array();
        foreach ($xml->channel as $channel)
        {
            for ($i=0; $i<count($channel->category); $i++)
            {
                //echo "channel->category[".$i."]=".$channel->category[$i]."<br />";
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
        $xml = $this->_categories_xml;
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
    
    /*
     * 通过channel id获取对应的categories数组：key{category_id} => value{category_name}
     */
    public function getCategoriesByChannelId($channel_id)
    {
        $xml = $this->_channels_xml;
        $result = array();
        foreach ($xml->channel as $channel)
        {
            $id = $channel["id"];
            if ($channel_id == $id)
            {
                foreach ($channel->category as $category_id)
                {
//                    echo "category id = ".$category_id."<br />";
                    $result["$category_id"] = $this->getCategoryNameById("$category_id");
                }
            }
        }
        return $result;
    }
    
    /*
     * 通过category id获取该category的name
     * TODO: 目前假设Category层级最大为2
     */
    public function getCategoryNameById($category_id)
    {
        $xml = $this->_categories_xml;
        foreach ($xml->category as $category)
        {
            // 第一层级
            if ($category["id"] == $category_id)
                return (string)$category->name;

            // 第二层级
            foreach ($category as $sub_category) 
            {
                if ($sub_category["id"] == $category_id)
                    return (string)$sub_category->name;
            }
        }
        return false;
    }
    
    /**
     * 获取子目录
     * TODO: 目前假设Category层级最大为2
     */
    public function getSubCategories($parent_id)
    {
       $xml = $this->_categories_xml;
       for ($i=0; $i<$xml->count(); $i++)
       {
           if ($xml->category[$i]["id"] == $parent_id)
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
        $xml = $this->_channels_xml;
        foreach ($xml->channel as $channel)
        {
            if ($id == $channel["id"])
            {
                return (string)($channel->name);  // SimpleXMLElement object to string
            }
        }
        return null;
    }
    
    private static $instance_;
    private function __construct()
    {
        if (Config::$DATA_SRC == Config::DATA_SRC_TVSOU)
            $CHANNELS_XML = "channels_tvsou.xml";
        else if (Config::$DATA_SRC == Config::DATA_SRC_TVMAO)
            $CHANNELS_XML = "channels_tvmao.xml";
//        $CHANNELS_XML = "channels_test.xml";
//        $CHANNELS_XML = "channels_error.xml";
        
        $this->_channels_xml_path = dirname(__FILE__).'/'."xml/".$CHANNELS_XML;
        $this->_category_xml_path = dirname(__FILE__).'/'."xml/".self::CHANNEL_CATEGORIES_XML;
        $this->_hot_xml_path = dirname(__FILE__).'/'."xml/".self::HOT_XML;
        $this->_channels_xml = simplexml_load_file($this->_channels_xml_path);
        $this->_categories_xml = simplexml_load_file($this->_category_xml_path);
    }
    private function __clone() { }
}

?>
