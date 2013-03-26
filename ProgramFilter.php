<?php
require_once (dirname(__FILE__).'/'.'../simplehtmldom_1_5/simple_html_dom.php');

class ProgramFilter
{
    public static function getInstance()
    {
        if(!self::$instance_ instanceof self)
        {
            self::$instance_ = new self();
        }
        return self::$instance_;
    }
    
    public function getChannel($url)
    {
        $html = file_get_html($url);
        foreach ($html->find("div[id=PMT1]") as $pmt1)
        {
            $times = $pmt1->find("div[id=e1] b font");
            $titles = $pmt1->find("div[id=e2] a.black");
            if(!count($titles))  // 过滤“广告”
            {
                $titles = $pmt1->find("div[id=e2]");
                if(!count($titles))
                    continue;
            }
            $list[] = array("time" => $times[0]->plaintext, "title" => $titles[0]->plaintext);
        }
        foreach ($html->find("div[id=PMT2]") as $pmt2)
        {
            $times = $pmt2->find("div[id=e1] b font");
            $titles = $pmt2->find("div[id=e2] a.black");
            if(!count($titles))  // 过滤“广告”
            {
                $titles = $pmt2->find("div[id=e2]");
                if(!count($titles))
                    continue;
            }
            //$text_time = iconv("GB2312", "UTF-8", $times[0]->plaintext);
            //$text_title = iconv("GB2312", "UTF-8", $titles[0]->plaintext);
            //$list[] = array("time" => $text_time, "title" => $text_title);
            $list[] = @array("time" => $times[0]->plaintext, "title" => $titles[0]->plaintext);
        }
        
        // Dump result
        foreach ($list as $program)
        {
            echo $program["time"].": ".$program["title"]."<br />";
        }
        return $list;
    }
            
    private static $instance_;
    private function __construct() { }
    private function __clone() { }
}

?>
