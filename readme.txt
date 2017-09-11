=== Child Themify ===
Contributors: JohnPBloch
Tags: themes, child, theme
Requires at least: 4.7.0
Tested up to: 4.8.9
Stable tag: {{VERSION}}
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Network: true

Create child themes at the click of a button.

== Description ==

Create child themes from any non-child theme at the click of a button.

This plugin is multisite compatible; if used on a multisite network, controls for creating child themes will be in the network admin instead of the regular site admin.

== Installation ==

1. Upload the `child-themify` directory and its contents to the `/wp-content/plugins/` directory (or your custom location if you manually changed the location).
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You can now create a child theme of any non-child theme you have installed by going to the themes page and clicking "Create a child theme" from the actions links of the theme of your choice.

== Frequently Asked Questions ==

= Where can I get some help? =

I'd really prefer that you use [Github's issue tracker](https://github.com/johnpbloch/child-themify/issues/new). The [WordPress.org support forum for the plugin](https://wordpress.org/support/plugin/child-themify) will work too, it will just take longer.

== Screenshots ==

1. Network administration area
2. Single site administration area

== Changelog ==

= 1.2.0 =
* Released: 2016-10-13
* The plugin now creates a functions.php file in the new theme

= 1.1.2 =
* Released: 2015-10-13
* Fixed basename location when loading the textdomain
* Added textdomain and domainpath headers to plugin file

= 1.1.1 =
* Released: 2015-01-13
* Added French translation from FR_lucien

= 1.1.0 =
* Released: 2014-12-20
* Thumbnail now gets copied when you create a child theme
* Various other tweaks and security hardening

= 1.0.4 =
* Released: 2014-09-15
* Added support for WordPress 4.0

= 1.0.3 =
* Released: 2014-04-15
* Standardized theme action links shim. See https://github.com/johnpbloch/child-themify/issues/2 for more information
* Maintenance, code cleanup, bug fixes

= 1.0.2 =
* Released: 2014-01-13
* Added support for WP 3.8

= 1.0.1 =
* Released: 2013-01-18
* Add a semicolon to the end of the @import line in the stylesheet. Props to Luis Alejandre (wpthemedetector.com) for finding and solving.

= 1.0 =
* Released: 2012-12-31
* Initial Release

== Upgrade Notice ==

Creates a blank functions.php file
