/**
 * WooCommerce Sold Out Products Block
 *
 * @package Kloon\WCSoldOutProducts
 */

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import Edit from './blocks/sold-out-products/edit';
import metadata from './blocks/sold-out-products/block.json';
import './blocks/sold-out-products/style.scss';
import './blocks/sold-out-products/editor.scss';

/**
 * Sold out product icon
 */
const SoldOutIcon = () => (
	<svg
		xmlns="http://www.w3.org/2000/svg"
		viewBox="0 0 24 24"
		width="24"
		height="24"
	>
		<path
			fill="currentColor"
			d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"
		/>
	</svg>
);

/**
 * Register the block
 */
registerBlockType( metadata.name, {
	...metadata,
	icon: SoldOutIcon,
	edit: Edit,
	save: () => null, // Dynamic block, rendered on server
} );
