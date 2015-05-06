=== Post Type Switcher ===
Contributors: johnjamesjacoby, beatpanda
Tags: post type
Requires at least: 3.0
Tested up to: 4.2
Stable tag: 1.5

A simple way to change a post's type in WordPress

== Description ==

Any combination is possible, even custom post types:

* Page to Post
* Post to Page
* Page to Attachment
* Post to Custom

Note: Invisible post types (revisions, menus, etc...) are purposely excluded. Filter 'pts_post_type_filter' to adjust the boundaries.

Now with bulk editing, thanks to Matthew Gerring!

== Changelog ==

= Version 1.5 - norcross =
* Fix multiple quickedit dropdowns

= Version 1.4 =
* Improve handling of non-public post types

= Version 1.3 =
* Fix saving of autodrafts

= Version 1.2.1 =
* Improved WordPress 3.9 integration (added dashicon to publish metabox)

= Version 1.2 =
* Add bulk editing to supported post types
* Props Matthew Gerring for bulk edit contribution

= Version 1.1.1 =
* Add is_admin() check to prevent theme-side interference
* Change save_post priority to 999 to avoid plugin compatibility issues
* Remove ending closing php tag
* HTML and PHPDoc improvements

= Version 1.1 =
* Fix revisions being nooped
* Fix malformed HTML for some user roles
* Classificationate

= Version 1.0 =
* Fix JS bugs
* Audit post save bail conditions
* Tweak UI for WordPress 3.3

= Version 0.3 =
* Use the API to change the post type, fixing a conflict with persistent object caches
* No longer requires JavaScript

= Version 0.2 =
* Disallow post types that are not public and do not have a visible UI

= Version 0.1 =
* Release

== Installation ==

* Install the plugin into the plugins/post-type-swticher directory, and activate.
* From the post edit screen, above the "Publish" button is the "Post Type" interface.
* Change post types as needed.

== Frequently Asked Questions ==

= Why would I need this? =
You need to selectively change a posts type from one to another.

= Does this ruin my taxonomy associations? =
It should not. This plugin only changes the 'post_type' property of a post.
