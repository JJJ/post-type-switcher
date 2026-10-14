<?php

declare(strict_types=1);

define( 'ABSPATH', dirname( __DIR__ ) . '/' );

if ( ! class_exists( 'WP_Post_Type' ) ) {
	class WP_Post_Type {
		public $name;
		public $labels;
		public $cap;

		public function __construct( $name, $label = '', $publish_capability = 'publish_posts' ) {
			$this->name   = $name;
			$this->labels = (object) array( 'singular_name' => $label ?: ucfirst( $name ) );
			$this->cap    = (object) array( 'publish_posts' => $publish_capability );
		}
	}
}

$GLOBALS['pts_test'] = array();

function pts_test_reset() {
	$GLOBALS['pts_test'] = array(
		'actions'   => array(),
		'filters'   => array(),
		'calls'     => array(),
		'callbacks' => array(),
	);
}

function pts_test_call( $name, $arguments, $default = null ) {
	$GLOBALS['pts_test']['calls'][ $name ][] = $arguments;

	if ( isset( $GLOBALS['pts_test']['callbacks'][ $name ] ) ) {
		return $GLOBALS['pts_test']['callbacks'][ $name ]( ...$arguments );
	}

	return $default;
}

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
	$GLOBALS['pts_test']['actions'][] = array( $hook, $callback, $priority, $accepted_args );
}

function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
	$GLOBALS['pts_test']['filters'][] = array( $hook, $callback, $priority, $accepted_args );
}

function do_action( $hook, ...$arguments ) {
	return pts_test_call( 'do_action:' . $hook, $arguments );
}

function apply_filters( $hook, $value, ...$arguments ) {
	return pts_test_call( 'apply_filters:' . $hook, array_merge( array( $value ), $arguments ), $value );
}

function load_plugin_textdomain( ...$arguments ) {
	return pts_test_call( __FUNCTION__, $arguments, true );
}

function plugin_basename( $file ) {
	return basename( $file );
}

function plugin_dir_url( $file ) {
	return 'https://example.test/plugins/post-type-switcher/';
}

function is_blog_admin() {
	return pts_test_call( __FUNCTION__, array(), true );
}

function wp_doing_ajax() {
	return pts_test_call( __FUNCTION__, array(), false );
}

function get_post_types( $arguments, $output ) {
	return pts_test_call( __FUNCTION__, array( $arguments, $output ), array() );
}

function get_post_type( $post_id = null ) {
	return pts_test_call( __FUNCTION__, array( $post_id ), 'post' );
}

function get_post_type_object( $post_type ) {
	return pts_test_call( __FUNCTION__, array( $post_type ), new WP_Post_Type( (string) $post_type ) );
}

function current_user_can( ...$arguments ) {
	return pts_test_call( __FUNCTION__, $arguments, true );
}

function wp_verify_nonce( ...$arguments ) {
	return pts_test_call( __FUNCTION__, $arguments, true );
}

function wp_is_post_autosave( $post_id ) {
	return pts_test_call( __FUNCTION__, array( $post_id ), false );
}

function wp_is_post_revision( $post_id ) {
	return pts_test_call( __FUNCTION__, array( $post_id ), false );
}

function sanitize_key( $value ) {
	return strtolower( preg_replace( '/[^a-z0-9_-]/i', '', (string) $value ) );
}

function absint( $value ) {
	return abs( (int) $value );
}

function esc_html__( $value ) {
	return $value;
}

function esc_html( $value ) {
	return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
}

function esc_html_e( $value ) {
	echo htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
}

function esc_attr( $value ) {
	return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
}

function selected( $selected, $current, $display = true ) {
	$result = (string) $selected === (string) $current ? 'selected="selected"' : '';
	if ( $display ) {
		echo $result;
	}
	return $result;
}

function get_post_meta( ...$arguments ) {
	return pts_test_call( __FUNCTION__, $arguments, '' );
}

function add_post_meta( ...$arguments ) {
	return pts_test_call( __FUNCTION__, $arguments, true );
}

function update_post_meta( ...$arguments ) {
	return pts_test_call( __FUNCTION__, $arguments, true );
}

function delete_post_meta( ...$arguments ) {
	return pts_test_call( __FUNCTION__, $arguments, true );
}

function set_post_type( ...$arguments ) {
	return pts_test_call( __FUNCTION__, $arguments, 1 );
}

pts_test_reset();
require_once dirname( __DIR__ ) . '/post-type-switcher.php';

foreach ( $GLOBALS['pts_test']['actions'] as $action ) {
	if ( 'init' === $action[0] && is_array( $action[1] ) && $action[1][0] instanceof Post_Type_Switcher ) {
		$GLOBALS['pts_test_plugin'] = $action[1][0];
		break;
	}
}

if ( empty( $GLOBALS['pts_test_plugin'] ) ) {
	throw new RuntimeException( 'Unable to locate the Post Type Switcher instance.' );
}
