<?php
/**
 * Description of Config
 *
 * @author Administrator
 */
class Config 
{
    const DATA_SRC_TVMAO = "tvmao";
    const DATA_SRC_TVSOU = "tvsou";
    const MAX_COLLECT_DAYS = 7;
    public static $DATA_SRC = self::DATA_SRC_TVSOU;
    public static $ChannelDetailFromWeb = true;
    public static $EnableAd = true;
    public static $HotSource = "tvmao";
}

?>
