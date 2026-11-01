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
