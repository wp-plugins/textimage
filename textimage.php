<?php
/*
Plugin Name: TextImage
Plugin URI: http://harbor.sealrock.com/ti/wp-content/uploads/2007/04/textimage.zip
Description: This plugin displays the text of your post as a .png image. 
Author: David Burns
Version: 0.13
Author URI: http://harbor.sealrock.com/ti/
*/ 


/* 
TextImage for Wordpress
Copyright (C) 2007 David Burns

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


// globals - replace with configuration options
	$textimage_font_directory = '/usr/share/fonts/'; // recursively search this directory for ttf fonts

	include 'wrapped_text_image.php';

// helper function - for php < 5.1

if ( !function_exists('htmlspecialchars_decode') )
{
    function htmlspecialchars_decode($text)
    {
        return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
    }
}

// filter function

	function textimage_filter_the_post($the_text)
	{
		global $textimage_font, $textimage_font_directory;
		if (function_exists('imagettftext')) {
			$image = null;
			$basename = md5($the_text) . '.png';
			$filename = get_option('textimage_cache') .  $basename;
			if (!file_exists($filename)) {
				$tcol = 0 + get_option('textimage_text_color'); // enforce numeric type
				$bcol = 0 + get_option('textimage_background_color');
				$fh = 0 + get_option('textimage_font_height');
				$iw = 0 + get_option('textimage_image_width');
				$fontfile = get_option('textimage_font');
				$image = wrapped_text_image(htmlspecialchars_decode(strip_tags($the_text)),$fontfile,
					$fh,$tcol,$bcol,$iw);
				imagepng($image, $filename); // save to cache
			} else {
	// not needed in production: error_log("Image in cache, skipping creation.");
			}

		// PHP would allow us to output the image directly, 
		// but then caching and the rest of the code would be more complicated.

			$cache_url = get_option('textimage_cache_url');
			$display_the_text = get_option('textimage_display_text');
			if ($display_the_text == "1") {
				$the_text = $the_text . "<img src=\"$cache_url$basename\">";
			} else {
				$the_text = "<img src=\"$cache_url$basename\">";
			}
		}
		return $the_text;
	}

	add_filter('the_content', 'textimage_filter_the_post');

	function textimage_get_fontlist($dir)
	{
		$fontlist = array();
		$dh = opendir($dir);
		while (($file = readdir($dh)) !== false) {
			$pinfo = pathinfo($file);
			if ($pinfo[extension] == "ttf") {
				$fontlist[] = $dir . $file;
			}
			else if (is_dir($dir . $file)) {
				if (($file != '.') && ($file != '..')) {
					$templist = textimage_get_fontlist($dir . $file . '/');
					$fontlist = array_merge($fontlist, $templist);
				}
			} 		
		}
		closedir($dh);
		return $fontlist;
	}

// Validate and add a trailing slash to path if needed
	function validate_path($t)
	{
		$t = stripslashes($t);
		if ($t[strlen($t)-1] != '/')
		{
			// error_log("adding slash to $t");
			$t .= '/';
		} else {
			// error_log("$t does not need a slash.");
		}
		return $t;
	}


// options page function
	function text_image_options()
	{
		global $textimage_font, $textimage_font_directory;
		if (!empty($_POST)) {
			error_log("display text: " . $_POST['textimage_display_text']);
			$textimage_cache = validate_path($_POST['textimage_cache']);
			$textimage_cache_url = validate_path($_POST['textimage_cache_url']);
			// clear the image cache 
			if ($_POST['clear_cache'] == 1) {
				$old_cache = get_option('textimage_cache');
				if (is_string($old_cache)) {
					foreach(glob("$old_cache*.png") as $fn) {
						unlink($fn);
					}
				}
			}
			update_option("textimage_cache", $textimage_cache);
			update_option("textimage_cache_url", $textimage_cache_url);
			update_option("textimage_image_width", $_POST['textimage_image_width']);
			update_option("textimage_font_height", $_POST['textimage_font_height']);
			update_option("textimage_text_color", $_POST['textimage_text_color']);
			update_option("textimage_background_color", $_POST['textimage_background_color']);
			update_option("textimage_font", $_POST['textimage_font']);
			update_option("textimage_display_text", $_POST['textimage_display_text']);
		} 
			$textimage_cache = get_option("textimage_cache");
			$textimage_cache_url = get_option("textimage_cache_url");
			$textimage_image_width = get_option("textimage_image_width");
			$textimage_font_height = get_option("textimage_font_height");
			$textimage_text_color = get_option("textimage_text_color");
			$textimage_background_color = get_option("textimage_background_color");
			$textimage_font = get_option("textimage_font");
			$textimage_display_text = get_option("textimage_display_text");
		
		$fontlist = textimage_get_fontlist($textimage_font_directory);
		?>
		<div class="wrap">
		<h2>TextImage Options</h2>
<?php	if (!function_exists('imagettftext')) { ?>
		<p>You don't seem to have the PHP gd module installed. This is required in order to use the TextImage plugin.</p>
		<p>For example, in CentOS 4 the command 'yum install php-gd' will install the necessary components.</p>
<?php }  else { ?>
		<div class="narrow">
		<form method="post" action="" id="text-image-options" style="margin: auto; width: 400px;">
		<p class="submit">
		<input type="submit" value='Update Options &raquo;'><br>
		</p>
		<table class="optiontable">
		<tr><th scope="row">Clear image cache:</th><td>
	<label for="clear_cache">
		<input name="clear_cache" type="checkbox" id="clear_cache" value="1"  /> 
		Delete all images in the cache and regenerate using new options</label></td></tr>
		<tr><th scope="row">Image cache directory:</th><td>
		<input type="text" id="textimage_cache" name="textimage_cache" 
			size=30 value='<?php echo get_option('textimage_cache'); ?>' ></td></tr>
		<tr><th scope="row">Image cache URL:</th><td><input type="text" id="textimage_cache_url" 
			name="textimage_cache_url" size=30 value='<?php echo get_option('textimage_cache_url'); ?>' ></td></tr>
		<tr><th scope="row">Image width (pixels):</th><td><input type="text" name="textimage_image_width" id="textimage_image_width" 
			size=6 value='<?php echo get_option('textimage_image_width'); ?>' ></td></tr>
		<tr><th scope="row">Font height (points):</th><td><input type="text" id="textimage_font_height" name="textimage_font_height" 
			size=6 value='<?php echo get_option('textimage_font_height'); ?>' ></td></tr>
		<tr><th scope="row">Text color:</th><td><input type="text" id="textimage_text_color" name="textimage_text_color" 
			size=8 value='<?php echo get_option('textimage_text_color'); ?>' ></td></tr>
		<tr><th scope="row">Background color:</th><td><input type="text" id="textimage_background_color" 
			name="textimage_background_color" size=8 value='<?php echo get_option('textimage_background_color'); ?>' ></td></tr>
		<tr><th scope="row">Font:</th><td><select id="textimage_font" name="textimage_font" 
			size=1 value='<?php echo get_option('textimage_font'); ?>' >
		<?php
				function quote_string($s) {
					return ('"'.$s.'"');
				}

				foreach($fontlist as $font) {
					echo("<option");
					if ($font == $textimage_font) echo(" selected"); // select if selected 
					echo(" value=");
					echo quote_string($font);
					echo(">");
					echo($font);
					echo("\n");
				}
		?>
	</select>
					
		</td></tr>
		<tr><th scope="row">Display text:</th>
		<td><label for="textimage_display_text"><input type="checkbox" 
			id="textimage_display_text" name="textimage_display_text"
			value="1"
			<?php if (get_option('textimage_display_text') == 1) echo " checked " ; ?> >
			Display text before image, for testing</label>
		</td></tr>
		</table>
		</form>
		</div>
		</div>
	
		<?php 
		} // end if-else gd installed

	} // end function text_image_options()
	
	function text_image_init_options()
	{
		if (function_exists('add_options_page')) {
			add_options_page("TextImage plugin", "TextImage", 5, text_image_options, text_image_options);
		} else {
			error_log("can't add options page");
		}
	}
	
	function text_image_init()
	{
		add_action('admin_menu', 'text_image_init_options');
	}
	add_action('init', 'text_image_init');

/* Version history

	0.13 add textimage_display_text option
	0.12 use htmlspecialchars_decode/encode 
	0.11 Added URI to comment, and this version history. also, add a trailing slash if needed to cahce and url.
	0.1 initial version

*/
?>
