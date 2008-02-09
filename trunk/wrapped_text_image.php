<?php

/* version: 0.2
 *  Copyright (c) 2002-2008 Seal Rock Research (www.sealrock.com) 

 *  all rights reserved.

 *
 * 
 *	generate an image with word-wrapped text 
 *	text: 		text to display

 *	font: 		ttf font file to use

 *	fontheight:	in pixels

 *	color:		rgb value, default black

 *  returns:	the image.

 *	Remember to destroy the image when you are done with it.

 */

function measure_string($s,$font,$fh,$ta)
{
	// error_log("measure_string: font ." . $font . ".", 0);
	list($blx,$bly,$brx,$bry,$trx,$try,$tlx,$tly) = imagettfbbox($fh,$ta,$font,$s);
	return max($trx,$brx) - min($tlx,$blx);
}

function measure_char($c,$font,$fh,$ta=0)
{
	$s1 = "mm";
	$m1 = measure_string($s1,$font,$fh,$ta);
	$m2 = measure_string("m" . $c . "m",$font,$fh,$ta);
	return $m2 - $m1;
}

/*
  * Given a string of text, return an array of lines
  * wrapped to fit within line_width
  */
function wrap_text($text, $font, $fontheight, $line_width)
{
	$text_angle = 0;
	$a_chars = array();
	$linelen = 0;
	$line_start = 0;
	$lastpos = 0;	
	$line = "";
	$len = strlen($text);
	for ($i = 0; $i < $len; $i++) {
		if (ctype_space($text[$i]) || ($text[$i] == "\n") || ($i == $len -1)) { // end of a word?
			if (measure_string($line, $font,$fontheight, $text_angle) >= $line_width) { // backup
				if ($lastpos > $line_start) {
					$text[$lastpos] = "\n";
					$line_start = $lastpos;
					$line = substr($text,$lastpos+1, $i - $lastpos); // what if this $line is too long?
				} else {
					// handle case of no word breaks in line > width
					// options: punt or insert newline
					// choice: punt
					// this code isn't right. maybe the right thing is to just insert a newline _here_.
					while (($text[$i] != '\n') && !ctype_space($text[$i]) && ($i < $len)) {
						$i++;
						$line_start = $lastpos;
						$line = "";
					}
				} // end if-else lastpos > line_start
			} else {
				$lastpos = $i; // not too long yet: mark end of last word seen
			}
		}
		if ($text[$i] == '\n') {
			$line_start = $lastpos;
			$line = "";
		} else {
			$line = $line . $text[$i]; // could use substr to get same effect
		}
	
	}

	$arr = explode("\n", $text);
	return $arr;
}

function wrapped_text_image($text="", $font='/home/david/wp/wp-content/plugins/bookos.ttf', $fontheight=16, $color=0xffff00, $bgcolor=0x808080, $width=480 )
{
//	$old_level = error_reporting(6143); //E_ALL

	error_log("Wrapping text '$text'");

	$height = 30;
	$textangle = 0;
	
	$v_pad = 6; // pad at top and bottom of image
	$h_pad = 5; // pad at left and right

	$line_pad = 6; // pad with each line

	$image = null;

	/* measure a character */
	$textangle = 0;
	list($pos_blx, $pos_bly, $pos_brx, $pos_bry, $pos_trx, $pos_try, $pos_tlx,
		$pos_tly) = imagettfbbox($fontheight, $textangle, $font, "Mg");

	$em_height = $pos_bly - $pos_tly;

	$line_height = $em_height + $line_pad;
	$line_width = $width - $h_pad * 2;

	$arr = wrap_text($text, $font, $fontheight, $line_width);

	$height = $v_pad * 2 + sizeof($arr) * $line_height;

	// if only one line, calculate width, otherwise use passed-in width
	if (sizeof($arr) == 1)
	{
		list($blx, $bly, $brx, $bry, $trx, $try, $tlx, $tly) = 
			imagettfbbox($fontheight, $textangle, $font, $text);
		$width = $brx - $blx + $h_pad*2;
	}

	$image = imagecreate($width, $height);
	$red = ($bgcolor & 0xff0000) >> 16;
	$green = ($bgcolor & 0x00ff00) >> 8;
	$blue = $bgcolor & 0x0000ff;

	imagecolorallocate($image, $red, $green, $blue); // background color

	$red = ($color & 0xff0000) >> 16;
	$green = ($color & 0x00ff00) >> 8;
	$blue = $color & 0x0000ff;

	$textcolor = imagecolorallocate($image, $red, $green, $blue);

	$line_x = $h_pad;
	$line_y = $line_height /* +  $line_pad*/; 

	foreach ( $arr as $s )
	{
		imagettftext($image, $fontheight, $textangle, $line_x, $line_y, $textcolor, $font, $s);
		$line_y = $line_y + $line_height; 
	}
	
	// put some stuff in to make it hard to ocr the text
//	$w = 0;
//	for ($w = $width, $h = $height; $w > 0 && $h > 0; $w = $w - $width / 5, $h = $h - $height / 5)
//		imageellipse($image, $width/2, $height/2, $w, $h, $textcolor);

//	error_reporting($old_level);

	return $image;
}

function show_image($image)
{
	header("Content-type: image/png");
	imagepng($image);
}

// generate and show the image, then destroy it.
function get_image($text, $font="bookos.ttf", $fontheight=16, $color=0, $bgcolor=0xffffff, $width=480 )
{
	$image = wrapped_text_image($text, $font, $fontheight, $color, $bgcolor, $width);
	if ($image) {
		show_image($image);
		imagedestroy($image);
		return true;
	} else {
		return false;
	}
}
?>
