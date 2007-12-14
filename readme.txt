=== TextImage ===

Contributors: davidburns
Tags: post, images, privacy
Requires at least: 2.1, untested with earlier versions
Tested up to: 2.1
Stable tag: trunk

This plugin displays the text of your post as a .png image. 

== Description ==

The TextImage plugin for WordPress displays your post text as a PNG image instead of sending it to the browser as normal HTML. You can specify the text color, background color, font, font size, and image width you want to use. 
Why would you want to do this? Text rendered as an image can't be read by most robots and filters. TextImage can 
help ensure that your posts won't be found by search engines and the like. If you want your posts to have the 
widest audience possible, TextImage probably is not for you. If you want to maintain a weblog for a private 
community and keep a low profile, TextImage might help.

The idea for the TextImage plugin came to me when I read about the Great Firewall of China ( see http://www.greatfirewallofchina.org/). This Internet censorship regime uses automated filtering to accomplish most of its dirty work. Something like TextImage might help get real information past this kind of robotic tyranny, though of course it will be useless against human censors.

== Installation ==

1. In addition to the normal WordPress requirements, TextImage requires gd and TrueType fonts. See "Requirements" for details.
1. Upload textimage.php and wrapped_text_image.php to the /wp-content/plugins/ directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Set your desired options in the Options->Textimage Plugin screen

All directory names and URLS must be given a trailing slash. 

== Frequently Asked Questions ==

none.

== Screenshots ==

none

== Requirements ==

TextImage requires the gd module for PHP.  If phpinfo() has a section for the gd module, you're probably OK. On 
my CentOS 4.3 server, I used the command 'yum install php-gd' to get the correct module installed. TextImage 
tries to detect whether gd is installed, and if it isn't, TextImage won't try to change the way WordPress 
displays your posts.

TextImage also needs TrueType fonts. These are files with the extension .ttf. There are probably hundreds of them on your Windows machine under /windows/fonts. There are also websites that sell TrueType fonts, or let you download them for free. 

If your Linux server has a graphical interface installed, it probably also has some TrueType fonts. TextImage looks for them under /usr/shared/fonts. If your fonts aren’t there you will need to change the value of the variable $textimage_font_directory at the top of textimage_wp.php. 

== Limitations ==

TextImage does not render HTML: it can't apply multiple fonts, bolding and italics (unless everything is bold or 
italic), or other fancy formatting to your post. You get one font in one size and color. You can, however, force line breaks by using carriage returns.

TextImage strips html tags from your post before it renders the image. It does not, however, modify the actual post. If you disable TextImage, all your HTML will work as before.

TextImage renders only posts as images. Titles, comments, and all other content are unaffected. 

== Other notes ==

To my great chagrin I discovered recently that Wordpress.org has been distributing a test 
version of TextImage that renders both the text and the image. That obviously is of little 
utility.

This version fixes that by making the test mode an option. If you turn test mode on, you'll 
see both the text and the image. Some posts display strangely on my test system but that's 
OK, since it's just for testing.
