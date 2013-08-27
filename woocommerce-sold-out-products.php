<?php
/**
 * Plugin Name: WooCommerce Sold Out Products
 * Plugin URI: http://www.woothemes.com/woocommerce/
 * Description: Adds a sold out products widget and shortcode to showcase your sold out products
 * Version: 1.0.4
 * Author: Gerhard Potgieter
 * Author URI: http://gerhardpotgieter.com
 * Requires at least: 3.5
 * Tested up to: 3.5
 *
 * @package WooCommerce
 * @author Gerhard Potgieter
 * @since 1.0
 **/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Sold_Out_Products' ) ) {
	require_once( 'classes/class-wc-sold-out-products.php' );
	$GLOBALS['wc_sold_out_products'] = new WC_Sold_Out_Products( __FILE__ );
}

?>