<?php
/**
 * Server-side rendering of the Sold Out Products block.
 *
 * @package Kloon\WCSoldOutProducts
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

// Get attributes with defaults.
$columns   = isset( $attributes['columns'] ) ? absint( $attributes['columns'] ) : 4;
$rows      = isset( $attributes['rows'] ) ? absint( $attributes['rows'] ) : 3;
$orderby   = isset( $attributes['orderby'] ) ? sanitize_text_field( $attributes['orderby'] ) : 'date';
$order     = isset( $attributes['order'] ) ? sanitize_text_field( $attributes['order'] ) : 'desc';
$className = isset( $attributes['className'] ) ? $attributes['className'] : '';

// Calculate total products.
$per_page = $columns * $rows;

// Get the plugin instance and render.
global $wc_sold_out_products;

if ( ! $wc_sold_out_products instanceof WC_Sold_Out_Products ) {
	echo '<p>' . esc_html__( 'WooCommerce Sold Out Products plugin not loaded.', 'wc-sold-out-products' ) . '</p>';
	return;
}

// Build shortcode attributes.
$shortcode_atts = [
	'per_page' => $per_page,
	'columns'  => $columns,
	'orderby'  => $orderby,
	'order'    => $order,
];

// Get the wrapper attributes.
$wrapper_attributes = get_block_wrapper_attributes(
	[
		'class' => 'wc-sold-out-products-block' . ( $className ? ' ' . esc_attr( $className ) : '' ),
	]
);

// Render the block.
$output = $wc_sold_out_products->sold_out_products_shortcode( $shortcode_atts );

if ( empty( trim( strip_tags( $output, '<ul><li><a><img>' ) ) ) ) {
	// No products found - show placeholder in editor.
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		echo '<p class="wc-sold-out-products-no-products">' . esc_html__( 'No sold out products found.', 'wc-sold-out-products' ) . '</p>';
		return;
	}
}

printf(
	'<div %1$s>%2$s</div>',
	$wrapper_attributes, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$output // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
