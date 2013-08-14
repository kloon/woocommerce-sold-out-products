<?php
/**
 * WooCommerce Sold Out Products
 *
 * This class handles all the plugin functionality
 *
 * @since		1.0
 * @package		WooCommerce
 * @category	Class
 * @author 		Gerhard Potgieter
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Sold_Out_Products {
	protected $file;
	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct( $file ) {
		$this->file = $file;
		add_action( 'plugins_loaded', array( $this, 'load_textdomain_files' ) );
		add_action( 'plugins_loaded', array( $this, 'unload_woocommerce_actions' ) );
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_stylesheet' ) );
		add_shortcode( 'sold_out_products', array( $this, 'sold_out_products_shortcode' ) );
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'single_sold_out_products_flash' ), 9 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'loop_sold_out_products_flash' ), 9 );
		add_filter( 'woocommerce_product_is_visible', array( $this, 'make_sold_out_products_visible' ), 10, 2 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'show_hide_loop_sale_flash' ), 10 );
	}

	function unload_woocommerce_actions() {
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	}

	/**
	 * load localization files
	 *
	 * @access public
	 * @return void
	 */
	function load_textdomain_files() {
		load_plugin_textdomain( 'wc-sold-out-products', false, dirname( plugin_basename( $this->file ) ) . '/languages/' );
	}

	function enqueue_stylesheet() {
		wp_register_style( 'wc-sold-out-products-stylesheet', plugins_url( 'assets/css/style.css', $this->file ) );
		wp_enqueue_style( 'wc-sold-out-products-stylesheet' );
	}

	/**
	 * register widget function.
	 *
	 * @access public
	 * @return void
	 */
	function register_widget() {
		require_once( 'class-wc-widget-sold-out-products.php' );
		register_widget( 'WC_Widget_Sold_Out_Products' );
	}

	/**
	 * shortcode function.
	 *
	 * @access public
	 * @return string
	 */
	function sold_out_products_shortcode( $atts ) {
		global $woocommerce_loop, $woocommerce, $sold_out_shortcode_used;
		$sold_out_shortcode_used = true;

		extract( shortcode_atts( array(
			'per_page' 	=> '12',
			'columns' 	=> '4',
			'orderby' => 'date',
			'order' => 'desc'
		), $atts ) );

		$meta_query = array();
		$meta_query[] = array(
			'key'     => '_visibility',
			'value'   => array( 'visible', 'catalog' ),
			'compare' => 'IN'
		);
		$meta_query[] = array(
			'key' 		=> '_stock_status',
			'value' 	=> 'outofstock',
			'compare' 	=> '='
		);

		$args = array(
			'post_type'	=> 'product',
			'post_status' => 'publish',
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' => $per_page,
			'orderby' => $orderby,
			'order' => $order,
			'meta_query' => $meta_query
		);

		ob_start();

		$products = new WP_Query( $args );

		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php woocommerce_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

		wp_reset_postdata();

		return '<div class="woocommerce">' . ob_get_clean() . '</div>';
	}

	/**
	 * add sold out text to the product image
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	function single_sold_out_products_flash() {
		global $product;
		if ( ! $product->is_in_stock() ) {
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
		}
		$plugin_path = trailingslashit( plugin_dir_path( $this->file ) );
		woocommerce_get_template( 'single-product/sold-out-flash.php', '', '', $plugin_path . 'templates/' );
	}

	/**
	 * add sold out text to the product image on shop page
	 *
	 * @since 1.0.1
	 * @access public
	 * @return void
	 */
	function loop_sold_out_products_flash() {
		global $product;
		$plugin_path = trailingslashit( plugin_dir_path( $this->file ) );
		woocommerce_get_template( 'loop/sold-out-flash.php', '', '', $plugin_path . 'templates/' );
	}

	/**
	 * Make all sold out products visible when shortcode is used
	 *
	 * @since 1.0.2
	 * @access public
	 * @return bool
	 */
	function make_sold_out_products_visible( $visible, $product_id ) {
		global $sold_out_shortcode_used;
		if ( $sold_out_shortcode_used ) {
			if ( $visible )
				return $visible;

			$product = get_product( $product_id );
			if ( get_option( 'woocommerce_hide_out_of_stock_items' ) == 'yes' && ! $product->is_in_stock() )
				return true;
			else return $visible;
		} else return $visible;
	}

	/**
	 * Only load sale flash when product not sold out
	 *
	 * @since 1.0.3
	 * @access public
	 * @return bool
	 */
	function show_hide_loop_sale_flash() {
		global $product;
		if ( $product->is_in_stock() ) {
			woocommerce_get_template( 'loop/sale-flash.php' );
		}
	}
}

?>