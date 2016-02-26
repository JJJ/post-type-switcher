<?php

/**
 * Post Type Switcher
 *
 * Allow switching of a post type while editing a post (in post publish section)
 *
 * @package Plugins/Admin/Post/TypeSwitcher
 */

/**
 * Plugin Name: Post Type Switcher
 * Plugin URI:  https://wordpress.org/plugins/post-type-switcher/
 * Author:      John James Jacoby
 * Author URI:  https://profiles.wordpress.org/johnjamesjacoby/
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Description: Allow switching of a post type while editing a post (in post publish section)
 * Version:     1.7.0
 * Text Domain: post-type-switcher
 * Domain Path: /assets/lang/
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The main post type switcher class
 *
 * @since 1.0.0
 */
final class Post_Type_Switcher {

	/**
	 * Hook in the basic early actions
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'admin_init',     array( $this, 'admin_init'      ) );
	}

	/**
	 * Load the plugin text domain for translation strings
	 *
	 * @since 1.6.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'post-type-switcher' );
	}

	/**
	 * Setup admin actions
	 *
	 * @since 1.7.0
	 *
	 * @return void
	 */
	public function admin_init() {

		// Bail if page not allowed
		if ( ! $this->is_allowed_page() ) {
			return;
		}

		// Add column for quick-edit support
		add_action( 'manage_posts_columns',        array( $this, 'add_column'       )         );
		add_action( 'manage_pages_columns',        array( $this, 'add_column'       )         );
		add_action( 'manage_posts_custom_column',  array( $this, 'manage_column'    ), 10,  2 );
		add_action( 'manage_pages_custom_column',  array( $this, 'manage_column'    ), 10,  2 );

		// Add UI to "Publish" metabox
		add_action( 'post_submitbox_misc_actions', array( $this, 'metabox'          )         );
		add_action( 'quick_edit_custom_box',       array( $this, 'quickedit'        ), 10,  2 );
		add_action( 'bulk_edit_custom_box',        array( $this, 'quickedit'        ), 10,  2 );
		add_action(	'admin_enqueue_scripts',       array( $this, 'quickedit_script' ), 10,  1 );
		add_action( 'save_post',                   array( $this, 'save_post'        ), 999, 2 ); // Late priority for plugin friendliness
		add_action( 'admin_head',                  array( $this, 'admin_head'       )         );
	}

	/**
	 * pts_metabox()
	 *
	 * Adds post_publish metabox to allow changing post_type
	 *
	 * @since 1.0.0
	 */
	public function metabox() {

		// Allow types to be filtered, just incase you really need to switch
		// between crazy types of posts.
		$args = (array) apply_filters( 'pts_post_type_filter', array(
			'public'  => true,
			'show_ui' => true
		) );

		// Post types
		$post_types = get_post_types( $args, 'objects' );
		$cpt_object = get_post_type_object( get_post_type() );

		// Bail if object does not exist or produces an error
		if ( empty( $cpt_object ) || is_wp_error( $cpt_object ) ) {
			return;
		}

		// Force-add current post type if it's not in the list
		// https://wordpress.org/support/topic/dont-show-for-non-public-post-types?replies=4#post-5849287
		if ( ! in_array( $cpt_object, $post_types, true ) ) {
			$post_types[ get_post_type() ] = $cpt_object;
		} ?>

		<div class="misc-pub-section misc-pub-section-last post-type-switcher">
			<label for="pts_post_type"><?php esc_html_e( 'Post Type:', 'post-type-switcher' ); ?></label>
			<span id="post-type-display"><?php echo esc_html( $cpt_object->labels->singular_name ); ?></span>

			<?php if ( current_user_can( $cpt_object->cap->publish_posts ) ) : ?>

				<a href="#" id="edit-post-type-switcher" class="hide-if-no-js"><?php esc_html_e( 'Edit', 'post-type-switcher' ); ?></a>

				<?php wp_nonce_field( 'post-type-selector', 'pts-nonce-select' ); ?>

				<div id="post-type-select">
					<select name="pts_post_type" id="pts_post_type">

						<?php foreach ( $post_types as $post_type => $pt ) : ?>

							<?php if ( ! current_user_can( $pt->cap->publish_posts ) ) :
								continue;
							endif; ?>

							<option value="<?php echo esc_attr( $pt->name ); ?>" <?php selected( get_post_type(), $post_type ); ?>><?php echo esc_html( $pt->labels->singular_name ); ?></option>

						<?php endforeach; ?>

					</select>
					<a href="#" id="save-post-type-switcher" class="hide-if-no-js button"><?php esc_html_e( 'OK', 'post-type-switcher' ); ?></a>
					<a href="#" id="cancel-post-type-switcher" class="hide-if-no-js"><?php esc_html_e( 'Cancel', 'post-type-switcher' ); ?></a>
				</div>

			<?php endif; ?>

		</div>

	<?php
	}

	/**
	 * Adds the post type column
	 *
	 * @since 1.2.0
	 */
	public function add_column( $columns ) {
		return array_merge( $columns,  array( 'post_type' => esc_html__( 'Type', 'post-type-switcher' ) ) );
	}

	/**
	 * Manages the post type column
	 *
	 * @since 1.1.1
	 */
	public function manage_column( $column, $post_id ) {
		switch( $column ) {
			case 'post_type' :
				$post_type = get_post_type_object( get_post_type( $post_id ) ); ?>

				<span data-post-type="<?php echo esc_attr( $post_type->name ); ?>"><?php echo esc_html( $post_type->labels->singular_name ); ?></span>

				<?php
				break;
		}
	}

	/**
	 * Adds quickedit button for bulk-editing post types
	 *
	 * @since 1.2.0
	 */
	public function quickedit( $column_name, $post_type ) {

		// Bail to prevent multiple dropdowns in each column
		if ( 'post_type' !== $column_name ) {
			return;
		} ?>

		<fieldset class="inline-edit-col-right">
			<div class="inline-edit-col">
				<label class="alignleft">
					<span class="title"><?php esc_html_e( 'Post Type', 'post-type-switcher' ); ?></span>
					<?php wp_nonce_field( 'post-type-selector', 'pts-nonce-select' ); ?>
					<?php $this->select_box(); ?>
				</label>
			</div>
		</fieldset>

	<?php
	}

	/**
	 * Adds quickedit script for getting values into quickedit box
	 *
	 * @since 1.2
	 */
	public function quickedit_script( $hook = '' ) {

		if ( 'edit.php' !== $hook ) {
			return;
		}

		wp_enqueue_script( 'pts_quickedit', plugin_dir_url( __FILE__ ) . 'assets/js/quickedit.js', array( 'jquery' ), '', true );
	}

	/**
	 * Output a post-type dropdown
	 *
	 * @since 1.2
	 */
	public function select_box() {
		$args = (array) apply_filters( 'pts_post_type_filter', array(
			'public'  => true,
			'show_ui' => true
		) );
		$post_types = get_post_types( $args, 'objects' ); ?>

		<select name="pts_post_type" id="pts_post_type">

			<?php foreach ( $post_types as $post_type => $pt ) : ?>

				<?php if ( ! current_user_can( $pt->cap->publish_posts ) ) :
					continue;
				endif; ?>

				<option value="<?php echo esc_attr( $pt->name ); ?>" <?php selected( get_post_type(), $post_type ); ?>><?php echo esc_html( $pt->labels->singular_name ); ?></option>

			<?php endforeach; ?>

		</select>

	<?php
	}

	/**
	 * Set the post type on save_post but only when editing
	 *
	 * We do a bunch of sanity checks here, to make sure we're only changing the
	 * post type when the user explicitly intends to.
	 *
	 * - Not during autosave
	 * - Check nonce
	 * - Check user capabilities
	 * - Check $_POST input name
	 * - Check if revision or current post-type
	 * - Check new post-type exists
	 * - Check that user can publish posts of new type
	 *
	 * @since 1.0.0
	 *
	 * @param  int     $post_id
	 * @param  object  $post
	 *
	 * @return If any number of condtions are met
	 */
	public function save_post( $post_id, $post ) {

		// Post type information.
		$post_type        = $_REQUEST['pts_post_type'];
		$post_type_object = get_post_type_object( $post_type );

		// Add nonce for security and authentication.
		$nonce_name   = $_REQUEST['pts-nonce-select'];
		$nonce_action = 'post-type-selector';

		// Check if a nonce is set.
		if ( ! isset( $nonce_name ) ) {
			return;
		}

		// Check if a nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
			return;
		}

		// Check if the user has permissions to 'edit_post'.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if it's not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if it's not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Check if a post type is set.
		if ( empty( $post_type ) ) {
			return;
		}

		// Check if a post type object is set.
		if ( empty( $post_type_object ) ) {
			return;
		}

		// Check if it's not a revision.
		if ( in_array( $post->post_type, array( $post_type, 'revision' ), true ) ) {
			return;
		}

		// Check if the user has permissions to 'publish_posts'.
		if ( ! current_user_can( $post_type_object->cap->publish_posts ) ) {
			return;
		}

		// Set the new post type.
		set_post_type( $post_id, $post_type_object->name );
	}

	/**
	 * Adds needed JS and CSS to admin header
	 *
	 * @since 1.0.0
	 *
	 * @return If on post-new.php
	 */
	public function admin_head() {
	?>

		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				jQuery( '.misc-pub-section.curtime.misc-pub-section-last' ).removeClass( 'misc-pub-section-last' );
				jQuery( '#edit-post-type-switcher' ).on( 'click', function(e) {
					jQuery( this ).hide();
					jQuery( '#post-type-select' ).slideDown();
					e.preventDefault();
				});
				jQuery( '#save-post-type-switcher' ).on( 'click', function(e) {
					jQuery( '#post-type-select' ).slideUp();
					jQuery( '#edit-post-type-switcher' ).show();
					jQuery( '#post-type-display' ).text( jQuery( '#pts_post_type :selected' ).text() );
					e.preventDefault();
				});
				jQuery( '#cancel-post-type-switcher' ).on( 'click', function(e) {
					jQuery( '#post-type-select' ).slideUp();
					jQuery( '#edit-post-type-switcher' ).show();
					e.preventDefault();
				});
			});
		</script>
		<style type="text/css">
			#post-type-select {
				line-height: 2.5em;
				margin-top: 3px;
				display: none;
			}
			#post-type-select select#pts_post_type {
				margin-right: 2px;
			}
			#post-type-select a#save-post-type-switcher {
				vertical-align: middle;
				margin-right: 2px;
			}
			#post-type-display {
				font-weight: bold;
			}
			#post-body .post-type-switcher::before {
				content: '\f109';
				font: 400 20px/1 dashicons;
				speak: none;
				display: inline-block;
				padding: 0 2px 0 0;
				top: 0;
				left: -1px;
				position: relative;
				vertical-align: top;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
				text-decoration: none !important;
				color: #888;
			}
		</style>

	<?php
	}

	/**
	 * Whether or not the current file requires the post type switcher
	 *
	 * @since 1.1.0
	 *
	 * @return bool True if it should load, false if not
	 */
	private static function is_allowed_page() {

		// Only for admin area
		if ( ! is_blog_admin() ) {
			return false;
		}

		// Allowed admin pages
		$pages = apply_filters( 'pts_allowed_pages', array(
			'post.php',
			'edit.php',
			'admin-ajax.php'
		) );

		// Only show switcher when editing
		return (bool) in_array( $GLOBALS['pagenow'], $pages, true );
	}
}
new Post_Type_Switcher();
