/**
 * ESLint configuration for WooCommerce Sold Out Products
 *
 * @package Kloon\WCSoldOutProducts
 */

module.exports = {
	extends: [ 'plugin:@woocommerce/eslint-plugin/recommended' ],
	env: {
		browser: true,
		es2021: true,
	},
	globals: {
		wp: 'readonly',
	},
	settings: {
		'import/resolver': {
			node: {
				extensions: [ '.js', '.jsx' ],
			},
		},
	},
	rules: {
		// Customize rules as needed.
		'@wordpress/i18n-text-domain': [
			'error',
			{
				allowedTextDomain: 'wc-sold-out-products',
			},
		],
	},
};
