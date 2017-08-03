=== BFBC2 Stats ===
Contributors: Ozgur Uysal
Tags: battlefield bad company 2, battlefield, bfbc2, stats, widget, sidebar
Requires at least: 2.9.2
Tested up to: 3.0.1
Stable tag: 1.2.3

A Wordpress Widget for Battlefield Bad Company 2 players to show their short stats on their Wordpress blog pages.

== Description ==

* Author URI: http://www.fps-gamer.net
* Copyright: Released under GNU GENERAL PUBLIC LICENSE

This is a sidebar widget I wrote for Battlefield Bad Company 2 players to show their short stats on their 
blog pages. Stats are powered by bfbcs.com.

If you have any problems, please feel free to contact me using the contact form on my website.

== Installation ==

Installation is very simple like any other widgets or plugins.

1. Upload the `bfbc2-stats` folder along with all its files to the `/wp-content/plugins/` directory
2. Connect to your webroot using a ftp client. Open bfbc2-stats directory, locate and make "cache" directory writable 
   by the webserver or simply chmod it to 777.
3. Activate the plugin through the "Plugins" menu in WordPress
4. From Apperaence/Widgets drag and drop BFBC2 stats widget to your sidebar where you want it to appear.

== Widget Settings ==

* Title: Enter a title if you want to change the default title which is "My BFBC2 Stats".

* Playername: Enter your player name exactly as you use in the game.

* Platform: Select your platform; PC, XBOX360 or PS3.

* Cache Time: Stats are cached in the cache directory for faster page load and reduce unnecessary connection request to 
              bfbcs.com everytime you connect to your page. Enter a cache time if you like or keep the default value 
              3600 seconds (1 hour). I don't reccomend setting this value to a lower number like 60 secs as it may slow 
              down your page loading time.

== Screenshots ==

1. Widget preview.
2. Widget settings in admin panel.

See http://www.fps-gamer.net/bfbc2-stats/ for a live demo.

== Changelog ==

= 1.2.3 =

* Now servers without cURL support can use the plugin.
* Added some css to override various theme styles.
* Fixed the broken plugin page link on the admin plugins page. (Thanks to Richard for pointing out!)

= 1.2.2 =

* Some Fixes

= 1.2.1 =

* Fixed image path bugs

= 1.2 =

* Added platform, favorite kit and veteran status
* Added favorite weapon
* Checks cURL functions status to prevent fatal errors
* Code improvements

= 1.1 =

* Fixed ratio calculation bug
* Added rank progress bar
* Fixed cross browser display issues

= 1.0 =

* First public release