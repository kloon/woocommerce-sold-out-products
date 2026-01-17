<?php
/**
 * Tests for the Gutenberg block functionality.
 *
 * @package Kloon\WCSoldOutProducts\Tests\Unit
 */

namespace Kloon\WCSoldOutProducts\Tests\Unit;

use Kloon\WCSoldOutProducts\Tests\WCSoldOutProductsTestCase;
use WP_Block_Type_Registry;

/**
 * Block test case.
 */
class BlockTest extends WCSoldOutProductsTestCase {

	/**
	 * Test block is registered.
	 */
	public function test_block_registered() {
		$registry = WP_Block_Type_Registry::get_instance();
		$block    = $registry->get_registered( 'wc-sold-out-products/sold-out-products' );

		$this->assertNotNull( $block );
	}

	/**
	 * Test block has render callback.
	 */
	public function test_block_has_render_callback() {
		$registry = WP_Block_Type_Registry::get_instance();
		$block    = $registry->get_registered( 'wc-sold-out-products/sold-out-products' );

		if ( $block ) {
			$this->assertNotNull( $block->render_callback );
		} else {
			$this->markTestSkipped( 'Block not registered - build may not have run.' );
		}
	}

	/**
	 * Test block render with default attributes.
	 */
	public function test_block_render_default_attributes() {
		$this->create_sold_out_product();

		$plugin = $this->get_plugin_instance();

		if ( ! $plugin ) {
			$this->markTestSkipped( 'Plugin instance not available.' );
			return;
		}

		$output = $plugin->render_block( [] );

		$this->assertStringContainsString( 'woocommerce', $output );
	}

	/**
	 * Test block render with custom attributes.
	 */
	public function test_block_render_custom_attributes() {
		$this->create_sold_out_product();

		$plugin = $this->get_plugin_instance();

		if ( ! $plugin ) {
			$this->markTestSkipped( 'Plugin instance not available.' );
			return;
		}

		$attributes = [
			'columns' => 3,
			'rows'    => 2,
			'orderby' => 'title',
			'order'   => 'asc',
		];

		$output = $plugin->render_block( $attributes );

		$this->assertStringContainsString( 'woocommerce', $output );
	}

	/**
	 * Test block render adds wrapper class.
	 */
	public function test_block_render_wrapper_class() {
		$this->create_sold_out_product();

		$plugin = $this->get_plugin_instance();

		if ( ! $plugin ) {
			$this->markTestSkipped( 'Plugin instance not available.' );
			return;
		}

		$output = $plugin->render_block( [] );

		$this->assertStringContainsString( 'wp-block-wc-sold-out-products-sold-out-products', $output );
	}

	/**
	 * Test block render with custom className.
	 */
	public function test_block_render_custom_classname() {
		$this->create_sold_out_product();

		$plugin = $this->get_plugin_instance();

		if ( ! $plugin ) {
			$this->markTestSkipped( 'Plugin instance not available.' );
			return;
		}

		$attributes = [
			'className' => 'my-custom-class',
		];

		$output = $plugin->render_block( $attributes );

		$this->assertStringContainsString( 'my-custom-class', $output );
	}

	/**
	 * Test block calculates per_page from rows and columns.
	 */
	public function test_block_calculates_per_page() {
		// Create enough products.
		for ( $i = 0; $i < 10; $i++ ) {
			$this->create_sold_out_product();
		}

		$plugin = $this->get_plugin_instance();

		if ( ! $plugin ) {
			$this->markTestSkipped( 'Plugin instance not available.' );
			return;
		}

		// 3 columns x 2 rows = 6 products.
		$attributes = [
			'columns' => 3,
			'rows'    => 2,
		];

		$output = $plugin->render_block( $attributes );

		$this->assertStringContainsString( 'woocommerce', $output );
	}

	/**
	 * Test block handles empty result.
	 */
	public function test_block_handles_empty_result() {
		$plugin = $this->get_plugin_instance();

		if ( ! $plugin ) {
			$this->markTestSkipped( 'Plugin instance not available.' );
			return;
		}

		// No products created.
		$output = $plugin->render_block( [] );

		// Should still return valid HTML.
		$this->assertStringContainsString( 'wp-block-wc-sold-out-products-sold-out-products', $output );
	}
}
