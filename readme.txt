=== Post Type Switcher ===
Contributors: johnjamesjacoby, beatpanda, norcross, stuttter
Tags: post type
Requires at least: 3.0
Tested up to: 4.6
Stable tag: 2.0.1

A simple way to change a post's type in WordPress

== Description ==

This plugin adds a simple post type dropdown to the post editor interface, allowing you to reassign any post to a new post type. It allows you to switch post's type while editing your post.

= Supported Types =

The plugin can convert nearly every combination of posts, pages, attachments, and even custom post types:

* Page to Post
* Post to Page
* Page to Attachment
* Post to Custom

Invisible post types, such as revisions, menus, etc., are purposely excluded. But, if you need to access invisible post types, you can adjust the boundaries using the 'pts_post_type_filter' filter.

= Bulk Editing =

With bulk editing, thanks to Matthew Gerring, you can select all the posts in a certain type and convert them to a new type with one quick action.

== Installation ==

= Installation =
1. In your WordPress Dashboard go to "Plugins" -> "Add Plugin".
2. Search for "Post Type Swticher".
3. Install the plugin by pressing the "Install" button.
4. Activate the plugin by pressing the "Activate" button.
5. From the post edit screen, above the "Publish" button is the "Post Type" interface.
6. Change post types as needed.

= Minimum Requirements =
* WordPress version 3.0 or greater.
* PHP version 5.2.4 or greater.
* MySQL version 5.0 or greater.

= Recommended Requirements =
* Latest WordPress version.
* PHP version 5.6 or greater.
* MySQL version 5.5 or greater.

== Frequently Asked Questions ==

= Why would I need this? =
You need to selectively change a posts type from one to another.

= Does this ruin my taxonomy associations? =
It should not. This plugin only changes the 'post_type' property of a post.

== Screenshots ==
1. "Type" column in "Posts" screen.
2. "Post Type" interface in "Quick Edit".
3. "Post Type" interface in "Edit Post" screen.

== Changelog ==

= Version 2.0.1 =
* Ensure quick-edit works with new procedure
* Quick-edit "Type" column works again!

= Version 2.0.0 =
* Improved plugin compatibility with WooThemes Sensei
* Filter post arguments vs. hook to save_post
* Add "post_type_switcher" action

= Version 1.7.0 =
* Add support for network activation

= Version 1.6.0 =
* Add textdomains for localization
* Load translation strings using load_plugin_textdomain()
* Before saving data chack if it's not an autosave using wp_is_post_autosave()
* Before saving data chack if it's not a revision using wp_is_post_revision()
* Security: Prevent direct access to directories
* Security: Translation strings escaping
* Add screenshots

= Version 1.5.0 - norcross =
* Fix multiple quickedit dropdowns

= Version 1.4.0 =
* Improve handling of non-public post types

= Version 1.3.0 =
* Fix saving of autodrafts

= Version 1.2.1 =
* Improved WordPress 3.9 integration (added dashicon to publish metabox)

= Version 1.2.0 =
* Add bulk editing to supported post types
* Props Matthew Gerring for bulk edit contribution

= Version 1.1.1 =
* Add is_admin() check to prevent theme-side interference
* Change save_post priority to 999 to avoid plugin compatibility issues
* Remove ending closing php tag
* HTML and PHPDoc improvements

= Version 1.1.0 =
* Fix revisions being nooped
* Fix malformed HTML for some user roles
* Classificationate

= Version 1.0.0 =
* Fix JS bugs
* Audit post save bail conditions
* Tweak UI for WordPress 3.3

= Version 0.3.0 =
* Use the API to change the post type, fixing a conflict with persistent object caches
* No longer requires JavaScript

= Version 0.2.0 =
* Disallow post types that are not public and do not have a visible UI

= Version 0.1.0 =
* Release
