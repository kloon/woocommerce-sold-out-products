<?php
/**
 * Plugin Name: WooCommerce Sold Out Products
 * Plugin URI: https://github.com/kloon/woocommerce-sold-out-products
 * Description: Adds a sold out products widget, shortcode, and Gutenberg block to showcase your sold out products
 * Version: 2.0.0
 * Author: Gerhard Potgieter
 * Author URI: https://developer.dev
 * Requires at least: 6.4
 * Tested up to: 6.7
 * Requires PHP: 7.4
 * WC requires at least: 8.5
 * WC tested up to: 9.5
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wc-sold-out-products
 * Domain Path: /languages
 *
 * @package Kloon\WCSoldOutProducts
 * @author  Gerhard Potgieter
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Define plugin constants.
define( 'WC_SOLD_OUT_PRODUCTS_VERSION', '2.0.0' );
define( 'WC_SOLD_OUT_PRODUCTS_FILE', __FILE__ );
define( 'WC_SOLD_OUT_PRODUCTS_PATH', plugin_dir_path( __FILE__ ) );
define( 'WC_SOLD_OUT_PRODUCTS_URL', plugin_dir_url( __FILE__ ) );
define( 'WC_SOLD_OUT_PRODUCTS_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Declare HPOS (High-Performance Order Storage) compatibility.
 *
 * @since 2.0.0
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
		}
	}
);

/**
 * Display admin notice if WooCommerce is not active.
 *
 * @since 2.0.0
 */
function wc_sold_out_products_missing_wc_notice() {
	?>
	<div class="error">
		<p>
			<?php
			printf(
				/* translators: %s: WooCommerce link */
				esc_html__( 'WooCommerce Sold Out Products requires WooCommerce to be installed and active. You can download %s here.', 'wc-sold-out-products' ),
				'<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>'
			);
			?>
		</p>
	</div>
	<?php
}

/**
 * Check WooCommerce dependency and initialize the plugin.
 *
 * @since 2.0.0
 */
function wc_sold_out_products_init() {
	// Check if WooCommerce is active.
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wc_sold_out_products_missing_wc_notice' );
		return;
	}

	// Check WooCommerce version.
	if ( version_compare( WC_VERSION, '8.5', '<' ) ) {
		add_action(
			'admin_notices',
			function () {
				?>
				<div class="error">
					<p>
						<?php
						esc_html_e( 'WooCommerce Sold Out Products requires WooCommerce 8.5 or higher.', 'wc-sold-out-products' );
						?>
					</p>
				</div>
				<?php
			}
		);
		return;
	}

	// Load the main plugin class.
	if ( ! class_exists( 'WC_Sold_Out_Products' ) ) {
		require_once WC_SOLD_OUT_PRODUCTS_PATH . 'classes/class-wc-sold-out-products.php';
		$GLOBALS['wc_sold_out_products'] = new WC_Sold_Out_Products( __FILE__ );
	}
}
add_action( 'plugins_loaded', 'wc_sold_out_products_init', 10 );

/**
 * Register activation hook.
 *
 * @since 2.0.0
 */
function wc_sold_out_products_activate() {
	// Set default options if needed.
	if ( false === get_option( 'wc_sold_out_products_version' ) ) {
		add_option( 'wc_sold_out_products_version', WC_SOLD_OUT_PRODUCTS_VERSION );
	} else {
		update_option( 'wc_sold_out_products_version', WC_SOLD_OUT_PRODUCTS_VERSION );
	}

	// Clear any cached data.
	wp_cache_delete( 'widget_sold_out_products', 'widget' );
}
register_activation_hook( __FILE__, 'wc_sold_out_products_activate' );

/**
 * Register deactivation hook.
 *
 * @since 2.0.0
 */
function wc_sold_out_products_deactivate() {
	// Clear cached data.
	wp_cache_delete( 'widget_sold_out_products', 'widget' );
}
register_deactivation_hook( __FILE__, 'wc_sold_out_products_deactivate' );

/**
 * Add settings link on plugin page.
 *
 * @since 2.0.0
 *
 * @param array $links Plugin action links.
 * @return array Modified plugin action links.
 */
function wc_sold_out_products_plugin_links( $links ) {
	$plugin_links = [
		'<a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">' . esc_html__( 'Widgets', 'wc-sold-out-products' ) . '</a>',
	];

	return array_merge( $plugin_links, $links );
}
add_filter( 'plugin_action_links_' . WC_SOLD_OUT_PRODUCTS_BASENAME, 'wc_sold_out_products_plugin_links' );
