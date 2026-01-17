<?php
/**
 * Tests for the main plugin functionality.
 *
 * @package Kloon\WCSoldOutProducts\Tests\Unit
 */

namespace Kloon\WCSoldOutProducts\Tests\Unit;

use Kloon\WCSoldOutProducts\Tests\WCSoldOutProductsTestCase;
use WC_Sold_Out_Products;

/**
 * Plugin test case.
 */
class PluginTest extends WCSoldOutProductsTestCase {

	/**
	 * Test that the plugin is loaded.
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'WC_SOLD_OUT_PRODUCTS_VERSION' ) );
		$this->assertTrue( defined( 'WC_SOLD_OUT_PRODUCTS_FILE' ) );
		$this->assertTrue( defined( 'WC_SOLD_OUT_PRODUCTS_PATH' ) );
		$this->assertTrue( defined( 'WC_SOLD_OUT_PRODUCTS_URL' ) );
	}

	/**
	 * Test that the plugin instance exists.
	 */
	public function test_plugin_instance_exists() {
		$instance = $this->get_plugin_instance();
		$this->assertInstanceOf( WC_Sold_Out_Products::class, $instance );
	}

	/**
	 * Test shortcode is registered.
	 */
	public function test_shortcode_registered() {
		$this->assertTrue( shortcode_exists( 'sold_out_products' ) );
	}

	/**
	 * Test is_product_sold_out with sold out product.
	 */
	public function test_is_product_sold_out_true() {
		$product = $this->create_sold_out_product();

		$this->assertTrue( WC_Sold_Out_Products::is_product_sold_out( $product ) );
		$this->assertTrue( WC_Sold_Out_Products::is_product_sold_out( $product->get_id() ) );
	}

	/**
	 * Test is_product_sold_out with in-stock product.
	 */
	public function test_is_product_sold_out_false() {
		$product = $this->create_in_stock_product();

		$this->assertFalse( WC_Sold_Out_Products::is_product_sold_out( $product ) );
		$this->assertFalse( WC_Sold_Out_Products::is_product_sold_out( $product->get_id() ) );
	}

	/**
	 * Test is_product_sold_out with invalid product.
	 */
	public function test_is_product_sold_out_invalid() {
		$this->assertFalse( WC_Sold_Out_Products::is_product_sold_out( 0 ) );
		$this->assertFalse( WC_Sold_Out_Products::is_product_sold_out( 999999 ) );
		$this->assertFalse( WC_Sold_Out_Products::is_product_sold_out( null ) );
	}

	/**
	 * Test get_sold_out_products returns array.
	 */
	public function test_get_sold_out_products_returns_array() {
		// Create some products.
		$this->create_sold_out_product();
		$this->create_sold_out_product();
		$this->create_in_stock_product();

		$products = WC_Sold_Out_Products::get_sold_out_products();

		$this->assertIsArray( $products );
	}

	/**
	 * Test get_sold_out_products respects limit.
	 */
	public function test_get_sold_out_products_respects_limit() {
		// Create 5 sold out products.
		for ( $i = 0; $i < 5; $i++ ) {
			$this->create_sold_out_product();
		}

		$products = WC_Sold_Out_Products::get_sold_out_products( [ 'limit' => 3 ] );

		$this->assertLessThanOrEqual( 3, count( $products ) );
	}

	/**
	 * Test get_sold_out_products only returns sold out products.
	 */
	public function test_get_sold_out_products_only_sold_out() {
		// Create mixed products.
		$this->create_sold_out_product();
		$this->create_in_stock_product();
		$this->create_sold_out_product();

		$products = WC_Sold_Out_Products::get_sold_out_products();

		foreach ( $products as $product ) {
			$this->assertFalse( $product->is_in_stock() );
		}
	}
}
