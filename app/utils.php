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
            // wen.tang add: for special chinese characters <<
            $next = 0;
            $utf8_text = "";
            if (isset($instr[$i + 1]))
            {
                $next = ord($instr[$i + 1]);
            }
            if ($h === 0x8B and $next === 0xD6)          // gb2312: 嬛(0x8BD6)
            {
                $utf8_text = "嬛";
                for ($k=0; $k<strlen($utf8_text); $k++)
                {
                    $outstr[$x++] = $utf8_text[$k];
                }
                $i++;
            }
            else if ($h === 0x91 and $next === 0x6A)    // gb2312: 慾(0x916A)
            {
                $utf8_text = "慾";
                for ($k=0; $k<strlen($utf8_text); $k++)
                {
                    $outstr[$x++] = $utf8_text[$k];
                }
                $i++;
            }
            else if ($h === 0x8C and $next === 0xC6)    // gb2312: 屍(0x8CC6)
            {
                $utf8_text = "屍";
                for ($k=0; $k<strlen($utf8_text); $k++)
                {
                    $outstr[$x++] = $utf8_text[$k];
                }
                $i++;
            }
            // wen.tang end >>
            else
            {
                $outstr[$x++] = $instr[$i];
            }
        }
    }
    fclose($fp);
    if( $instr != '' )
    {
        return join( '', $outstr);
    }
}

function get_html_charset($html)
{
    return preg_match("/<meta.+?charset=[^\w]?([-\w]+)/i",$html,$temp) ? strtolower($temp[1]):"";
}

?>