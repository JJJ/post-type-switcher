# Post Type Switcher

The simplest way to change a post's type in WordPress

## Description

This plugin adds a simple post type dropdown to the post editor interface, allowing you to reassign any post to a new post type. It allows you to switch a post's type while editing your post.

### Supported Types

The plugin can convert nearly every combination of posts, pages, and even custom post types:

* Page to Post
* Post to Page
* Post to Custom
* Custom to Custom
* Media to Post, Page, or Custom

Attachments can be switched to another supported post type from the extended Edit Media screen. A converted attachment with WordPress's normal `inherit` status becomes a draft so it remains available in the target post type. Attachment is not available as a target because ordinary posts do not have the file, MIME type, and metadata that WordPress expects from media items.

Invisible post types, such as revisions and menus, are purposely excluded. If you need to access invisible post types, you can adjust the boundaries using the `pts_post_type_filter` filter.

### Bulk Editing

With bulk editing (thanks to Matthew Gerring) you can select all the posts in a certain type and convert them to a new type with one quick action.

### Block Editor

With Block Editor support (thanks to Daniel Bachhuber) you can switch between post types that use either the Block Editor or Classic Editor without losing any of your embedded content.

## Installation

1. In your WordPress Dashboard go to "Plugins" -> "Add Plugin".
2. Search for "Post Type Switcher".
3. Install the plugin by pressing the "Install" button.
4. Activate the plugin by pressing the "Activate" button.
5. From the post edit screen, above the "Publish" button is the "Post Type" interface.
6. Change post types as needed.

### Minimum Requirements
* WordPress version 6.2 or greater.
* PHP version 8.0 or greater.
* MySQL version 5.7 or greater.

### Recommended Requirements
* Latest WordPress version.
* PHP version 8.0 or greater.
* MySQL version 8.0 or greater, or MariaDB 10.5 or greater.

## Frequently Asked Questions

### Why would I need this?
You need to selectively change a post's type from one to another.

### Does this ruin my taxonomy associations?
It should not. This plugin preserves taxonomy associations while changing a post's type. When an attachment uses WordPress's internal `inherit` status, converting it to another type also changes its status to `draft` so the converted item remains editable.

### Does this ruin block-editor content?
It should not. Be careful when editing HTML content while switching between editor types!

### Will this delete my content?
No. This plugin does not include any code capable of deleting anything.

Plugin conflicts are not unheard of and can be difficult to troubleshoot.

If you're worried, backup your database and deactivate all other plugins before using this tool.

If you're still worried, ask for help in the WordPress.org support forums.

### Support
* Community: https://wordpress.org/support/plugin/post-type-switcher
* Development: https://github.com/jjj/post-type-switcher/discussions

## Screenshots
1. "Type" column in the Posts screen.
2. "Post Type" interface in Quick Edit.
3. "Post Type" chooser in the Block Editor.
