# Post Type Switcher

The simplest way to change a post's type in WordPress

## Description

This plugin adds a simple post type dropdown to the post editor interface, allowing you to reassign any post to a new post type. It allows you to switch post's type while editing your post.

### Supported Types

The plugin can convert nearly every combination of posts, pages, and even custom post types:

* Page to Post
* Post to Page
* Post to Custom

Invisible post types, such as revisions, menus, etc., are purposely excluded. Attachments are also currently excluded, due to some bugs and conflicts.

If you need to access invisible post types, you can adjust the boundaries using the `pts_post_type_filter` filter.

If you are experiencing a conflict with a post-type from another plugin, please accept my apologies in adavnce, and open an issue including as much information as you can. Your content is very important to me, and I want to keep it safe.

### Bulk Editing

With bulk editing (thanks to Matthew Gerring) you can select all the posts in a certain type and convert them to a new type with one quick action.

### Block Editor

With block-editor (aka Gutenberg) support (thanks to Daniel Bachhuber) you can switch between post-types that use either the Block Editor and the Classic one, without losing any of your embedded content.

## Installation

### Installation
1. In your WordPress Dashboard go to "Plugins" -> "Add Plugin".
2. Search for "Post Type Swticher".
3. Install the plugin by pressing the "Install" button.
4. Activate the plugin by pressing the "Activate" button.
5. From the post edit screen, above the "Publish" button is the "Post Type" interface.
6. Change post types as needed.

### Minimum Requirements
* WordPress version 3.0 or greater.
* PHP version 5.2.4 or greater.
* MySQL version 5.0 or greater.

### Recommended Requirements
* Latest WordPress version.
* PHP version 5.6 or greater.
* MySQL version 5.5 or greater.

## Frequently Asked Questions

### Why would I need this?
You need to selectively change a posts type from one to another.

### Does this ruin my taxonomy associations?
It should not. This plugin only changes the 'post_type' property of a post.

## Screenshots
1. "Type" column in "Posts" screen.
2. "Post Type" interface in "Quick Edit".
3. "Post Type" interface in "Edit Post" screen.
