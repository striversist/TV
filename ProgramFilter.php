<?php
require_once (dirname(__FILE__).'/'.'../simplehtmldom_1_5/simple_html_dom.php');

set_time_limit(0);
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
    
    public function getProgramList($dom)
    {
        foreach ($dom->find("div[id=PMT1]") as $pmt1)
        {
            $times = $pmt1->find("div[id=e1] b font");
            $titles = $pmt1->find("div[id=e2] a.black");
            if(!count($titles))  // 过滤“广告”
            {
                $titles = $pmt1->find("div[id=e2]");
                
                // Deal with special case: <div id="e2">abcd<a href="xxx">...</a></div>
                $start = strpos($titles[0]->outertext, ">", 1);
                $end = strpos($titles[0]->outertext, "<", 1);
                $substr = substr($titles[0]->outertext, $start + 1, $end - $start - 1);
                $titles[0]->plaintext = $substr;
                
                if(!count($titles))
                    continue;
            }
            //echo "time: ".$times[0]->plaintext."  ";
            //echo "title: ".$titles[0]->plaintext."<br />";
            $list[] = @array("time" => $times[0]->plaintext, "title" => $titles[0]->plaintext);
        }
        foreach ($dom->find("div[id=PMT2]") as $pmt2)
        {
            $times = $pmt2->find("div[id=e1] b font");
            $titles = $pmt2->find("div[id=e2] a.black");
            $substr = null;
            if(!count($titles))  // 过滤“广告”
            {
                $titles = $pmt2->find("div[id=e2]");
                
                // Deal with special case: <div id="e2">abcd<a href="xxx">...</a></div>
                $start = strpos($titles[0]->outertext, ">", 1);
                $end = strpos($titles[0]->outertext, "<", 1);
                $substr = substr($titles[0]->outertext, $start + 1, $end - $start - 1);
                $titles[0]->plaintext = $substr;
                
                if(!count($titles))
                    continue;
            }

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
