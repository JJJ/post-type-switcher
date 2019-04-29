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
 * Version:     3.1.1
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
	 * Asset version, for cache busting
	 *
	 * @since 3.0.1
	 *
	 * @var int
	 */
	private $asset_version = 201802160001;

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
		$post_type_names = get_post_types( $this->get_post_type_args(), 'names' );
		foreach ( $post_type_names as $name ) {
			add_filter( "manage_{$name}_posts_columns",       array( $this, 'add_column'    )        );
			add_action( "manage_{$name}_posts_custom_column", array( $this, 'manage_column' ), 10, 2 );
		}

		// Add UI to "Publish" metabox
		add_action( 'admin_head',                  array( $this, 'admin_head'          ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'metabox'             ) );
		add_action( 'quick_edit_custom_box',       array( $this, 'quick_edit'          ) );
		add_action( 'bulk_edit_custom_box',        array( $this, 'quick_edit_bulk'     ) );
		add_action( 'admin_enqueue_scripts',       array( $this, 'quick_edit_script'   ) );

		// Add UI to the block editor
		add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_assets' ) );

		// Override
		add_action( 'wp_ajax_post_type_switcher', array( $this, 'handle_ajax' ) );
		add_filter( 'wp_insert_attachment_data',  array( $this, 'override_type' ), 10, 2 );
		add_filter( 'wp_insert_post_data',        array( $this, 'override_type' ), 10, 2 );

		// Pass object into an action
		do_action( 'post_type_switcher', $this );
	}

	/**
	 * Output meta box fields on New/Edit Post screen
	 *
	 * @since 1.0.0
	 */
	public function metabox() {

		// Post types
		$post_type  = get_post_type();
		$post_types = get_post_types( $this->get_post_type_args(), 'objects' );
		$cpt_object = get_post_type_object( $post_type );

		// Bail if object does not exist or produces an error
		if ( empty( $cpt_object ) || is_wp_error( $cpt_object ) ) {
			return;
		}

		// Force-add current post type if it's not in the list
		// https://wordpress.org/support/topic/dont-show-for-non-public-post-types?replies=4#post-5849287
		if ( ! in_array( $cpt_object, $post_types, true ) ) {
			$post_types[ $post_type ] = $cpt_object;
		}

		// Unset attachment types, since support seems to be broken
		if ( isset( $post_types['attachment'] ) ) {
			unset( $post_types['attachment'] );
		}

		?><div class="misc-pub-section misc-pub-section-last post-type-switcher">
			<label for="pts_post_type"><?php esc_html_e( 'Post Type:', 'post-type-switcher' ); ?></label>
			<span id="post-type-display"><?php echo esc_html( $cpt_object->labels->singular_name ); ?></span>

			<?php if ( current_user_can( $cpt_object->cap->publish_posts ) ) : ?>

				<a href="#" id="edit-post-type-switcher" class="hide-if-no-js"><?php esc_html_e( 'Edit', 'post-type-switcher' ); ?></a>
				<div id="post-type-select">
					<select name="pts_post_type" id="pts_post_type"><?php

						foreach ( $post_types as $_post_type => $pt ) :

							if ( ! current_user_can( $pt->cap->publish_posts ) ) :
								continue;
							endif;

							?><option value="<?php echo esc_attr( $pt->name ); ?>" <?php selected( $post_type, $_post_type ); ?>><?php echo esc_html( $pt->labels->singular_name ); ?></option><?php

						endforeach;

					?></select>
					<a href="#" id="save-post-type-switcher" class="hide-if-no-js button"><?php esc_html_e( 'OK', 'post-type-switcher' ); ?></a>
					<a href="#" id="cancel-post-type-switcher" class="hide-if-no-js"><?php esc_html_e( 'Cancel', 'post-type-switcher' ); ?></a>
				</div><?php

				wp_nonce_field( 'post-type-selector', 'pts-nonce-select' );

			endif;

		?></div>

	<?php
	}

	/**
	 * Adds the post type column
	 *
	 * @since 1.2.0
	 */
	public function add_column( $columns ) {
		return array_merge( $columns, array( 'post_type' => esc_html__( 'Type', 'post-type-switcher' ) ) );
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
	public function quick_edit( $column_name = '' ) {

		// Bail to prevent multiple dropdowns in each column
		if ( 'post_type' !== $column_name ) {
			return;
		} ?>

		<div id="pts_quick_edit" class="inline-edit-group wp-clearfix">
			<label class="alignleft">
				<span class="title"><?php esc_html_e( 'Post Type', 'post-type-switcher' ); ?></span><?php

				wp_nonce_field( 'post-type-selector', 'pts-nonce-select' );

				$this->select_box();

			?></label>
		</div>

	<?php
	}

	/**
	 * Adds quickedit button for bulk-editing post types
	 *
	 * @since 1.2.0
	 */
	public function quick_edit_bulk( $column_name = '' ) {

		// Bail to prevent multiple dropdowns in each column
		if ( 'post_type' !== $column_name ) {
			return;
		} ?>

		<label id="pts_bulk_edit" class="alignleft">
			<span class="title"><?php esc_html_e( 'Post Type', 'post-type-switcher' ); ?></span><?php

			wp_nonce_field( 'post-type-selector', 'pts-nonce-select' );

			$this->select_box( true );

		?></label>

	<?php
	}

	/**
	 * Adds quickedit script for getting values into quickedit box
	 *
	 * @since 1.2
	 */
	public function quick_edit_script( $hook = '' ) {

		// Bail if not edit.php admin page
		if ( 'edit.php' !== $hook ) {
			return;
		}

		// Enqueue quick edit JS
		wp_enqueue_script( 'pts_quickedit', plugin_dir_url( __FILE__ ) . 'assets/js/quickedit.js', array( 'jquery' ), $this->asset_version, true );
	}

	/**
	 * Enqueues modifications to the block editor.
	 *
	 * @since 3.2
	 */
	public function block_editor_assets() {

		$current_post_type        = get_post_type();
		$current_post_type_object = get_post_type_object( $current_post_type );
		if ( ! current_user_can( $current_post_type_object->cap->publish_posts ) ) {
			return;
		}

		wp_enqueue_script(
			'pts_blockeditor',
			plugin_dir_url( __FILE__ ) . 'build/index.js',
			array( 'wp-components', 'wp-edit-post', 'wp-element', 'wp-i18n', 'wp-plugins' ),
			$this->asset_version
		);
		$args                 = $this->get_post_type_args();
		$available_post_types = array();
		foreach ( get_post_types( $args, 'objects' ) as $post_type ) {
			if ( ! current_user_can( $post_type->cap->publish_posts ) ) {
				continue;
			}
			$available_post_types[] = array(
				'value' => $post_type->name,
				'label' => $post_type->labels->singular_name,
			);
		}
		$change_url = add_query_arg(
			array(
				'action'           => 'post_type_switcher',
				'pts-nonce-select' => wp_create_nonce( 'post-type-selector' ),
				'post_id'          => get_the_ID(),
			),
			admin_url( 'admin-ajax.php' )
		);
		wp_localize_script(
			'pts_blockeditor',
			'ptsBlockEditor',
			array(
				'availablePostTypes'   => $available_post_types,
				'currentPostType'      => $current_post_type,
				'currentPostTypeLabel' => $current_post_type_object->labels->singular_name,
				'changeUrl'            => $change_url,
			)
		);
	}

	/**
	 * Output a post-type dropdown
	 *
	 * @since 1.2
	 */
	public function select_box( $bulk = false ) {

		// Get post type specific data
		$args       = $this->get_post_type_args();
		$post_types = get_post_types( $args, 'objects' );
		$post_type  = get_post_type();
		$selected   = '';

		// Unset attachment types, since support seems to be broken
		if ( isset( $post_types['attachment'] ) ) {
			unset( $post_types['attachment'] );
		}

		// Start an output buffer
		ob_start();

		// Output
		?><select name="pts_post_type" id="pts_post_type"><?php

			// Maybe include "No Change" option for bulk
			if ( true === $bulk ) :
				?><option value="-1"><?php esc_html_e( '&mdash; No Change &mdash;', 'post-type-switcher' ); ?></option><?php
			endif;

			// Loop through post types
			foreach ( $post_types as $_post_type => $pt ) :

				// Skip if user cannot publish this type of post
				if ( ! current_user_can( $pt->cap->publish_posts ) ) :
					continue;
				endif;

				// Only select if not bulk
				if ( false === $bulk ) :
					$selected = selected( $post_type, $_post_type );
				endif;

				// Output option
				?><option value="<?php echo esc_attr( $pt->name ); ?>" <?php echo $selected; // Do not escape ?>><?php echo esc_html( $pt->labels->singular_name ); ?></option><?php

			endforeach;

		?></select><?php

		// Output the current buffer
		echo ob_get_clean();
	}

	/**
	 * Handles an admin-ajax request to change post types.
	 */
	public function handle_ajax() {
		if ( empty( $_GET['pts_post_type'] )
			|| empty( $_GET['pts-nonce-select'] )
			|| empty( $_GET['post_id'] ) ) {
			return wp_die( __( 'Missing data.', 'pts' ) );
		}

		// Post type information
		$post_id          = absint( $_GET['post_id'] );
		$post_type        = sanitize_key( $_GET['pts_post_type'] );
		$post_type_object = get_post_type_object( $post_type );

		if ( ! current_user_can( $post_type_object->cap->publish_posts )
			|| ! wp_verify_nonce( $_GET['pts-nonce-select'], 'post-type-selector' ) ) {
			return wp_die( __( 'Sorry, you cannot do this.', 'pts' ) );
		}

		wp_update_post(
			array(
				'ID'        => $post_id,
				'post_type' => $post_type,
			)
		);
		wp_safe_redirect( get_edit_post_link( $post_id, 'raw' ) );
		exit;
	}

	/**
	 * Override post_type in wp_insert_post()
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
	 * @since 2.0.0
	 *
	 * @param  array  $data
	 * @param  array  $postarr
	 *
	 * @return Maybe modified $data
	 */
	public function override_type( $data = array(), $postarr = array() ) {

		// Bail if form field is missing
		if ( empty( $_REQUEST['pts_post_type'] ) || empty( $_REQUEST['pts-nonce-select'] ) ) {
			return $data;
		}

		// Bail if no specific post ID is being saved
		if ( empty( $postarr['post_ID'] ) ) {
			return $data;
		}

		// Post type information
		$post_id          = absint( $postarr['post_ID'] );
		$post_type        = sanitize_key( $_REQUEST['pts_post_type'] );
		$post_type_object = get_post_type_object( $post_type );

		// Bail if empty post type
		if ( empty( $post_id ) || empty( $post_type ) || empty( $post_type_object ) ) {
			return $data;
		}

		// Bail if no change
		if ( $post_type === $data['post_type'] ) {
			return $data;
		}

		// Bail if posted ID to update does not match the post ID being changed.
		// This is to prevent child posts (or related) from also being changed.
		// See: https://github.com/JJJ/post-type-switcher/issues/9
		if ( $post_id !== $postarr['ID'] ) {
			return $data;
		}

		// Bail if user cannot 'edit_post' on the current post ID
		if ( ! current_user_can( 'edit_post', $postarr['ID'] ) ) {
			return $data;
		}

		// Bail if user cannot 'publish_posts' on the new type
		if ( ! current_user_can( $post_type_object->cap->publish_posts ) ) {
			return $data;
		}

		// Bail if nonce is invalid
		if ( ! wp_verify_nonce( $_REQUEST['pts-nonce-select'], 'post-type-selector' ) ) {
			return $data;
		}

		// Bail if autosave
		if ( wp_is_post_autosave( $postarr['ID'] ) ) {
			return $data;
		}

		// Bail if revision
		if ( wp_is_post_revision( $postarr['ID'] ) ) {
			return $data;
		}

		// Bail if it's a revision
		if ( in_array( $postarr['post_type'], array( $post_type, 'revision' ), true ) ) {
			return $data;
		}

		// Update post type
		$data['post_type'] = $post_type;

		// Return modified post data
		return $data;
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
			#wpbody-content .inline-edit-row .inline-edit-col-right .alignleft + .alignleft {
				float: right;
			}
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
			.wp-list-table .column-post_type {
				width: 10%;
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
		if ( is_blog_admin()
			|| ( defined( 'DOING_AJAX' ) && DOING_AJAX && ( ! empty( $_REQUEST['action'] ) && ( in_array( $_REQUEST['action'], array( 'inline-save', 'post_type_switcher' ), true ) ) ) ) ) {

			// Allowed admin pages
			$pages = apply_filters( 'pts_allowed_pages', array(
				'post.php',
				'edit.php',
				'admin-ajax.php'
			) );

			// Only show switcher when editing
			return (bool) in_array( $GLOBALS['pagenow'], $pages, true );
		}

		// Default to false
		return false;
	}

	/**
	 * Allow types to be filtered, just incase you really need to switch between
	 * crazy types of posts.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	private function get_post_type_args() {
		return (array) apply_filters( 'pts_post_type_filter', array(
			'public'  => true,
			'show_ui' => true
		) );
	}
}
new Post_Type_Switcher();
