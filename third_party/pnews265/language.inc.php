<?

# PHP News Reader
# Copyright (C) 2001-2007 Shen Cheng-Da
# 
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

#$CFG['default_language'] = 'en';

$lang_option = array(	'en'         => 'English',
			'zh-tw'      => 'Chinese (BIG5)',
			'zh-cn'      => 'Chinese (GB)',
			'unicode'    => 'Unicode (UTF-8)',
			'fr'         => 'Fran&ccedil;ais',
			'fi'         => 'Finnish',
			'de'         => 'German',
			'it'         => 'Italiano',
			'sk'         => 'Slovak' );

$lang_define = array(	'en'         => 'language/english.inc.php',
			'zh-tw'      => 'language/chinese.inc.php',
			'zh-cn'      => 'language/chinese_gb.inc.php',
			'unicode'    => 'language/english.inc.php',
			'fr'         => 'language/french.inc.php',
			'fi'         => 'language/finnish.inc.php',
			'de'         => 'language/german.inc.php',
			'it'         => 'language/italian.inc.php',
			'sk'         => 'language/slovak.inc.php' );

$lang_coding = array(	'en'         => 'iso-8859-1',
			'zh-tw'      => 'BIG5',
			'zh-cn'      => 'GB2312',
			'unicode'    => 'UTF-8',
			'fr'         => 'iso-8859-15',
			'fi'         => 'iso-8859-15',
			'de'         => 'iso-8859-15',
			'it'         => 'iso-8859-15',
			'sk'         => 'iso-8859-2' );

$charset_alias = array( 'big5'       => 'big5',
			'gb'         => 'gb2312',
			'gb2312'     => 'gb2312',
			'utf-8'      => 'utf-8',
			'iso-8859-1' => 'iso-8859-1',
			'iso-8859-2' => 'iso-8859-2',
			'iso-8859-15'=> 'iso-8859-15',
			'de-ascii'   => 'iso-8859-15',
			'us-ascii'   => 'iso-8859-15',
			'fr-ascii'   => 'iso-8859-15',
			'it-ascii'   => 'iso-8859-15',
			'ascii'      => 'iso-8859-15' );

if( isset($_SESSION['session_language']) )
	$curr_language = $_SESSION['session_language'];
elseif( isset($_COOKIE['cookie_language']) ) {
	$curr_language = $_COOKIE['cookie_language'];
	$_SESSION['session_language'] = $_COOKIE['cookie_language'];
}
elseif( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
	$lang_prefer = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
	$ml = $lang_prefer[0];
#	$ml = substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 );
	if( isset($lang_option[$ml]) ) {
		$curr_language = $ml;
		$_SESSION['session_language'] = $ml;
	}
	else {
		$curr_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		$_SESSION['session_language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	}
}
else
	$curr_language = '';

if( $curr_language == '' || !isset($lang_option[$curr_language]) ) {
	$curr_language = $CFG['default_language'];
	$_SESSION['session_language'] = $CFG['default_language'];
}

setcookie( 'cookie_language', $curr_language, time()+86400*30 );

$curr_charset = $lang_coding[$curr_language];

/* Include the localized language definition resource */

// echo "<!-- include {$lang_define[$curr_language]} -->\n";

@include($lang_define[$curr_language]);

if( !isset( $pnews_msg['Login'] ) ) {
	echo "<font face=Georgia>Warning: language definition missed ({$lang_define[$curr_language]})</font><br>\n";
	echo "<!-- Current language: $curr_language -->\n";
	echo "<!-- Current charset:  $curr_charset  -->\n";
}
function b2g( $instr ) {

	$fp = fopen( 'language/big5-gb.tab', 'r' );

	$len = strlen($instr);
	for( $i = 0 ; $i < $len ; $i++ ) {
		$h = ord($instr[$i]);
		if( $h >= 160 ) {
			$l = ($i+1 >= $len) ? 32 : ord($instr[$i+1]);
			if( $h == 161 && $l == 64 )
				$gb = '  ';
			else {
				fseek( $fp, (($h-160)*255+$l-1)*3 );
				$gb = fread( $fp, 2 );
			}
			$instr[$i] = $gb[0];
			$instr[$i+1] = $gb[1];
			$i++;
		}
	}
	fclose($fp);
	return $instr;
}

function g2b( $instr ) {

	$fp = fopen( 'language/gb-big5.tab', 'r' );

	$len = strlen($instr);
	for( $i = 0 ; $i < $len ; $i++ ) {
		$h = ord($instr[$i]);
		if( $h > 160 && $h < 248 ) {
			$l = ($i+1 >= $len) ? 32 : ord($instr[$i+1]);
			if( $l > 160 && $l < 255 ) {
				fseek( $fp, (($h-161)*94+$l-161)*3 );
				$bg = fread( $fp, 2 );
			}
			else
				$bg = '  ';
			$instr[$i] = $bg[0];
			$instr[$i+1] = $bg[1];
			$i++;
		}
	}
	fclose($fp);
	return $instr;
}

function b2u( $instr ) {
	$fp = fopen( 'language/big5-unicode.tab', 'r' );
	$len = strlen($instr);
	$outstr = '';
	for( $i = $x = 0 ; $i < $len ; $i++ ) {
		$h = ord($instr[$i]);
		if( $h >= 160 ) {
			$l = ( $i+1 >= $len ) ? 32 : ord($instr[$i+1]);
			if( $h == 161 && $l == 64 )
				$uni = '  ';
			else {
				fseek( $fp, ($h-160)*510+($l-1)*2 );
				$uni = fread( $fp, 2 );
			}
			$codenum = ord($uni[0])*256 + ord($uni[1]);
			if( $codenum < 0x800 ) {
				$outstr[$x++] = chr( 192 + $codenum / 64 );
				$outstr[$x++] = chr( 128 + $codenum % 64 );
#				printf("[%02X%02X]<br>\n", ord($outstr[$x-2]), ord($uni[$x-1]) );
			}
			else {
				$outstr[$x++] = chr( 224 + $codenum / 4096 );
				$codenum %= 4096;
				$outstr[$x++] = chr( 128 + $codenum / 64 );
				$outstr[$x++] = chr( 128 + ($codenum % 64) );
#				printf("[%02X%02X%02X]<br>\n", ord($outstr[$x-3]), ord($outstr[$x-2]), ord($outstr[$x-1]) );
			}
			$i++;
		}
		else
			$outstr[$x++] = $instr[$i];
	}
	fclose($fp);
	if( $instr != '' )
		return join( '', $outstr);
}

function u2b( $instr ) {
	$fp = fopen( 'language/unicode-big5.tab', 'r' );
	$len = strlen($instr);
	$outstr = '';
	for( $i = $x = 0 ; $i < $len ; $i++ ) {
		$b1 = ord($instr[$i]);
		if( $b1 < 0x80 ) {
			$outstr[$x++] = chr($b1);
#			printf( "[%02X]", $b1);
		}
		elseif( $b1 >= 224 ) {	# 3 bytes UTF-8
			$b1 -= 224;
			$b2 = ord($instr[$i+1]) - 128;
			$b3 = ord($instr[$i+2]) - 128;
			$i += 2;
			$uc = $b1 * 4096 + $b2 * 64 + $b3 ;
			fseek( $fp, $uc * 2 );
			$bg = fread( $fp, 2 );
			$outstr[$x++] = $bg[0];
			$outstr[$x++] = $bg[1];
#			printf( "[%02X%02X]", ord($bg[0]), ord($bg[1]));
		}
		elseif( $b1 >= 192 ) {	# 2 bytes UTF-8
			printf( "[%02X%02X]", $b1, ord($instr[$i+1]) );
			$b1 -= 192;
			$b2 = ord($instr[$i]) - 128;
			$i++;
			$uc = $b1 * 64 + $b2 ;
			fseek( $fp, $uc * 2 );
			$bg = fread( $fp, 2 );
			$outstr[$x++] = $bg[0];
			$outstr[$x++] = $bg[1];
#			printf( "[%02X%02X]", ord($bg[0]), ord($bg[1]));
		}
	}
	fclose($fp);
	if( $instr != '' ) {
#		echo '##' . $instr . " becomes " . join( '', $outstr) . "<br>\n";
		return join( '', $outstr);
	}
}

function g2u( $instr ) {
	$fp = fopen( 'language/gb-unicode.tab', 'r' );
	$len = strlen($instr);
	$outstr = '';
	for( $i = $x = 0 ; $i < $len ; $i++ ) {
		$h = ord($instr[$i]);
		if( $h > 160 ) {
			$l = ( $i+1 >= $len ) ? 32 : ord($instr[$i+1]);
			fseek( $fp, ($h-161)*188+($l-161)*2 );
			$uni = fread( $fp, 2 );
			$codenum = ord($uni[0])*256 + ord($uni[1]);
			if( $codenum < 0x800 ) {
				$outstr[$x++] = chr( 192 + $codenum / 64 );
				$outstr[$x++] = chr( 128 + $codenum % 64 );
#				printf("[%02X%02X]<br>\n", ord($outstr[$x-2]), ord($uni[$x-1]) );
			}
			else {
				$outstr[$x++] = chr( 224 + $codenum / 4096 );
				$codenum %= 4096;
				$outstr[$x++] = chr( 128 + $codenum / 64 );
				$outstr[$x++] = chr( 128 + ($codenum % 64) );
#				printf("[%02X%02X%02X]<br>\n", ord($outstr[$x-3]), ord($outstr[$x-2]), ord($outstr[$x-1]) );
			}
			$i++;
		}
		else
			$outstr[$x++] = $instr[$i];
	}
	fclose($fp);
	if( $instr != '' )
		return join( '', $outstr);
}

function u2g( $instr ) {
	$fp = fopen( 'language/unicode-gb.tab', 'r' );
	$len = strlen($instr);
	$outstr = '';
	for( $i = $x = 0 ; $i < $len ; $i++ ) {
		$b1 = ord($instr[$i]);
		if( $b1 < 0x80 ) {
			$outstr[$x++] = chr($b1);
#			printf( "[%02X]", $b1);
		}
		elseif( $b1 >= 224 ) {	# 3 bytes UTF-8
			$b1 -= 224;
			$b2 = ($i+1 >= $len) ? 0 : ord($instr[$i+1]) - 128;
			$b3 = ($i+2 >= $len) ? 0 : ord($instr[$i+2]) - 128;
			$i += 2;
			$uc = $b1 * 4096 + $b2 * 64 + $b3 ;
			fseek( $fp, $uc * 2 );
			$gb = fread( $fp, 2 );
			$outstr[$x++] = $gb[0];
			$outstr[$x++] = $gb[1];
#			printf( "[%02X%02X]", ord($gb[0]), ord($gb[1]));
		}
		elseif( $b1 >= 192 ) {	# 2 bytes UTF-8
			printf( "[%02X%02X]", $b1, ord($instr[$i+1]) );
			$b1 -= 192;
			$b2 = ($i+1>=$len) ? 0 : ord($instr[$i+1]) - 128;
			$i++;
			$uc = $b1 * 64 + $b2 ;
			fseek( $fp, $uc * 2 );
			$gb = fread( $fp, 2 );
			$outstr[$x++] = $gb[0];
			$outstr[$x++] = $gb[1];
#			printf( "[%02X%02X]", ord($gb[0]), ord($gb[1]));
		}
	}
	fclose($fp);
	if( $instr != '' ) {
#		echo '##' . $instr . " becomes " . join( '', $outstr) . "<br>\n";
		return join( '', $outstr);
	}
}

function get_conversion( $original, $preferred ) {

	global $charset_alias;

	$original = $charset_alias[trim(strtolower($original))];
	$preferred = $charset_alias[trim(strtolower($preferred))];

	if ( ( $preferred == 'big5' ) && ( $original == 'gb2312' ) ) {
		$convert['to'] = 'g2b';
		$convert['back'] = 'b2g';
		$convert['source'] = 'GB2312';
		$convert['result'] = 'BIG5';
	}
	elseif ( ( $preferred == 'gb2312' ) && ( $original == 'big5' ) ) {
		$convert['to'] = 'b2g';
		$convert['back'] = 'g2b';
		$convert['source'] = 'BIG5';
		$convert['result'] = 'GB2312';
	}
	elseif ( ( $preferred == 'utf-8' ) && ( $original == 'big5' ) ) {
		$convert['to'] = 'b2u';
		$convert['back'] = 'u2b';
		$convert['source'] = 'BIG5';
		$convert['result'] = 'UTF-8';
	}
	elseif ( ( $preferred == 'big5' ) && ( $original == 'utf-8' ) ) {
		$convert['to'] = 'u2b';
		$convert['back'] = 'b2u';
		$convert['source'] = 'UTF-8';
		$convert['result'] = 'BIG5';
	}
	elseif ( ( $preferred == 'utf-8' ) && ( $original == 'gb2312' ) ) {
		$convert['to'] = 'g2u';
		$convert['back'] = 'u2g';
		$convert['source'] = 'GB2312';
		$convert['result'] = 'UTF-8';
	}
	elseif ( ( $preferred == 'gb2312' ) && ( $original == 'utf-8' ) ) {
		$convert['to'] = 'u2g';
		$convert['back'] = 'g2u';
		$convert['source'] = 'UTF-8';
		$convert['result'] = 'GB2312';
	}
	else {
		$convert['to'] = null;
		$convert['back'] = null;
	}
	return($convert);
}

?>
