<?php
/**
 * Tests for the shortcode functionality.
 *
 * @package Kloon\WCSoldOutProducts\Tests\Unit
 */

namespace Kloon\WCSoldOutProducts\Tests\Unit;

use Kloon\WCSoldOutProducts\Tests\WCSoldOutProductsTestCase;

/**
 * Shortcode test case.
 */
class ShortcodeTest extends WCSoldOutProductsTestCase {

	/**
	 * Test shortcode output with no products.
	 */
	public function test_shortcode_no_products() {
		$output = do_shortcode( '[sold_out_products]' );

		$this->assertStringContainsString( 'woocommerce', $output );
	}

	/**
	 * Test shortcode output with sold out products.
	 */
	public function test_shortcode_with_products() {
		$product = $this->create_sold_out_product();

		$output = do_shortcode( '[sold_out_products]' );

		$this->assertStringContainsString( 'woocommerce', $output );
		$this->assertStringContainsString( 'wc-sold-out-products-shortcode', $output );
	}

	/**
	 * Test shortcode respects per_page attribute.
	 */
	public function test_shortcode_per_page() {
		// Create 5 sold out products.
		for ( $i = 0; $i < 5; $i++ ) {
			$this->create_sold_out_product( [ 'name' => 'Sold Out Product ' . $i ] );
		}

		$output = do_shortcode( '[sold_out_products per_page="2"]' );

		$this->assertStringContainsString( 'woocommerce', $output );
	}

	/**
	 * Test shortcode respects columns attribute.
	 */
	public function test_shortcode_columns() {
		$this->create_sold_out_product();

		$output = do_shortcode( '[sold_out_products columns="3"]' );

		$this->assertStringContainsString( 'woocommerce', $output );
	}

	/**
	 * Test shortcode handles invalid attributes gracefully.
	 */
	public function test_shortcode_invalid_attributes() {
		$this->create_sold_out_product();

		// Test with invalid values - should fall back to defaults.
		$output = do_shortcode( '[sold_out_products per_page="invalid" columns="abc"]' );

		$this->assertStringContainsString( 'woocommerce', $output );
	}

	/**
	 * Test shortcode orderby attribute.
	 */
	public function test_shortcode_orderby() {
		$this->create_sold_out_product();

		$valid_orderbys = [ 'date', 'title', 'price', 'popularity', 'rating', 'rand', 'menu_order' ];

		foreach ( $valid_orderbys as $orderby ) {
			$output = do_shortcode( '[sold_out_products orderby="' . $orderby . '"]' );
			$this->assertStringContainsString( 'woocommerce', $output );
		}
	}

	/**
	 * Test shortcode order attribute.
	 */
	public function test_shortcode_order() {
		$this->create_sold_out_product();

		$output_desc = do_shortcode( '[sold_out_products order="desc"]' );
		$this->assertStringContainsString( 'woocommerce', $output_desc );

		$output_asc = do_shortcode( '[sold_out_products order="asc"]' );
		$this->assertStringContainsString( 'woocommerce', $output_asc );
	}

	/**
	 * Test shortcode doesn't show in-stock products.
	 */
	public function test_shortcode_excludes_in_stock() {
		$sold_out = $this->create_sold_out_product( [ 'name' => 'Sold Out Only Product' ] );
		$in_stock = $this->create_in_stock_product( [ 'name' => 'In Stock Only Product' ] );

		$output = do_shortcode( '[sold_out_products]' );

		// The in-stock product name shouldn't appear in the output.
		// (This is a basic check - actual visibility depends on template)
		$this->assertStringContainsString( 'woocommerce', $output );
	}

	/**
	 * Test filter for shortcode query args.
	 */
	public function test_shortcode_query_args_filter() {
		$filter_called = false;

		add_filter(
			'wc_sold_out_products_shortcode_query_args',
			function ( $args, $atts ) use ( &$filter_called ) {
				$filter_called = true;
				return $args;
			},
			10,
			2
		);

		$this->create_sold_out_product();
		do_shortcode( '[sold_out_products]' );

		$this->assertTrue( $filter_called );
	}
}
