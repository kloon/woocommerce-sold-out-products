<?php
/**
 * PHPUnit bootstrap file for WooCommerce Sold Out Products.
 *
 * @package Kloon\WCSoldOutProducts\Tests
 */

// Composer autoloader.
require_once dirname( __DIR__, 2 ) . '/vendor/autoload.php';

// Define test constants.
define( 'WC_SOLD_OUT_PRODUCTS_TESTS_DIR', __DIR__ );
define( 'WC_SOLD_OUT_PRODUCTS_PLUGIN_DIR', dirname( __DIR__, 2 ) );

// Determine the tests directory (from wp-env or local install).
$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

// Check for wp-phpunit installation.
if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	// Try vendor path.
	$_tests_dir = dirname( __DIR__, 2 ) . '/vendor/wp-phpunit/wp-phpunit';

	if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
		echo "Could not find WordPress tests library. Did you run `composer install`?\n";
		exit( 1 );
	}
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	// Define WooCommerce constants if not already defined.
	if ( ! defined( 'WC_ABSPATH' ) ) {
		$wc_dir = WP_PLUGIN_DIR . '/woocommerce/';
		if ( file_exists( $wc_dir . 'woocommerce.php' ) ) {
			define( 'WC_ABSPATH', $wc_dir );
		}
	}

	// Load WooCommerce.
	if ( defined( 'WC_ABSPATH' ) && file_exists( WC_ABSPATH . 'woocommerce.php' ) ) {
		require WC_ABSPATH . 'woocommerce.php';
	} elseif ( file_exists( dirname( __DIR__, 2 ) . '/vendor/woocommerce/woocommerce/woocommerce.php' ) ) {
		require dirname( __DIR__, 2 ) . '/vendor/woocommerce/woocommerce/woocommerce.php';
	}

	// Load our plugin.
	require WC_SOLD_OUT_PRODUCTS_PLUGIN_DIR . '/woocommerce-sold-out-products.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

/**
 * Install WooCommerce after WordPress is loaded.
 */
function _install_woocommerce() {
	// Clean existing install first.
	define( 'WP_UNINSTALL_PLUGIN', true );

	// Initialize WooCommerce.
	if ( class_exists( 'WooCommerce' ) ) {
		WC()->init();

		// Include WooCommerce install functions.
		if ( defined( 'WC_ABSPATH' ) ) {
			include_once WC_ABSPATH . 'includes/class-wc-install.php';
			WC_Install::install();
		}

		// Reload capabilities after install.
		$GLOBALS['wp_roles'] = null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		wp_roles();
	}
}
tests_add_filter( 'setup_theme', '_install_woocommerce' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

// Include WooCommerce test helpers if available.
if ( defined( 'WC_ABSPATH' ) && file_exists( WC_ABSPATH . 'tests/legacy/framework/class-wc-unit-test-case.php' ) ) {
	require_once WC_ABSPATH . 'tests/legacy/framework/class-wc-unit-test-case.php';
	require_once WC_ABSPATH . 'tests/legacy/framework/helpers/class-wc-helper-product.php';
} elseif ( defined( 'WC_ABSPATH' ) && file_exists( WC_ABSPATH . 'tests/php/includes/class-wc-unit-test-case.php' ) ) {
	require_once WC_ABSPATH . 'tests/php/includes/class-wc-unit-test-case.php';
}

// Load our test case base class.
require_once __DIR__ . '/WCSoldOutProductsTestCase.php';
