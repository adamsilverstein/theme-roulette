=== Theme Roulette ===
Contributors:      Adam Silverstein
Donate link:       http://wordpress.org/plugins
Tags:
Requires at least: 3.9
Tested up to:      4.1
Stable tag:        0.1.3
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

A random theme at random times. Inspired by logout roulette.

== Description ==

Randomly changes your site theme :) Changes theme on the admin side as well, for extra fun! Configurable with filters!

Chooses from installed themes, and switches to a random one on 50% of page loads (by default). If you have fewer than 50 themes, installs a new theme chosen at random from the top themes on the WordPress.org directory.

*Filterz!*

thmr_max_themes_to_install:
Installs max 50 themes (over time) by default. Use the 'thmr_max_themes_to_install' filter to adjust the maximum number of themese this plugin will install.

thmr_times_to_switch_out_of_ten:
Switches the theme 5 out of 10 loads (50 %) - adjust this setting with the 'thmr_times_to_switch_out_of_ten' filter.

thmr_dont_show_on_admin
Use the 'thmr_dont_show_on_admin' to disable on the admin side (__return_true to turn off).

WARNING: This plugin may bring your site down - use at your own risk!

== Installation ==

Enable, enjoy!

= Manual Installation =

1. Upload the entire `/theme-roulette` directory to the `/wp-content/plugins/` directory.
2. Activate Theme Roulette through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

= 0.1.2 =
* First release

== Upgrade Notice ==

= 0.1.2 =
First Release