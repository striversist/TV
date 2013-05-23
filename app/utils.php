<?php
function gb2312_to_utf8( $instr ) 
{
    $fp = fopen('../third_party/pnews265/language/gb-unicode.tab', 'r' );
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

function get_html_charset($html)
{
    return preg_match("/<meta.+?charset=[^\w]?([-\w]+)/i",$html,$temp) ? strtolower($temp[1]):"";
}

?>