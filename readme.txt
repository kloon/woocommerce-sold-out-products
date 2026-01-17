=== WooCommerce Sold Out Products ===
Contributors: Kloon
Donate link: https://github.com/sponsors/kloon
Tags: woocommerce, products, sold out, widget, block, gutenberg
Requires at least: 6.4
Tested up to: 6.7
Stable tag: 2.0.0
Requires PHP: 7.4
WC requires at least: 8.5
WC tested up to: 9.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display sold out products with a Gutenberg block, widget, or shortcode for WooCommerce.

== Description ==

**WooCommerce Sold Out Products** enhances WooCommerce by adding multiple ways to display your sold out products:

* **Gutenberg Block** - A modern block editor experience with live preview and customizable settings
* **Widget** - Classic widget for sidebars and widget areas
* **Shortcode** - For use in classic editor or anywhere shortcodes are supported

The plugin uses native WooCommerce templates ensuring that sold out products integrate seamlessly with your current WooCommerce theme.

= Features =

* **Gutenberg Block** with customizable columns, rows, and sorting options
* **Classic Widget** for displaying sold out products in sidebars
* **Shortcode** for flexible placement anywhere on your site
* **"Sold Out!" Badges** displayed on product images in shop loops and single product pages
* **HPOS Compatible** - Works with WooCommerce High-Performance Order Storage
* **Block Editor Compatible** - Full support for the WordPress block editor
* **Template Overrides** - Customize the sold out badge by copying templates to your theme

= Requirements =

* WordPress 6.4 or higher
* WooCommerce 8.5 or higher
* PHP 7.4 or higher

= Links =

* [GitHub Repository](https://github.com/kloon/woocommerce-sold-out-products)
* [Report Issues](https://github.com/kloon/woocommerce-sold-out-products/issues)

== Installation ==

= Automatic Installation =

1. Go to Plugins > Add New in your WordPress dashboard
2. Search for "WooCommerce Sold Out Products"
3. Click "Install Now" and then "Activate"

= Manual Installation =

1. Download the plugin ZIP file from WordPress.org
2. Go to Plugins > Add New > Upload Plugin
3. Upload the ZIP file and click "Install Now"
4. Activate the plugin

== Usage ==

= Gutenberg Block =

1. Edit any page or post with the block editor
2. Click the "+" button to add a new block
3. Search for "Sold Out Products"
4. Configure the block settings in the sidebar:
   * **Columns** - Number of products per row (1-6)
   * **Rows** - Number of rows to display (1-10)
   * **Order by** - Sort products by date, title, price, popularity, rating, or menu order
   * **Order** - Ascending or descending

= Widget =

1. Go to Appearance > Widgets
2. Find "WooCommerce Sold Out Products" widget
3. Drag it to your desired widget area
4. Configure the settings:
   * **Title** - Widget title
   * **Number of products** - How many products to show (1-15)
   * **Show hidden product variations** - Include product variations

= Shortcode =

Use the `[sold_out_products]` shortcode with these optional attributes:

* `per_page` - Number of products to display (default: 12)
* `columns` - Number of columns per row (default: 4)
* `orderby` - Sort by: date, title, price, popularity, rating, rand, menu_order (default: date)
* `order` - Sort direction: asc or desc (default: desc)

**Example:**
`[sold_out_products per_page="8" columns="4" orderby="date" order="desc"]`

= Template Overrides =

You can customize the sold out badge by copying templates to your theme:

* `templates/loop/sold-out-flash.php` → `yourtheme/woocommerce/loop/sold-out-flash.php`
* `templates/single-product/sold-out-flash.php` → `yourtheme/woocommerce/single-product/sold-out-flash.php`

= Filters =

**woocommerce_sold_out_flash**
Modify the sold out badge HTML.

`add_filter( 'woocommerce_sold_out_flash', function( $html, $post, $product ) {
    return '<span class="my-custom-badge">Out of Stock</span>';
}, 10, 3 );`

**wc_sold_out_products_shortcode_query_args**
Modify the shortcode/block product query.

**wc_sold_out_products_widget_query_args**
Modify the widget product query.

== Screenshots ==

1. Gutenberg block in the editor with settings panel
2. Sold out products displayed on the frontend
3. Widget configuration in the customizer
4. Sold Out badge on single product page

== Frequently Asked Questions ==

= Does this plugin work with WooCommerce HPOS? =

Yes! Version 2.0.0 fully supports WooCommerce High-Performance Order Storage (HPOS).

= Can I customize the "Sold Out" badge? =

Yes, you can either:
1. Override the template files in your theme
2. Use the `woocommerce_sold_out_flash` filter
3. Add custom CSS to style the `.soldout` class

= Does the block work with Full Site Editing themes? =

Yes, the Sold Out Products block works with both classic themes and Full Site Editing (FSE) themes.

= How do I show sold out products that are hidden in WooCommerce settings? =

The shortcode and block automatically show sold out products even when WooCommerce is set to hide out-of-stock items from the catalog.

== Changelog ==

= 2.0.0 - 2024-01-17 =
**Major Update - Complete Modernization**

* **New:** Gutenberg block for displaying sold out products with live preview
* **New:** HPOS (High-Performance Order Storage) compatibility
* **New:** Cart/Checkout blocks compatibility declaration
* **New:** Modern development setup with wp-env, PHPCS (WooCommerce sniffs), and PHPUnit
* **New:** Node.js 22+ and pnpm 9+ support
* **Updated:** Minimum requirements - WordPress 6.4+, WooCommerce 8.5+, PHP 7.4+
* **Updated:** Complete code refactor following WooCommerce coding standards
* **Updated:** Modern WooCommerce API usage (replaced deprecated functions)
* **Updated:** Widget rewritten with modern WordPress widget API
* **Updated:** CSS modernized with flexbox/grid, dark mode support
* **Updated:** Templates with proper escaping and type checking
* **Fixed:** Product visibility queries updated for WooCommerce 3.0+ taxonomy
* **Fixed:** Sale flash display logic improved
* **Improved:** Caching for widget output
* **Improved:** Block editor experience with ServerSideRender

= 1.0.4 - 2013-08-27 =
* Version bump to fix missing templates files

= 1.0.3 - 2013-08-14 =
* Fix missing sale badges after first sold out badge

= 1.0.2 - 2013-07-03 =
* Show sold out products on shortcode page when WC has it set to hide

= 1.0.1 - 2013-05-16 =
* Sold Out flash on shop archive pages

= 1.0 - 2013-05-10 =
* Initial release!

== Upgrade Notice ==

= 2.0.0 =
Major update with Gutenberg block support, HPOS compatibility, and modernized codebase. Requires WordPress 6.4+, WooCommerce 8.5+, and PHP 7.4+.
