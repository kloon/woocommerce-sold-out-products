<?php
/**
 * Tests for the widget functionality.
 *
 * @package Kloon\WCSoldOutProducts\Tests\Unit
 */

namespace Kloon\WCSoldOutProducts\Tests\Unit;

use Kloon\WCSoldOutProducts\Tests\WCSoldOutProductsTestCase;
use WC_Widget_Sold_Out_Products;

/**
 * Widget test case.
 */
class WidgetTest extends WCSoldOutProductsTestCase {

	/**
	 * Widget instance.
	 *
	 * @var WC_Widget_Sold_Out_Products
	 */
	protected $widget;

	/**
	 * Set up test fixtures.
	 */
	public function setUp(): void {
		parent::setUp();

		// Make sure widget class is loaded.
		if ( ! class_exists( 'WC_Widget_Sold_Out_Products' ) ) {
			require_once WC_SOLD_OUT_PRODUCTS_PATH . 'classes/class-wc-widget-sold-out-products.php';
		}

		$this->widget = new WC_Widget_Sold_Out_Products();
	}

	/**
	 * Test widget is registered.
	 */
	public function test_widget_registered() {
		global $wp_widget_factory;

		$this->assertArrayHasKey( 'WC_Widget_Sold_Out_Products', $wp_widget_factory->widgets );
	}

	/**
	 * Test widget has correct ID base.
	 */
	public function test_widget_id_base() {
		$this->assertEquals( 'woocommerce_sold_out_products', $this->widget->id_base );
	}

	/**
	 * Test widget has name.
	 */
	public function test_widget_has_name() {
		$this->assertNotEmpty( $this->widget->widget_name );
	}

	/**
	 * Test widget has description.
	 */
	public function test_widget_has_description() {
		$this->assertNotEmpty( $this->widget->widget_description );
	}

	/**
	 * Test widget settings exist.
	 */
	public function test_widget_settings() {
		$this->assertIsArray( $this->widget->settings );
		$this->assertArrayHasKey( 'title', $this->widget->settings );
		$this->assertArrayHasKey( 'number', $this->widget->settings );
		$this->assertArrayHasKey( 'show_variations', $this->widget->settings );
	}

	/**
	 * Test widget update sanitizes title.
	 */
	public function test_widget_update_sanitizes_title() {
		$new_instance = [
			'title'           => '<script>alert("XSS")</script>My Title',
			'number'          => 5,
			'show_variations' => 0,
		];

		$old_instance = [];

		$result = $this->widget->update( $new_instance, $old_instance );

		$this->assertStringNotContainsString( '<script>', $result['title'] );
	}

	/**
	 * Test widget update validates number.
	 */
	public function test_widget_update_validates_number() {
		// Test minimum.
		$result = $this->widget->update( [ 'number' => 0 ], [] );
		$this->assertGreaterThanOrEqual( 1, $result['number'] );

		// Test maximum.
		$result = $this->widget->update( [ 'number' => 100 ], [] );
		$this->assertLessThanOrEqual( 15, $result['number'] );

		// Test valid number.
		$result = $this->widget->update( [ 'number' => 5 ], [] );
		$this->assertEquals( 5, $result['number'] );
	}

	/**
	 * Test widget update handles checkbox.
	 */
	public function test_widget_update_checkbox() {
		// Checked.
		$result = $this->widget->update( [ 'show_variations' => 1 ], [] );
		$this->assertEquals( 1, $result['show_variations'] );

		// Unchecked (not present in POST).
		$result = $this->widget->update( [], [] );
		$this->assertEquals( 0, $result['show_variations'] );
	}

	/**
	 * Test widget form output.
	 */
	public function test_widget_form_output() {
		ob_start();
		$this->widget->form( [] );
		$output = ob_get_clean();

		$this->assertStringContainsString( 'title', $output );
		$this->assertStringContainsString( 'number', $output );
		$this->assertStringContainsString( 'show_variations', $output );
	}

	/**
	 * Test widget query args filter.
	 */
	public function test_widget_query_args_filter() {
		$filter_called = false;

		add_filter(
			'wc_sold_out_products_widget_query_args',
			function ( $args, $instance ) use ( &$filter_called ) {
				$filter_called = true;
				return $args;
			},
			10,
			2
		);

		$this->create_sold_out_product();

		// Trigger widget output.
		ob_start();
		$this->widget->widget(
			[
				'widget_id'     => 'test-widget-1',
				'before_widget' => '<div>',
				'after_widget'  => '</div>',
				'before_title'  => '<h2>',
				'after_title'   => '</h2>',
			],
			[ 'number' => 5 ]
		);
		ob_get_clean();

		$this->assertTrue( $filter_called );
	}

	/**
	 * Test widget output with sold out products.
	 */
	public function test_widget_output_with_products() {
		$product = $this->create_sold_out_product( [ 'name' => 'Test Sold Out Widget Product' ] );

		ob_start();
		$this->widget->widget(
			[
				'widget_id'     => 'test-widget-2',
				'before_widget' => '<div class="widget">',
				'after_widget'  => '</div>',
				'before_title'  => '<h2>',
				'after_title'   => '</h2>',
			],
			[
				'title'  => 'Sold Out',
				'number' => 5,
			]
		);
		$output = ob_get_clean();

		$this->assertStringContainsString( 'product_list_widget', $output );
	}

	/**
	 * Test widget flush cache.
	 */
	public function test_widget_flush_cache() {
		// This should not throw any errors.
		$this->widget->flush_widget_cache();
		$this->assertTrue( true );
	}
}
