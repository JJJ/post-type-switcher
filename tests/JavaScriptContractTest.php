<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class JavaScriptContractTest extends TestCase {
	public function test_block_editor_source_preserves_confirmation_and_redirect_contract(): void {
		$source = (string) file_get_contents( dirname( __DIR__ ) . '/src/components/post-type-switcher.js' );

		$this->assertStringContainsString( 'window.confirm( message )', $source );
		$this->assertStringContainsString( "'&pts_post_type=' + value", $source );
		$this->assertStringContainsString( 'currentPostType: oldPostType', $source );
	}

	public function test_quick_edit_asset_preserves_wordpress_inline_edit_integration(): void {
		$source = (string) file_get_contents( dirname( __DIR__ ) . '/assets/js/quickedit.js' );

		$this->assertStringContainsString( 'inlineEditPost.edit', $source );
		$this->assertStringContainsString( 'inlineEditPost.edit = function', $source );
		$this->assertStringContainsString( 'pts_post_type', $source );
	}
}
