<?

# PHP News Reader
# Copyright (C) 2001-2007 Shen Chang-Da
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

if( $_SERVER['argc'] < 3 ) {
	echo "Usage: php fix-g2b.php [word-in-gb] [word-in-big5]\n";
	exit;
}

$loc = get_g2b_location( $_SERVER['argv'][1] );

if( strlen( $_SERVER['argv'][1] ) != 2  || strlen( $_SERVER['argv'][1] ) != 2 ) {
	echo "String length is incorrect\n";
	exit;
}

if( $loc < 0 ) {
	echo "Word not converted\n";
	exit;
}

$fp = fopen( 'gb-big5.tab', 'r+' );
fseek( $fp, $loc*3 );
fwrite( $fp, $_SERVER['argv'][2], 2 );
fclose($fp);

echo "Word converted\n";

function get_g2b_location( $instr ) {
	$h = ord($instr[0]);
	if( $h > 160 && $h < 248 ) {
		$l = ord($instr[1]);
		if( $l > 160 && $l < 255 ) {
			return(($h-161)*94+$l-161);
		else
			return(-1);
	}
	else
		return(-1);
}

?>
