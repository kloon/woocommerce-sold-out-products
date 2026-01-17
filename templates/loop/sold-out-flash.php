<?php
/**
 * Product Loop Sold Out Flash
 *
 * This template displays the "Sold Out!" badge on product loops/archives.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/sold-out-flash.php.
 *
 * @package Kloon\WCSoldOutProducts\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product ) {
	return;
}

if ( ! $product->is_in_stock() ) :
	/**
	 * Filter the sold out flash HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param string     $html    The sold out flash HTML.
	 * @param WP_Post    $post    The product post object.
	 * @param WC_Product $product The product object.
	 */
	$html = apply_filters(
		'woocommerce_sold_out_flash',
		sprintf(
			'<span class="soldout">%s</span>',
			esc_html__( 'Sold Out!', 'wc-sold-out-products' )
		),
		get_post(),
		$product
	);

	echo wp_kses_post( $html );
endif;
