/**
 * Webpack configuration for WooCommerce Sold Out Products
 *
 * @package Kloon\WCSoldOutProducts
 */

const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );
const CopyPlugin = require( 'copy-webpack-plugin' );

module.exports = {
	...defaultConfig,
	entry: {
		index: path.resolve( __dirname, 'src', 'index.js' ),
	},
	output: {
		...defaultConfig.output,
		path: path.resolve( __dirname, 'build' ),
	},
	plugins: [
		...defaultConfig.plugins.filter(
			( plugin ) => plugin.constructor.name !== 'CopyPlugin'
		),
		new CopyPlugin( {
			patterns: [
				{
					from: path.resolve(
						__dirname,
						'src/blocks/sold-out-products/block.json'
					),
					to: path.resolve( __dirname, 'build/block.json' ),
				},
				{
					from: path.resolve(
						__dirname,
						'src/blocks/sold-out-products/render.php'
					),
					to: path.resolve( __dirname, 'build/render.php' ),
				},
			],
		} ),
	],
};
