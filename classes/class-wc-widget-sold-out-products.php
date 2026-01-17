<?php
/**
 * WooCommerce Sold Out Products Widget
 *
 * Displays a list of sold out products in widget areas.
 *
 * @since   1.0.0
 * @package Kloon\WCSoldOutProducts
 * @author  Gerhard Potgieter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sold Out Products Widget class.
 *
 * @since 1.0.0
 */
class WC_Widget_Sold_Out_Products extends WP_Widget {

	/**
	 * Widget CSS class.
	 *
	 * @var string
	 */
	public $widget_cssclass;

	/**
	 * Widget description.
	 *
	 * @var string
	 */
	public $widget_description;

	/**
	 * Widget ID base.
	 *
	 * @var string
	 */
	public $widget_id;

	/**
	 * Widget name.
	 *
	 * @var string
	 */
	public $widget_name;

	/**
	 * Widget settings.
	 *
	 * @var array
	 */
	public $settings;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->widget_cssclass    = 'woocommerce widget_sold_out_products';
		$this->widget_description = __( 'Display a list of your most recent sold out products on your site.', 'wc-sold-out-products' );
		$this->widget_id          = 'woocommerce_sold_out_products';
		$this->widget_name        = __( 'WooCommerce Sold Out Products', 'wc-sold-out-products' );

		$this->settings = [
			'title'           => [
				'type'  => 'text',
				'std'   => __( 'Sold Out Products', 'wc-sold-out-products' ),
				'label' => __( 'Title', 'wc-sold-out-products' ),
			],
			'number'          => [
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => 15,
				'std'   => 5,
				'label' => __( 'Number of products to show', 'wc-sold-out-products' ),
			],
			'show_variations' => [
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show hidden product variations', 'wc-sold-out-products' ),
			],
		];

		$widget_ops = [
			'classname'                   => $this->widget_cssclass,
			'description'                 => $this->widget_description,
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
		];

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

		add_action( 'save_post', [ $this, 'flush_widget_cache' ] );
		add_action( 'deleted_post', [ $this, 'flush_widget_cache' ] );
		add_action( 'switch_theme', [ $this, 'flush_widget_cache' ] );
		add_action( 'woocommerce_product_set_stock_status', [ $this, 'flush_widget_cache' ] );
	}

	/**
	 * Output the widget content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args     Widget display arguments.
	 * @param array $instance Widget settings.
	 */
	public function widget( $args, $instance ) {
		$cache = $this->get_cached_widget( $args );

		if ( false !== $cache ) {
			echo $cache; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		ob_start();

		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];
		$number = max( 1, min( 15, $number ) );

		$show_variations = ! empty( $instance['show_variations'] );

		// Modern WooCommerce product query.
		$query_args = [
			'posts_per_page' => $number,
			'no_found_rows'  => true,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_query'     => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				[
					'key'     => '_stock_status',
					'value'   => 'outofstock',
					'compare' => '=',
				],
			],
			'tax_query'      => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				[
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => [ 'exclude-from-catalog' ],
					'operator' => 'NOT IN',
				],
			],
		];

		// Exclude variations if not showing them.
		if ( ! $show_variations ) {
			$query_args['post_parent'] = 0;
		}

		/**
		 * Filter the widget query args.
		 *
		 * @since 2.0.0
		 *
		 * @param array $query_args Query arguments.
		 * @param array $instance   Widget instance settings.
		 */
		$query_args = apply_filters( 'wc_sold_out_products_widget_query_args', $query_args, $instance );

		$products = new WP_Query( $query_args );

		if ( $products->have_posts() ) {
			$this->widget_start( $args, $instance );

			echo '<ul class="product_list_widget">';

			while ( $products->have_posts() ) {
				$products->the_post();

				global $product;

				if ( ! $product instanceof WC_Product ) {
					continue;
				}

				$product_permalink = $product->get_permalink();
				$product_name      = $product->get_name();

				echo '<li class="wc-sold-out-product-item">';
				echo '<a href="' . esc_url( $product_permalink ) . '" title="' . esc_attr( $product_name ) . '">';

				// Product thumbnail.
				if ( $product->get_image_id() ) {
					echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) );
				} else {
					echo wp_kses_post( wc_placeholder_img( 'woocommerce_thumbnail' ) );
				}

				// Product name.
				echo '<span class="product-title">' . esc_html( $product_name ) . '</span>';
				echo '</a>';

				// Product price.
				if ( $price_html = $product->get_price_html() ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.Found
					echo '<span class="price">' . wp_kses_post( $price_html ) . '</span>';
				}

				echo '</li>';
			}

			echo '</ul>';

			$this->widget_end( $args );
		}

		wp_reset_postdata();

		$content = ob_get_clean();

		if ( ! empty( $content ) ) {
			$this->cache_widget( $args, $content );
			echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Output the start of the widget.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args     Widget display arguments.
	 * @param array $instance Widget settings.
	 */
	protected function widget_start( $args, $instance ) {
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$title = ! empty( $instance['title'] ) ? $instance['title'] : $this->settings['title']['std'];

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		if ( $title ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Output the end of the widget.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args Widget display arguments.
	 */
	protected function widget_end( $args ) {
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Update widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance New widget settings.
	 * @param array $old_instance Old widget settings.
	 * @return array Updated widget settings.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		if ( empty( $this->settings ) ) {
			return $instance;
		}

		foreach ( $this->settings as $key => $setting ) {
			if ( isset( $new_instance[ $key ] ) ) {
				$type = isset( $setting['type'] ) ? $setting['type'] : 'text';

				switch ( $type ) {
					case 'number':
						$instance[ $key ] = absint( $new_instance[ $key ] );
						if ( isset( $setting['min'] ) && $instance[ $key ] < $setting['min'] ) {
							$instance[ $key ] = $setting['min'];
						}
						if ( isset( $setting['max'] ) && $instance[ $key ] > $setting['max'] ) {
							$instance[ $key ] = $setting['max'];
						}
						break;

					case 'checkbox':
						$instance[ $key ] = ! empty( $new_instance[ $key ] ) ? 1 : 0;
						break;

					default:
						$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
						break;
				}
			} elseif ( 'checkbox' === $setting['type'] ) {
				$instance[ $key ] = 0;
			}
		}

		$this->flush_widget_cache();

		return $instance;
	}

	/**
	 * Output the widget settings form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Current widget settings.
	 */
	public function form( $instance ) {
		if ( empty( $this->settings ) ) {
			return;
		}

		foreach ( $this->settings as $key => $setting ) {
			$type  = isset( $setting['type'] ) ? $setting['type'] : 'text';
			$value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];
			$class = isset( $setting['class'] ) ? $setting['class'] : '';

			switch ( $type ) {
				case 'text':
					?>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>">
							<?php echo esc_html( $setting['label'] ); ?>
						</label>
						<input
							class="widefat <?php echo esc_attr( $class ); ?>"
							id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"
							name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>"
							type="text"
							value="<?php echo esc_attr( $value ); ?>"
						/>
					</p>
					<?php
					break;

				case 'number':
					?>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>">
							<?php echo esc_html( $setting['label'] ); ?>
						</label>
						<input
							class="widefat <?php echo esc_attr( $class ); ?>"
							id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"
							name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>"
							type="number"
							step="<?php echo esc_attr( isset( $setting['step'] ) ? $setting['step'] : 1 ); ?>"
							min="<?php echo esc_attr( isset( $setting['min'] ) ? $setting['min'] : '' ); ?>"
							max="<?php echo esc_attr( isset( $setting['max'] ) ? $setting['max'] : '' ); ?>"
							value="<?php echo esc_attr( $value ); ?>"
						/>
					</p>
					<?php
					break;

				case 'checkbox':
					?>
					<p>
						<input
							class="checkbox <?php echo esc_attr( $class ); ?>"
							id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"
							name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>"
							type="checkbox"
							value="1"
							<?php checked( $value, 1 ); ?>
						/>
						<label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>">
							<?php echo esc_html( $setting['label'] ); ?>
						</label>
					</p>
					<?php
					break;
			}
		}
	}

	/**
	 * Flush the widget cache.
	 *
	 * @since 1.0.0
	 */
	public function flush_widget_cache() {
		$cache_key = 'wc_sold_out_products_widget';
		wp_cache_delete( $cache_key, 'widget' );
	}

	/**
	 * Get cached widget content.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args Widget display arguments.
	 * @return string|false Cached content or false if not cached.
	 */
	protected function get_cached_widget( $args ) {
		if ( ! isset( $args['widget_id'] ) ) {
			return false;
		}

		$cache_key = 'wc_sold_out_products_widget';
		$cache     = wp_cache_get( $cache_key, 'widget' );

		if ( ! is_array( $cache ) ) {
			return false;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			return $cache[ $args['widget_id'] ];
		}

		return false;
	}

	/**
	 * Cache the widget content.
	 *
	 * @since 2.0.0
	 *
	 * @param array  $args    Widget display arguments.
	 * @param string $content Widget content to cache.
	 */
	protected function cache_widget( $args, $content ) {
		if ( ! isset( $args['widget_id'] ) ) {
			return;
		}

		$cache_key = 'wc_sold_out_products_widget';
		$cache     = wp_cache_get( $cache_key, 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = [];
		}

		$cache[ $args['widget_id'] ] = $content;

		wp_cache_set( $cache_key, $cache, 'widget', DAY_IN_SECONDS );
	}
}
