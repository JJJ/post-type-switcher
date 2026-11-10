<?php
/**
 * Attachment source behavior tests.
 *
 * @package Post_Type_Switcher
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/class-sitepress.php';
require_once __DIR__ . '/attachment-source-stubs.php';

/**
 * Verify Attachment source behavior.
 */
final class AttachmentSourceTest extends TestCase {
	/**
	 * Reset shared test state.
	 */
	protected function setUp(): void {
		pts_test_reset();
		$_REQUEST = array();
		$_GET     = array();
		unset( $GLOBALS['sitepress'], $GLOBALS['wpdb'] );
	}

	/**
	 * Verify Attachment-specific hooks are registered.
	 */
	public function test_attachment_hooks_are_registered(): void {
		$GLOBALS['pagenow'] = 'post.php';

		$GLOBALS['pts_test']['callbacks']['get_post_types'] = static function ( $arguments, $output ) {
			return 'names' === $output
				? array(
					'post' => 'post',
					'page' => 'page',
				)
				: array();
		};

		$GLOBALS['pts_test_plugin']->admin_init();

		$this->assertContains(
			array( 'attachment_submitbox_misc_actions', array( $GLOBALS['pts_test_plugin'], 'metabox' ), 10, 1 ),
			$GLOBALS['pts_test']['actions']
		);
		$this->assertContains(
			array( 'wp_insert_attachment_data', array( $GLOBALS['pts_test_plugin'], 'override_type' ), 10, 2 ),
			$GLOBALS['pts_test']['filters']
		);
	}

	/**
	 * Verify an attachment can switch out without becoming a target.
	 */
	public function test_attachment_metabox_supports_switching_out_without_exposing_attachment_as_a_target(): void {
		$GLOBALS['pts_test']['callbacks']['get_post_types'] = static function () {
			return array(
				'post'       => new WP_Post_Type( 'post', 'Post' ),
				'page'       => new WP_Post_Type( 'page', 'Page' ),
				'attachment' => new WP_Post_Type( 'attachment', 'Media' ),
			);
		};

		$GLOBALS['pts_test']['callbacks']['get_post_type'] = static function () {
			return 'attachment';
		};

		$GLOBALS['pts_test']['callbacks']['get_post_type_object'] = static function () {
			return new WP_Post_Type( 'attachment', 'Media', 'upload_files' );
		};

		ob_start();
		$GLOBALS['pts_test_plugin']->metabox();
		$output = (string) ob_get_clean();

		$this->assertStringContainsString( 'value="post"', $output );
		$this->assertStringContainsString( 'value="page"', $output );
		$this->assertSame( 1, substr_count( $output, 'value="attachment"' ) );
		$this->assertMatchesRegularExpression( '/value="attachment"[^>]+selected="selected"/', $output );
	}

	/**
	 * Verify form conversion normalizes status and preserves media data.
	 */
	public function test_form_switch_normalizes_attachment_status_and_preserves_media_data(): void {
		$_REQUEST = array(
			'pts_post_type'    => 'page',
			'pts-nonce-select' => 'valid',
		);
		$data     = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'post_mime_type' => 'image/jpeg',
			'post_parent'    => 7,
		);
		$post     = array(
			'ID'        => 42,
			'post_ID'   => 42,
			'post_type' => 'attachment',
		);

		$GLOBALS['pts_test']['callbacks']['get_post_type_object'] = static function () {
			return new WP_Post_Type( 'page', 'Page', 'publish_pages' );
		};

		$result = $GLOBALS['pts_test_plugin']->override_type( $data, $post );

		$this->assertSame( 'page', $result['post_type'] );
		$this->assertSame( 'draft', $result['post_status'] );
		$this->assertSame( 'image/jpeg', $result['post_mime_type'] );
		$this->assertSame( 7, $result['post_parent'] );
		$this->assertSame(
			array( array( 'page', 'attachment', 42 ) ),
			$GLOBALS['pts_test']['calls']['do_action:post_type_after_switch']
		);
	}

	/**
	 * Verify non-inherit attachment statuses remain unchanged.
	 */
	public function test_form_switch_preserves_non_inherit_attachment_statuses(): void {
		$_REQUEST = array(
			'pts_post_type'    => 'page',
			'pts-nonce-select' => 'valid',
		);
		$data     = array(
			'post_type'   => 'attachment',
			'post_status' => 'private',
		);
		$post     = array(
			'ID'        => 42,
			'post_ID'   => 42,
			'post_type' => 'attachment',
		);

		$GLOBALS['pts_test']['callbacks']['get_post_type_object'] = static function () {
			return new WP_Post_Type( 'page', 'Page', 'publish_pages' );
		};

		$result = $GLOBALS['pts_test_plugin']->override_type( $data, $post );

		$this->assertSame( 'page', $result['post_type'] );
		$this->assertSame( 'private', $result['post_status'] );
	}

	/**
	 * Verify related attachment saves are not mistaken for the requested post.
	 */
	public function test_form_switch_ignores_related_attachment_saves_without_the_requested_post_id(): void {
		$_REQUEST = array(
			'pts_post_type'    => 'page',
			'pts-nonce-select' => 'valid',
		);
		$data     = array( 'post_type' => 'attachment' );
		$post     = array(
			'ID'        => 84,
			'post_type' => 'attachment',
		);

		$this->assertSame( $data, $GLOBALS['pts_test_plugin']->override_type( $data, $post ) );
		$this->assertArrayNotHasKey( 'do_action:post_type_after_switch', $GLOBALS['pts_test']['calls'] );
	}

	/**
	 * Verify WPML's direct sibling switch receives the same status normalization.
	 */
	public function test_direct_switch_normalizes_a_translated_attachment_status(): void {
		$GLOBALS['pts_test']['callbacks']['get_post_type'] = static function () {
			return 'attachment';
		};

		$GLOBALS['pts_test']['callbacks']['get_post_field'] = static function () {
			return 'inherit';
		};

		$method = new ReflectionMethod( Post_Type_Switcher::class, 'set_post_type' );
		if ( PHP_VERSION_ID < 80100 ) {
			$method->setAccessible( true );
		}
		$result = $method->invoke( $GLOBALS['pts_test_plugin'], 84, 'page' );

		$this->assertSame( 1, $result );
		$this->assertArrayNotHasKey( 'set_post_type', $GLOBALS['pts_test']['calls'] );
		$this->assertSame(
			array(
				array(
					array(
						'ID'          => 84,
						'post_type'   => 'page',
						'post_status' => 'draft',
					),
				),
			),
			$GLOBALS['pts_test']['calls']['wp_update_post']
		);
	}

	/**
	 * Verify a failed normalized update does not commit type or switch metadata.
	 */
	public function test_direct_switch_does_not_commit_attachment_type_or_metadata_when_normalized_update_fails(): void {
		$GLOBALS['pts_test']['callbacks']['get_post_type'] = static function () {
			return 'attachment';
		};

		$GLOBALS['pts_test']['callbacks']['get_post_field'] = static function () {
			return 'inherit';
		};

		$GLOBALS['pts_test']['callbacks']['wp_update_post'] = static function () {
			return 0;
		};

		$method = new ReflectionMethod( Post_Type_Switcher::class, 'set_post_type' );
		if ( PHP_VERSION_ID < 80100 ) {
			$method->setAccessible( true );
		}
		$result = $method->invoke( $GLOBALS['pts_test_plugin'], 84, 'page' );

		$this->assertSame( 0, $result );
		$this->assertArrayNotHasKey( 'set_post_type', $GLOBALS['pts_test']['calls'] );
		$this->assertArrayNotHasKey( 'add_post_meta', $GLOBALS['pts_test']['calls'] );
		$this->assertArrayNotHasKey( 'update_post_meta', $GLOBALS['pts_test']['calls'] );
		$this->assertArrayNotHasKey( 'delete_post_meta', $GLOBALS['pts_test']['calls'] );
		$this->assertSame(
			array(
				array(
					array(
						'ID'          => 84,
						'post_type'   => 'page',
						'post_status' => 'draft',
					),
				),
			),
			$GLOBALS['pts_test']['calls']['wp_update_post']
		);
	}

	/**
	 * Verify AJAX failures do not announce a completed switch.
	 */
	public function test_ajax_switch_does_not_fire_success_action_when_normalized_update_fails(): void {
		$_GET = array(
			'post_id'          => 84,
			'pts_post_type'    => 'page',
			'pts-nonce-select' => 'valid',
		);

		$GLOBALS['pts_test']['callbacks']['get_post_type'] = static function () {
			return 'attachment';
		};

		$GLOBALS['pts_test']['callbacks']['get_post_field'] = static function () {
			return 'inherit';
		};

		$GLOBALS['pts_test']['callbacks']['wp_update_post'] = static function () {
			return 0;
		};

		$GLOBALS['pts_test']['callbacks']['wp_safe_redirect'] = static function () {
			throw new RuntimeException( 'redirected' );
		};

		try {
			$GLOBALS['pts_test_plugin']->handle_ajax();
			$this->fail( 'Expected the redirect fixture to stop execution.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame( 'redirected', $exception->getMessage() );
		}

		$this->assertArrayNotHasKey( 'do_action:post_type_after_switch', $GLOBALS['pts_test']['calls'] );
	}

	/**
	 * Verify WPML leaves a failed sibling unchanged while the outer save proceeds.
	 */
	public function test_wpml_sync_does_not_record_target_type_for_a_failed_sibling_conversion(): void {
		$GLOBALS['sitepress'] = new SitePress();
		$GLOBALS['wpdb']      = new class() {
			/**
			 * Database table prefix.
			 *
			 * @var string
			 */
			public $prefix = 'wp_';

			/**
			 * Recorded database updates.
			 *
			 * @var array<int, array<int, mixed>>
			 */
			public $updates = array();

			/**
			 * Return the supplied query fixture.
			 *
			 * @param string $query Query text.
			 * @return string Query text.
			 */
			public function prepare( $query ) {
				return $query;
			}

			/**
			 * Return the translation group fixture.
			 *
			 * @return int Translation group ID.
			 */
			public function get_var() {
				return 7;
			}

			/**
			 * Return translated post fixtures.
			 *
			 * @return array<int, string> Translated post IDs.
			 */
			public function get_col() {
				return array( '84', '85' );
			}

			/**
			 * Record a translation-table update.
			 *
			 * @param mixed ...$arguments Update arguments.
			 * @return int Updated row count.
			 */
			public function update( ...$arguments ) {
				$this->updates[] = $arguments;
				return 1;
			}
		};

		$GLOBALS['pts_test']['callbacks']['get_post_type'] = static function () {
			return 'attachment';
		};

		$GLOBALS['pts_test']['callbacks']['get_post_field'] = static function () {
			return 'inherit';
		};

		$GLOBALS['pts_test']['callbacks']['wp_update_post'] = static function () {
			return 0;
		};

		$GLOBALS['pts_test_plugin']->wpml_sync_type( 'page', 'attachment', 84 );

		$this->assertSame(
			array(
				array(
					'wp_icl_translations',
					array( 'element_type' => 'post_page' ),
					array(
						'trid'       => 7,
						'element_id' => 84,
					),
				),
			),
			$GLOBALS['wpdb']->updates
		);
	}
}
