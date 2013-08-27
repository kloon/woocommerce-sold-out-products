=== WooCommerce Sold Out Products ===
Contributors: Kloon
Donate link: http://gerhardpotgieter.com/donate
Tags: woocommerce, products, sold out, widget, shortcode
Requires at least: 3.5
Tested up to: 3.5.2
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Extends WooCommerce by adding a widget and shortcode for displaying sold out products

== Description ==

"WooCommerce Sold Out Products" enhances the built-in WooCommerce functionality by adding a Widget and Shortcode for displaying sold out products. The WooCommerce Sold Out Products extension uses native WooCommerce functionality ensuring that the widget and shortcode content for the sold out products integrates with your current WooCommerce templates ensuring uniformity on your WooCommerce store.

This plugin is WooCommerce 2.x compatible.

Contribute to the plugin via [github](https://github.com/kloon/woocommerce-sold-out-products)

== Usage ==

Widget

1. Go to Appearance -> Widgets
1. Drag the 'WooCommerce Sold Out Products' widget to your desired widget area
1. Select the setting and save.

Shortcode

The shortcode to use is sold_out_products and takes the following arguments:
* per_page - Number of products to display
* columns - Number of columns per row
* orderby - Order by http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters ( default: date )
* order - asc or desc ( default: desc )

[sold_out_products per_page="10" columns="4"]

== Installation ==

Installing "WooCommerce Sold Out Products" can be done either by searching for "WooCommerce Sold Out Products" via the "Plugins > Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org
1. Upload the ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress dashboard
1. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. The Widget on Twenty Twelve theme
1. Shortcode on a page
1. Sold Out Flash on single product page

== Changelog ==

= 1.0.4 = 2013-08-27 =
* Version bump to fix missing templates files

= 1.0.3 - 2013-08-14 =
* Fix missing sale badges after first sold out badge

= 1.0.2 - 2013-07-03 =
* Show sold out products on shortcode page when WC has it set to hide

= 1.0.1 - 2013-05-16 =
* Sold Out flash on shop archive pages

= 1.0 - 2013-05-10 =
* Initial release!

== Frequently Asked Questions ==

= Nothing here yet =
