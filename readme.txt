=== Child Themify ===
Contributors: JohnPBloch
Tags: themes, child, theme
Requires at least: 4.7.0
Tested up to: 4.8.9
Stable tag: {{VERSION}}
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create child themes with the click of a button.

== Description ==

Create child themes from any non-child theme with the click of a button.

This plugin is multisite compatible; if used on a multisite network, controls for creating child themes will be in the network admin instead of the regular site admin. Pick the parent theme from the dropdown and name name your new child theme. You can also change the author name and select files you want to copy from the parent to the new theme.

Click the button and you're all set!

== Installation ==

1. Upload the `child-themify` directory and its contents to the `/wp-content/plugins/` directory (or your custom location if you manually changed the location).
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You can now create a child theme of any non-child theme you have installed by going to the "Create Child Theme" page in the Appearance/Themes admin menu.

== Frequently Asked Questions ==

= Why do you use @import instead of enqueueing the stylesheet? =

I [catch a lot of flack](https://wordpress.org/support/topic/method-not-recommended/) for not using the method recommended to theme developers for including stylesheets, so hopefully you one-star fanatics will see this first and reconsider.

This plugin is not creating themes for submission to the repo and it doesn't have any control over the quality of the theme it's extending. In order for this plugin to successfully enqueue the child theme AND parent theme's style.css, the parent theme has to actually enqueue its own stylesheet (not a given by a long shot), it has to be done using `get_template_directory_uri()`, not `get_stylesheet_directory_uri()` (also not a given), and, in order for the child stylesheet to be enqueued *after* the parent's (kind of important for a child theme), there has to be some kind of reliable naming convention for the enqueue ID in the parent theme (no such standard exists). On the other hand, `@import` just works. It's the only available method that gets the child theme working in virtually all cases out of the box.

And if you really can't bear the thought of using `@import`, just edit the files after creating the theme so that it no longer uses it. It's *your* theme, you can do as you like with it.

= How much does this plugin do? =

This plugin *only* creates the theme for you. It doesn't do anything to the database, it doesn't do anything to your new child theme after you've created it. It only gives you buttons to create a child theme.

= Where can I get some help? =

I'd really prefer that you use [Github's issue tracker](https://github.com/johnpbloch/child-themify/issues/new). The [WordPress.org support forum for the plugin](https://wordpress.org/support/plugin/child-themify) will work too, it will just take longer.

== Screenshots ==

1. No Parent Selected
2. Parent Selected
3. Advanced Fields

== Changelog ==

= 2.0.0 =
* Released: 2017-10-04
* Completely rebuilt the interface
* Added controls to modify the Theme Author value
* Added controls to select theme files that should get copied into the new theme

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
