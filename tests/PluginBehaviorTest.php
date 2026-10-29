<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PluginBehaviorTest extends TestCase {
	protected function setUp(): void {
		pts_test_reset();
		$_REQUEST = array();
	}

	public function test_post_type_column_contract_is_preserved(): void {
		$this->assertSame(
			array( 'title' => 'Title', 'post_type' => 'Type' ),
			$GLOBALS['pts_test_plugin']->add_column( array( 'title' => 'Title' ) )
		);
		$this->assertSame(
			array( 'author', 'post_type' ),
			$GLOBALS['pts_test_plugin']->default_hidden_columns( array( 'author' ) )
		);
	}

	public function test_switchable_types_exclude_attachments_and_remain_filterable(): void {
		$GLOBALS['pts_test']['callbacks']['get_post_types'] = static function () {
			return array(
				'post'       => new WP_Post_Type( 'post', 'Post' ),
				'page'       => new WP_Post_Type( 'page', 'Page' ),
				'attachment' => new WP_Post_Type( 'attachment', 'Media' ),
			);
		};
		$GLOBALS['pts_test']['callbacks']['apply_filters:pts_get_post_types_filter'] = static function ( $types ) {
			$types['book'] = new WP_Post_Type( 'book', 'Book' );
			return $types;
		};

		$method = new ReflectionMethod( Post_Type_Switcher::class, 'get_post_types' );
		if ( PHP_VERSION_ID < 80100 ) {
			$method->setAccessible( true );
		}
		$types = $method->invoke( $GLOBALS['pts_test_plugin'] );

		$this->assertArrayNotHasKey( 'attachment', $types );
		$this->assertSame( array( 'post', 'page', 'book' ), array_keys( $types ) );
	}

	public function test_override_type_requires_edit_and_target_publish_capabilities(): void {
		$_REQUEST = array(
			'pts_post_type'    => 'page',
			'pts-nonce-select' => 'valid',
		);
		$data = array( 'post_type' => 'post' );
		$post = array( 'ID' => 42, 'post_ID' => 42, 'post_type' => 'post' );
		$GLOBALS['pts_test']['callbacks']['get_post_type_object'] = static function () {
			return new WP_Post_Type( 'page', 'Page', 'publish_pages' );
		};
		$GLOBALS['pts_test']['callbacks']['current_user_can'] = static function ( $capability ) {
			return 'publish_pages' !== $capability;
		};

		$this->assertSame( $data, $GLOBALS['pts_test_plugin']->override_type( $data, $post ) );
		$this->assertArrayNotHasKey( 'do_action:post_type_after_switch', $GLOBALS['pts_test']['calls'] );

		pts_test_reset();
		$GLOBALS['pts_test']['callbacks']['get_post_type_object'] = static function () {
			return new WP_Post_Type( 'page', 'Page', 'publish_pages' );
		};
		$GLOBALS['pts_test']['callbacks']['current_user_can'] = static function ( $capability ) {
			return 'edit_post' !== $capability;
		};

		$this->assertSame( $data, $GLOBALS['pts_test_plugin']->override_type( $data, $post ) );
		$this->assertArrayNotHasKey( 'do_action:post_type_after_switch', $GLOBALS['pts_test']['calls'] );
	}

	public function test_override_type_changes_only_the_requested_post_and_fires_the_public_action(): void {
		$_REQUEST = array(
			'pts_post_type'    => 'page',
			'pts-nonce-select' => 'valid',
		);
		$data = array( 'post_type' => 'post' );
		$post = array( 'ID' => 42, 'post_ID' => 42, 'post_type' => 'post' );
		$GLOBALS['pts_test']['callbacks']['get_post_type_object'] = static function () {
			return new WP_Post_Type( 'page', 'Page', 'publish_pages' );
		};

		$result = $GLOBALS['pts_test_plugin']->override_type( $data, $post );

		$this->assertSame( 'page', $result['post_type'] );
		$this->assertSame(
			array( array( 'page', 'post', 42 ) ),
			$GLOBALS['pts_test']['calls']['do_action:post_type_after_switch']
		);
		$this->assertSame( array( array( 'valid' ) ), $GLOBALS['pts_test']['calls']['wp_unslash'] );
		$this->assertSame( array( array( 'valid' ) ), $GLOBALS['pts_test']['calls']['sanitize_text_field'] );
		$this->assertSame(
			array( array( 'valid', 'post-type-selector' ) ),
			$GLOBALS['pts_test']['calls']['wp_verify_nonce']
		);
	}

	public function test_bulk_selector_retains_no_change_and_capability_filtering(): void {
		$GLOBALS['pts_test']['callbacks']['get_post_types'] = static function () {
			return array(
				'post' => new WP_Post_Type( 'post', 'Post', 'publish_posts' ),
				'book' => new WP_Post_Type( 'book', 'Book', 'publish_books' ),
			);
		};
		$GLOBALS['pts_test']['callbacks']['current_user_can'] = static function ( $capability ) {
			return 'publish_books' !== $capability;
		};

		ob_start();
		$GLOBALS['pts_test_plugin']->select_box( true );
		$output = (string) ob_get_clean();

		$this->assertStringContainsString( 'value="-1"', $output );
		$this->assertStringContainsString( 'value="post"', $output );
		$this->assertStringNotContainsString( 'value="book"', $output );
	}
}
