=== TextImage ===

Contributors: davidburns
Tags: posts, images, privacy
Requires at least: 2.1
Tested up to: 2.7.1
Stable tag: trunk

This plugin displays the text of your post as a .png image. 

== Description ==

The TextImage plugin for WordPress displays your post text as a PNG image instead of sending it to the browser as normal HTML. You can specify the text color, background color, font, font size, and image width you want to use.

Any text in your post enclosed between &lt;textimage&gt; and&lt;/textimage&gt; tags will be displayed as an image. 

Why would you want to do this? Text rendered as an image cannot be read by most robots and filters. TextImage can help ensure that your posts won't be found by search engines and the like. If you want your posts to have the widest audience possible, TextImage probably isn't for you. If you want to maintain a weblog for a private community and keep a low profile, TextImage might help.

If you just need an occasional TextImage and don't want to install the plugin, just go to http://t2img.com/ where you can use the free web service to create your image.

The idea for the TextImage plugin came to me when I read about the Great Firewall of China ( see http://www.greatfirewallofchina.org/). This Internet censorship regime uses automated filtering to accomplish most of its dirty work. Something like TextImage might help get real information past this kind of robotic tyranny, though of course it will be useless against human censors.

TextImage does not alter your post or change anything in your database, except its own configuration options. If you deactivate the TextImage plugin your posts will display normally.

Version 0.22 is a minor upgrade. The settings page now lets you specify the directory to search for fonts. This version also has code to circumvent a bug in WordPress 2.7.1 which caused TextImage (and many other plugins) to stop working under PHP 4.

Version 0.21 corrected an error in the plugin URI. If you used the "plugin homepage" link to download version
0.2, you got a previous version instead; please download again.
 
== Installation ==

1. In addition to the normal WordPress requirements, TextImage requires gd and TrueType fonts. See "Requirements" for details.
1. Upload textimage.php and wrapped_text_image.php to the /wp-content/plugins/ directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Set your desired options in the Options->Textimage Plugin screen

== Frequently Asked Questions ==

none.

== Screenshots ==

none

== Requirements ==

TextImage requires the gd module for PHP.  If phpinfo() has a section for the gd module, you're probably OK. On my CentOS 
4.3 server, I used the command 'yum install php-gd' to get the correct module installed. TextImage tries to detect whether 
gd is installed, and if it isn’t, TextImage won't try to change the way WordPress displays your posts.

TextImage also needs TrueType fonts. These are files with the extension .ttf. There are probably hundreds of them on your Windows machine under /windows/fonts. There are also websites that sell TrueType fonts, or let you download them for free. 

If your Linux server has a graphical interface installed, it probably also has some TrueType fonts. TextImage looks for them under /usr/shared/fonts. If your fonts aren’t there you will need to change the value of the variable $textimage_font_directory at the top of textimage_wp.php. 

== Configuration ==

TextImage needs an image cache directory where WordPress can write the image files. If you create this in wp-content and give it the same permissions it should work. 

TextImage also needs the URL to reach this directory. This doesn't need the http://  - for example, /blog/wp-content/cache/ 

Enter text and background colors as 6-digit hex RGB values. 0xff0000 is red, 0x00ff00 is green, and 0x0000ff is blue. 

TextImage expects to find .ttf font files in /usr/share/fonts. If your fonts are somewhere else, specify a different directory on the configuration page. TextImage will recursively search directories under the one you specify to find font files.

== Limitations ==

TextImage does not render HTML: it can't apply multiple fonts, bolding and italics (unless everything is bold or italic), or 
other fancy formatting to your post. You get one font in one size and color. You can, however, force line breaks by using carriage returns.

TextImage strips html tags from your post before it renders the image. It does not, however, modify the actual post. If you disable TextImage, all your HTML will work as before.

The current word-wrap algorithm sometimes makes lines shorter than they need to be.

TextImage renders only posts as images. Titles, comments, and all other content are unaffected. 

TextImage receives only the most basic testing. Please report any problems by commenting on the TextImage blog at 
http://www.t2img.com/blog.
