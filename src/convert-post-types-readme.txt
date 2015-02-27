=== Convert Post Types ===
Contributors: sillybean
Tags: post types, conversion
Donate Link: http://sillybean.net/code/wordpress/convert-post-types/
Text Domain: convert-post-types
Domain Path: /languages
Requires at least: 3.0
Tested up to: 4.1
Stable tag: 1.5

A bulk conversion utility for post types.

== Description ==

This is a utility for converting lots of posts or pages to a custom post type (or vice versa). You can limit the conversion to posts in a single category or children of specific page. You can also assign new taxonomy terms, which will be added to the posts' existing terms.

This plugin is useful for converting many posts at once. If you'd rather do one at a time, use <a href="http://wordpress.org/extend/plugins/post-type-switcher/">Post Type Switcher</a> instead.

== Installation ==

1. Install a database backup plugin, like <a href="http://www.ilfilosofo.com/blog/wp-db-backup">WP DB Backup</a>. Make a backup of your database, and make sure you know how to restore your site using the backup. I'm not kidding. Do this first!
1. Upload this plugin directory to `/wp-content/plugins/` 
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Tools &rarr; Convert Post Types to convert your posts.

== Screenshots ==

1. The options screen

== Changelog ==

= 1.5 =
* Post parent is now set to zero when converting children of a certain page.
= 1.4 =
* Refactored some code and added WPML support. Props <a href="http://www.jennybeaumont.com/post-type-switcher-wpml-fix/">Jenny Beaumont</a>.
= 1.3.1 =
* Fixed a problem with the "Convert" button overlapping the footer in WP 3.8x
= 1.3 =
* Failing to select a to/from post type now gives an error instead of converting posts to an invalid type.
* Things converted to posts do not receive the default category if other categories are set.
* Flat taxonomies are now set correctly.
* Various query-related bugs fixed, like all pages being converted even if a parent was chosen.
= 1.2.1 =
* Fixed a notice on the admin screen.
= 1.2 =
* Fixed compatibility problem with WordPress 3.3.
* Using built-in functions instead of database queries for better caching and support for hooks.
= 1.1 =
* Removed private post types (like nav menu items) from the dropdown menus to prevent accidents. Only public post types are available for switching.
= 1.0 =
* First release (June 30, 2010)