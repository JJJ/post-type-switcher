<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class JavaScriptContractTest extends TestCase {
	public function test_block_editor_source_preserves_confirmation_and_redirect_contract(): void {
		$source = (string) file_get_contents( dirname( __DIR__ ) . '/src/components/post-type-switcher.js' );
		$plugin = '';
		foreach ( new SplFileObject( dirname( __DIR__ ) . '/post-type-switcher.php' ) as $line ) {
			$plugin .= $line;
		}
		$asset = require dirname( __DIR__ ) . '/assets/js/block.asset.php';

		$this->assertStringContainsString( 'window.confirm( message )', $source );
		$this->assertStringContainsString( "'&pts_post_type=' + value", $source );
		$this->assertStringContainsString( 'currentPostType: oldPostType', $source );
		$this->assertContains( 'react-jsx-runtime', $asset['dependencies'] );
		$this->assertStringContainsString( '$asset[\'dependencies\']', $plugin );
		$this->assertStringContainsString( '$asset[\'version\']', $plugin );
	}

	public function test_quick_edit_asset_preserves_wordpress_inline_edit_integration(): void {
		$source = (string) file_get_contents( dirname( __DIR__ ) . '/assets/js/quickedit.js' );

		$this->assertStringContainsString( 'inlineEditPost.edit', $source );
		$this->assertStringContainsString( 'inlineEditPost.edit = function', $source );
		$this->assertStringContainsString( 'pts_post_type', $source );
	}
}
