<?php
/**
 * Base test case class for WooCommerce Sold Out Products.
 *
 * @package Kloon\WCSoldOutProducts\Tests
 */

namespace Kloon\WCSoldOutProducts\Tests;

use WP_UnitTestCase;
use WC_Product_Simple;
use WC_Helper_Product;

/**
 * Base test case class that provides WooCommerce-specific testing utilities.
 */
abstract class WCSoldOutProductsTestCase extends WP_UnitTestCase {

	/**
	 * Array of created product IDs for cleanup.
	 *
	 * @var array
	 */
	protected $created_products = [];

	/**
	 * Set up test fixtures.
	 */
	public function setUp(): void {
		parent::setUp();

		// Ensure WooCommerce is loaded.
		if ( ! class_exists( 'WooCommerce' ) ) {
			$this->markTestSkipped( 'WooCommerce is not available.' );
		}

		// Initialize WooCommerce session and cart if needed.
		if ( function_exists( 'WC' ) ) {
			WC()->init();
		}
	}

	/**
	 * Tear down test fixtures.
	 */
	public function tearDown(): void {
		// Clean up created products.
		foreach ( $this->created_products as $product_id ) {
			wp_delete_post( $product_id, true );
		}
		$this->created_products = [];

		parent::tearDown();
	}

	/**
	 * Create a simple product for testing.
	 *
	 * @param array $args Product arguments.
	 * @return WC_Product_Simple
	 */
	protected function create_simple_product( $args = [] ) {
		$defaults = [
			'name'          => 'Test Product',
			'regular_price' => '10.00',
			'status'        => 'publish',
		];

		$args = wp_parse_args( $args, $defaults );

		// Use WooCommerce helper if available.
		if ( class_exists( 'WC_Helper_Product' ) && method_exists( 'WC_Helper_Product', 'create_simple_product' ) ) {
			$product = WC_Helper_Product::create_simple_product();
			if ( isset( $args['stock_status'] ) ) {
				$product->set_stock_status( $args['stock_status'] );
			}
			if ( isset( $args['manage_stock'] ) ) {
				$product->set_manage_stock( $args['manage_stock'] );
			}
			if ( isset( $args['stock_quantity'] ) ) {
				$product->set_stock_quantity( $args['stock_quantity'] );
			}
			$product->save();
		} else {
			$product = new WC_Product_Simple();
			$product->set_name( $args['name'] );
			$product->set_regular_price( $args['regular_price'] );
			$product->set_status( $args['status'] );

			if ( isset( $args['stock_status'] ) ) {
				$product->set_stock_status( $args['stock_status'] );
			}
			if ( isset( $args['manage_stock'] ) ) {
				$product->set_manage_stock( $args['manage_stock'] );
			}
			if ( isset( $args['stock_quantity'] ) ) {
				$product->set_stock_quantity( $args['stock_quantity'] );
			}
			if ( isset( $args['catalog_visibility'] ) ) {
				$product->set_catalog_visibility( $args['catalog_visibility'] );
			}

			$product->save();
		}

		$this->created_products[] = $product->get_id();

		return $product;
	}

	/**
	 * Create a sold out product for testing.
	 *
	 * @param array $args Additional product arguments.
	 * @return WC_Product_Simple
	 */
	protected function create_sold_out_product( $args = [] ) {
		$defaults = [
			'name'           => 'Sold Out Product',
			'stock_status'   => 'outofstock',
			'manage_stock'   => true,
			'stock_quantity' => 0,
		];

		return $this->create_simple_product( wp_parse_args( $args, $defaults ) );
	}

	/**
	 * Create an in-stock product for testing.
	 *
	 * @param array $args Additional product arguments.
	 * @return WC_Product_Simple
	 */
	protected function create_in_stock_product( $args = [] ) {
		$defaults = [
			'name'           => 'In Stock Product',
			'stock_status'   => 'instock',
			'manage_stock'   => true,
			'stock_quantity' => 10,
		];

		return $this->create_simple_product( wp_parse_args( $args, $defaults ) );
	}

	/**
	 * Get the plugin instance.
	 *
	 * @return \WC_Sold_Out_Products|null
	 */
	protected function get_plugin_instance() {
		global $wc_sold_out_products;
		return $wc_sold_out_products;
	}

	/**
	 * Assert that a string contains the sold out badge HTML.
	 *
	 * @param string $html The HTML to check.
	 */
	protected function assertContainsSoldOutBadge( $html ) {
		$this->assertStringContainsString( 'soldout', $html );
		$this->assertStringContainsString( 'Sold Out', $html );
	}

	/**
	 * Assert that a string does not contain the sold out badge HTML.
	 *
	 * @param string $html The HTML to check.
	 */
	protected function assertNotContainsSoldOutBadge( $html ) {
		$this->assertStringNotContainsString( 'class="soldout"', $html );
	}
}
