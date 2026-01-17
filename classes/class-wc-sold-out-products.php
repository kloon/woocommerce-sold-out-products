<?php
/**
 * WooCommerce Sold Out Products Main Class
 *
 * This class handles all the plugin functionality including the shortcode,
 * widget registration, sold out badges, and Gutenberg block.
 *
 * @since   1.0.0
 * @package Kloon\WCSoldOutProducts
 * @author  Gerhard Potgieter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class.
 *
 * @since 1.0.0
 */
class WC_Sold_Out_Products {

	/**
	 * Plugin file path.
	 *
	 * @var string
	 */
	protected $file;

	/**
	 * Flag to track if shortcode is being used.
	 *
	 * @var bool
	 */
	private static $shortcode_in_use = false;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file Main plugin file path.
	 */
	public function __construct( $file ) {
		$this->file = $file;
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 2.0.0
	 */
	private function init_hooks() {
		// Text domain.
		add_action( 'init', [ $this, 'load_textdomain_files' ] );

		// Scripts and styles.
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );

		// Widget.
		add_action( 'widgets_init', [ $this, 'register_widget' ] );

		// Shortcode.
		add_shortcode( 'sold_out_products', [ $this, 'sold_out_products_shortcode' ] );

		// Gutenberg block.
		add_action( 'init', [ $this, 'register_block' ] );

		// Sale flash modifications.
		add_action( 'wp_loaded', [ $this, 'modify_sale_flash_hooks' ] );

		// Sold out flash.
		add_action( 'woocommerce_before_single_product_summary', [ $this, 'single_sold_out_products_flash' ], 9 );
		add_action( 'woocommerce_before_shop_loop_item_title', [ $this, 'loop_sold_out_products_flash' ], 9 );
		add_action( 'woocommerce_before_shop_loop_item_title', [ $this, 'show_hide_loop_sale_flash' ], 10 );

		// Visibility filter for shortcode/block.
		add_filter( 'woocommerce_product_is_visible', [ $this, 'make_sold_out_products_visible' ], 10, 2 );
	}

	/**
	 * Modify WooCommerce sale flash hooks.
	 *
	 * @since 1.0.0
	 */
	public function modify_sale_flash_hooks() {
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	}

	/**
	 * Load plugin text domain for translations.
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain_files() {
		load_plugin_textdomain(
			'wc-sold-out-products',
			false,
			dirname( plugin_basename( $this->file ) ) . '/languages/'
		);
	}

	/**
	 * Enqueue frontend scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style(
			'wc-sold-out-products-style',
			WC_SOLD_OUT_PRODUCTS_URL . 'assets/css/style.css',
			[],
			WC_SOLD_OUT_PRODUCTS_VERSION
		);

		// Enqueue block styles on frontend.
		if ( file_exists( WC_SOLD_OUT_PRODUCTS_PATH . 'build/index.css' ) ) {
			wp_enqueue_style(
				'wc-sold-out-products-block-style',
				WC_SOLD_OUT_PRODUCTS_URL . 'build/index.css',
				[ 'wc-sold-out-products-style' ],
				WC_SOLD_OUT_PRODUCTS_VERSION
			);
		}
	}

	/**
	 * Enqueue block editor assets.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_block_editor_assets() {
		$asset_file = WC_SOLD_OUT_PRODUCTS_PATH . 'build/index.asset.php';

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$asset = require $asset_file;

		wp_enqueue_script(
			'wc-sold-out-products-block-editor',
			WC_SOLD_OUT_PRODUCTS_URL . 'build/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_enqueue_style(
			'wc-sold-out-products-block-editor-style',
			WC_SOLD_OUT_PRODUCTS_URL . 'build/index.css',
			[ 'wp-edit-blocks' ],
			WC_SOLD_OUT_PRODUCTS_VERSION
		);
	}

	/**
	 * Register the sold out products widget.
	 *
	 * @since 1.0.0
	 */
	public function register_widget() {
		require_once WC_SOLD_OUT_PRODUCTS_PATH . 'classes/class-wc-widget-sold-out-products.php';
		register_widget( 'WC_Widget_Sold_Out_Products' );
	}

	/**
	 * Register Gutenberg block.
	 *
	 * @since 2.0.0
	 */
	public function register_block() {
		// Check if block.json exists in build folder.
		$block_json = WC_SOLD_OUT_PRODUCTS_PATH . 'build/block.json';

		if ( file_exists( $block_json ) ) {
			register_block_type(
				$block_json,
				[
					'render_callback' => [ $this, 'render_block' ],
				]
			);
		} else {
			// Fallback: register block programmatically.
			register_block_type(
				'wc-sold-out-products/sold-out-products',
				[
					'api_version'     => 3,
					'editor_script'   => 'wc-sold-out-products-block-editor',
					'editor_style'    => 'wc-sold-out-products-block-editor-style',
					'style'           => 'wc-sold-out-products-block-style',
					'render_callback' => [ $this, 'render_block' ],
					'attributes'      => [
						'columns'  => [
							'type'    => 'number',
							'default' => 4,
						],
						'rows'     => [
							'type'    => 'number',
							'default' => 3,
						],
						'orderby'  => [
							'type'    => 'string',
							'default' => 'date',
						],
						'order'    => [
							'type'    => 'string',
							'default' => 'desc',
						],
						'className' => [
							'type'    => 'string',
							'default' => '',
						],
					],
				]
			);
		}
	}

	/**
	 * Render the Gutenberg block.
	 *
	 * @since 2.0.0
	 *
	 * @param array $attributes Block attributes.
	 * @return string Block HTML output.
	 */
	public function render_block( $attributes ) {
		$attributes = wp_parse_args(
			$attributes,
			[
				'columns'   => 4,
				'rows'      => 3,
				'orderby'   => 'date',
				'order'     => 'desc',
				'className' => '',
			]
		);

		$per_page = absint( $attributes['columns'] ) * absint( $attributes['rows'] );

		$shortcode_atts = [
			'per_page' => $per_page,
			'columns'  => absint( $attributes['columns'] ),
			'orderby'  => sanitize_text_field( $attributes['orderby'] ),
			'order'    => sanitize_text_field( $attributes['order'] ),
		];

		$output = $this->sold_out_products_shortcode( $shortcode_atts );

		$class_name = ! empty( $attributes['className'] ) ? ' ' . esc_attr( $attributes['className'] ) : '';

		return sprintf(
			'<div class="wp-block-wc-sold-out-products-sold-out-products%s">%s</div>',
			$class_name,
			$output
		);
	}

	/**
	 * Sold out products shortcode handler.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Shortcode HTML output.
	 */
	public function sold_out_products_shortcode( $atts ) {
		// Parse attributes.
		$atts = shortcode_atts(
			[
				'per_page' => 12,
				'columns'  => 4,
				'orderby'  => 'date',
				'order'    => 'desc',
			],
			$atts,
			'sold_out_products'
		);

		// Sanitize attributes.
		$per_page = absint( $atts['per_page'] );
		$columns  = absint( $atts['columns'] );
		$orderby  = sanitize_text_field( $atts['orderby'] );
		$order    = strtoupper( sanitize_text_field( $atts['order'] ) );

		// Validate order.
		if ( ! in_array( $order, [ 'ASC', 'DESC' ], true ) ) {
			$order = 'DESC';
		}

		// Set flag to allow sold out products to be visible.
		self::$shortcode_in_use = true;

		// Build product query args using modern WooCommerce methods.
		$args = [
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => $per_page,
			'orderby'             => $orderby,
			'order'               => $order,
			'tax_query'           => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				[
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => [ 'exclude-from-catalog' ],
					'operator' => 'NOT IN',
				],
			],
			'meta_query'          => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				[
					'key'     => '_stock_status',
					'value'   => 'outofstock',
					'compare' => '=',
				],
			],
		];

		// Apply WooCommerce visibility filter.
		$args = apply_filters( 'wc_sold_out_products_shortcode_query_args', $args, $atts );

		ob_start();

		$products = new WP_Query( $args );

		// Set loop properties using modern WooCommerce method.
		wc_set_loop_prop( 'columns', $columns );
		wc_set_loop_prop( 'is_shortcode', true );
		wc_set_loop_prop( 'name', 'sold_out_products' );
		wc_set_loop_prop( 'total', $products->found_posts );
		wc_set_loop_prop( 'total_pages', $products->max_num_pages );
		wc_set_loop_prop( 'per_page', $per_page );
		wc_set_loop_prop( 'current_page', max( 1, $products->get( 'paged' ) ) );

		if ( $products->have_posts() ) {
			/**
			 * Hook: woocommerce_before_shop_loop.
			 *
			 * @hooked woocommerce_output_all_notices - 10
			 * @hooked woocommerce_result_count - 20
			 * @hooked woocommerce_catalog_ordering - 30
			 */
			do_action( 'wc_sold_out_products_before_loop' );

			woocommerce_product_loop_start();

			while ( $products->have_posts() ) {
				$products->the_post();

				/**
				 * Hook: woocommerce_shop_loop.
				 */
				do_action( 'woocommerce_shop_loop' );

				wc_get_template_part( 'content', 'product' );
			}

			woocommerce_product_loop_end();

			/**
			 * Hook: woocommerce_after_shop_loop.
			 *
			 * @hooked woocommerce_pagination - 10
			 */
			do_action( 'wc_sold_out_products_after_loop' );
		} else {
			/**
			 * Hook: woocommerce_no_products_found.
			 */
			do_action( 'woocommerce_no_products_found' );
		}

		wp_reset_postdata();

		// Reset flag.
		self::$shortcode_in_use = false;

		// Reset loop props.
		wc_reset_loop();

		$output = ob_get_clean();

		return '<div class="woocommerce wc-sold-out-products-shortcode">' . $output . '</div>';
	}

	/**
	 * Display sold out flash on single product page.
	 *
	 * @since 1.0.0
	 */
	public function single_sold_out_products_flash() {
		global $product;

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		if ( ! $product->is_in_stock() ) {
			// Remove sale flash for sold out products.
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
		}

		wc_get_template(
			'single-product/sold-out-flash.php',
			[],
			'',
			WC_SOLD_OUT_PRODUCTS_PATH . 'templates/'
		);
	}

	/**
	 * Display sold out flash on shop loop.
	 *
	 * @since 1.0.1
	 */
	public function loop_sold_out_products_flash() {
		wc_get_template(
			'loop/sold-out-flash.php',
			[],
			'',
			WC_SOLD_OUT_PRODUCTS_PATH . 'templates/'
		);
	}

	/**
	 * Make sold out products visible when shortcode/block is used.
	 *
	 * @since 1.0.2
	 *
	 * @param bool $visible    Whether the product is visible.
	 * @param int  $product_id Product ID.
	 * @return bool Whether the product should be visible.
	 */
	public function make_sold_out_products_visible( $visible, $product_id ) {
		if ( ! self::$shortcode_in_use ) {
			return $visible;
		}

		if ( $visible ) {
			return $visible;
		}

		// Get product using modern method.
		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return $visible;
		}

		// If WooCommerce is set to hide out of stock items, make sold out products visible for our shortcode.
		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && ! $product->is_in_stock() ) {
			return true;
		}

		return $visible;
	}

	/**
	 * Show sale flash only for in-stock products.
	 *
	 * @since 1.0.3
	 */
	public function show_hide_loop_sale_flash() {
		global $product;

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		if ( $product->is_in_stock() ) {
			wc_get_template( 'loop/sale-flash.php' );
		}
	}

	/**
	 * Get sold out products for REST API or other uses.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args Query arguments.
	 * @return array Array of WC_Product objects.
	 */
	public static function get_sold_out_products( $args = [] ) {
		$defaults = [
			'limit'    => 12,
			'orderby'  => 'date',
			'order'    => 'DESC',
			'return'   => 'objects',
		];

		$args = wp_parse_args( $args, $defaults );

		$query_args = [
			'status'       => 'publish',
			'stock_status' => 'outofstock',
			'limit'        => absint( $args['limit'] ),
			'orderby'      => sanitize_text_field( $args['orderby'] ),
			'order'        => sanitize_text_field( $args['order'] ),
			'return'       => $args['return'],
		];

		return wc_get_products( $query_args );
	}

	/**
	 * Check if a product is sold out.
	 *
	 * @since 2.0.0
	 *
	 * @param int|WC_Product $product Product ID or object.
	 * @return bool Whether the product is sold out.
	 */
	public static function is_product_sold_out( $product ) {
		if ( is_numeric( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product instanceof WC_Product ) {
			return false;
		}

		return ! $product->is_in_stock();
	}
}
