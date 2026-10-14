<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class HookRegistrationTest extends TestCase {
	protected function setUp(): void {
		pts_test_reset();
	}

	public function test_init_registers_existing_admin_hooks(): void {
		$GLOBALS['pts_test_plugin']->init();

		$this->assertContains(
			array( 'admin_init', array( $GLOBALS['pts_test_plugin'], 'admin_init' ), 10, 1 ),
			$GLOBALS['pts_test']['actions']
		);
		$this->assertContains(
			array( 'admin_init', array( $GLOBALS['pts_test_plugin'], 'admin_done' ), 10, 1 ),
			$GLOBALS['pts_test']['actions']
		);
		$this->assertArrayNotHasKey( 'load_plugin_textdomain', $GLOBALS['pts_test']['calls'] );
	}

	public function test_admin_init_preserves_switching_and_compatibility_hooks(): void {
		$GLOBALS['pagenow'] = 'post.php';
		$GLOBALS['pts_test']['callbacks']['get_post_types'] = static function ( $arguments, $output ) {
			return 'names' === $output ? array( 'post' => 'post', 'page' => 'page' ) : array();
		};

		$GLOBALS['pts_test_plugin']->admin_init();

		$this->assertContains(
			array( 'wp_ajax_post_type_switcher', array( $GLOBALS['pts_test_plugin'], 'handle_ajax' ), 10, 1 ),
			$GLOBALS['pts_test']['actions']
		);
		$this->assertContains(
			array( 'post_type_after_switch', array( $GLOBALS['pts_test_plugin'], 'wpml_sync_type' ), 10, 3 ),
			$GLOBALS['pts_test']['actions']
		);
		$this->assertContains(
			array( 'wp_insert_post_data', array( $GLOBALS['pts_test_plugin'], 'override_type' ), 10, 2 ),
			$GLOBALS['pts_test']['filters']
		);
	}
}
