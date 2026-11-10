<?php
/**
 * WordPress function stubs for Attachment source tests.
 *
 * @package Post_Type_Switcher
 */

/**
 * Record a post-field lookup.
 *
 * @param mixed ...$arguments Function arguments.
 * @return mixed Stubbed result.
 */
function get_post_field( ...$arguments ) {
	return pts_test_call( __FUNCTION__, $arguments, '' );
}

/**
 * Render the selector nonce fixture.
 *
 * @param mixed ...$arguments Function arguments.
 */
function wp_nonce_field( ...$arguments ) {
	pts_test_call( __FUNCTION__, $arguments );

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fixed test fixture HTML.
	echo '<input type="hidden" name="pts-nonce-select" value="valid" />';
}

/**
 * Record a post update.
 *
 * @param mixed ...$arguments Function arguments.
 * @return mixed Stubbed result.
 */
function wp_update_post( ...$arguments ) {
	return pts_test_call( __FUNCTION__, $arguments, $arguments[0]['ID'] ?? 0 );
}

/**
 * Record an edit-link lookup.
 *
 * @param mixed ...$arguments Function arguments.
 * @return mixed Stubbed result.
 */
function get_edit_post_link( ...$arguments ) {
	return pts_test_call( __FUNCTION__, $arguments, 'https://example.test/wp-admin/post.php?post=84&action=edit' );
}

/**
 * Record a safe redirect.
 *
 * @param mixed ...$arguments Function arguments.
 * @return mixed Stubbed result.
 */
function wp_safe_redirect( ...$arguments ) {
	return pts_test_call( __FUNCTION__, $arguments, true );
}

if ( ! class_exists( 'SitePress' ) ) {
	/**
	 * Minimal WPML fixture.
	 */
	class SitePress {
	}
}
