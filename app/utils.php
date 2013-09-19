<?php
function gb2312_to_utf8( $instr ) 
{
    static $fp;
    $fp = fopen(dirname(__FILE__).'/'."../third_party/pnews265/language/gb-unicode.tab", 'r' );
    $len = strlen($instr);
    $outstr = '';
    for( $i = $x = 0 ; $i < $len ; $i++ ) 
    {
        $h = ord($instr[$i]);
        
        // wen.tang add: for special chinese characters <<
        $next = 0;
        if (isset($instr[$i + 1]))
        {
            $next = ord($instr[$i + 1]);
        }
        if (is_specical_gb2312_char($h, $next))
        {
            $convert = convert_special_gb2312_char($h, $next);
            for ($k=0; $k<count($convert); $k++)
            {
                $outstr[$x++] = $convert[$k];
            }
            $i++;
            continue;
        }
        // wen.tang end >>
        
        if( $h > 160 ) 
        {
            $l = ( $i+1 >= $len ) ? 32 : ord($instr[$i+1]);
            fseek( $fp, ($h-161)*188+($l-161)*2 );
            $uni = fread( $fp, 2 );
            if (!isset($uni[0]) || !isset($uni[1]))
                continue;
            $codenum = ord($uni[0])*256 + ord($uni[1]);
            if( $codenum < 0x800 ) 
            {
                $outstr[$x++] = chr( 192 + $codenum / 64 );
                $outstr[$x++] = chr( 128 + $codenum % 64 );
#		printf("[%02X%02X]<br>\n", ord($outstr[$x-2]), ord($uni[$x-1]) );
            }
            else 
            {
                $outstr[$x++] = chr( 224 + $codenum / 4096 );
                $codenum %= 4096;
                $outstr[$x++] = chr( 128 + $codenum / 64 );
                $outstr[$x++] = chr( 128 + ($codenum % 64) );
#		printf("[%02X%02X%02X]<br>\n", ord($outstr[$x-3]), ord($outstr[$x-2]), ord($outstr[$x-1]) );
            }
            $i++;
        }
        else
        {
            $outstr[$x++] = $instr[$i];
        }
    }
    fclose($fp);
    if( $instr != '' )
    {
        return join( '', $outstr);
    }
}

$SpecialChars = array
(
    0x8BD6 => "嬛",
    0x916A => "慾",
    0x8CC6 => "屍",
    0x8753 => "嘢",
    0x836B => "僰",
    0x87E5 => "囧",
    0x8CAA => "尓",
    0x9F68 => "焗",
    0x85EE => "咁",
    0x8956 => "塚",
    0x879F => "嚐",
    0x835E => "僞",
    0xB0A0 => "盃",
    0x9550 => "昉",
    0x8BC3 => "嬅",
    0xC3FB => "名",
    0xB5F5 => "吊",
    0x9259 => "扽",
    0x86F2 => "嗱",
    0x96FD => "桚",
    0x9FB0 => "煱",
    0x9DF7 => "濛",
    0x8253 => "係",
    0x9589 => "晧",
    0x86A8 => "啫",
    0x9D4D => "滿",
    0x83E4 => "冧",
    0x9A99 => "殭",
    0x8CC6 => "屍",
    0xA0AD => "牠",
    0x87C0 => "嚴",
    0xB2BB => "不",
    0xB8B4 => "复",
    0x87D6 => "囍",
    0x9CF3 => "滙",
    0xAB98 => "珮",
);

function is_specical_gb2312_char($first, $second)
{
    global $SpecialChars;
    $ret = false;
    $index = $first * 256 + $second;
    if (isset($SpecialChars[$index]))
    {
        $ret = true;
    }
    return $ret;
}

/*
 * first, second: 字符编码的第一、二个编码值
 * out: 输出到的数组中
 * return: 返回转换后的数组
 */
function convert_special_gb2312_char($first, $second)
{
    global $SpecialChars;
    $ret = array();
    if (is_specical_gb2312_char($first, $second))
    {
        $index = $first * 256 + $second;
        for ($i=0; $i<strlen($SpecialChars[$index]); $i++)
        {
            $ret[$i] = $SpecialChars[$index][$i];
        }
    }
    return $ret;
}

function get_html_charset($html)
{
    return preg_match("/<meta.+?charset=[^\w]?([-\w]+)/i",$html,$temp) ? strtolower($temp[1]):"";
}

function guid()
{
    if (function_exists('com_create_guid'))
    {
        return com_create_guid();
    }
    else
    {
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12);
        return $uuid;
    }
}

?>