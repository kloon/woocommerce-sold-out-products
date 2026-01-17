/**
 * Sold Out Products Block Editor Component
 *
 * @package Kloon\WCSoldOutProducts
 */

import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	Placeholder,
	Spinner,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Editor component for the Sold Out Products block.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to set block attributes.
 * @return {Element} Block editor component.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { columns, rows, orderby, order } = attributes;

	const blockProps = useBlockProps( {
		className: 'wc-sold-out-products-block',
	} );

	// Check if WooCommerce is active
	const isWooCommerceActive = useSelect( ( select ) => {
		// Check if WooCommerce store exists
		const stores = select( 'core' )?.getEntityRecords?.( 'postType', 'product', { per_page: 1 } );
		return stores !== null;
	}, [] );

	const orderbyOptions = [
		{ label: __( 'Date', 'wc-sold-out-products' ), value: 'date' },
		{ label: __( 'Title', 'wc-sold-out-products' ), value: 'title' },
		{ label: __( 'Price', 'wc-sold-out-products' ), value: 'price' },
		{ label: __( 'Popularity', 'wc-sold-out-products' ), value: 'popularity' },
		{ label: __( 'Rating', 'wc-sold-out-products' ), value: 'rating' },
		{ label: __( 'Random', 'wc-sold-out-products' ), value: 'rand' },
		{ label: __( 'Menu Order', 'wc-sold-out-products' ), value: 'menu_order' },
	];

	const orderOptions = [
		{ label: __( 'Descending', 'wc-sold-out-products' ), value: 'desc' },
		{ label: __( 'Ascending', 'wc-sold-out-products' ), value: 'asc' },
	];

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Layout', 'wc-sold-out-products' ) }
					initialOpen={ true }
				>
					<RangeControl
						label={ __( 'Columns', 'wc-sold-out-products' ) }
						value={ columns }
						onChange={ ( value ) =>
							setAttributes( { columns: value } )
						}
						min={ 1 }
						max={ 6 }
					/>
					<RangeControl
						label={ __( 'Rows', 'wc-sold-out-products' ) }
						value={ rows }
						onChange={ ( value ) => setAttributes( { rows: value } ) }
						min={ 1 }
						max={ 10 }
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Sorting', 'wc-sold-out-products' ) }
					initialOpen={ false }
				>
					<SelectControl
						label={ __( 'Order by', 'wc-sold-out-products' ) }
						value={ orderby }
						options={ orderbyOptions }
						onChange={ ( value ) =>
							setAttributes( { orderby: value } )
						}
					/>
					<SelectControl
						label={ __( 'Order', 'wc-sold-out-products' ) }
						value={ order }
						options={ orderOptions }
						onChange={ ( value ) =>
							setAttributes( { order: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<ServerSideRender
					block="wc-sold-out-products/sold-out-products"
					attributes={ attributes }
					LoadingResponsePlaceholder={ () => (
						<Placeholder
							icon="cart"
							label={ __( 'Sold Out Products', 'wc-sold-out-products' ) }
						>
							<Spinner />
						</Placeholder>
					) }
					ErrorResponsePlaceholder={ () => (
						<Placeholder
							icon="warning"
							label={ __( 'Sold Out Products', 'wc-sold-out-products' ) }
						>
							{ __( 'Error loading products. Please check that WooCommerce is active.', 'wc-sold-out-products' ) }
						</Placeholder>
					) }
					EmptyResponsePlaceholder={ () => (
						<Placeholder
							icon="cart"
							label={ __( 'Sold Out Products', 'wc-sold-out-products' ) }
						>
							{ __( 'No sold out products found. Products will appear here when they go out of stock.', 'wc-sold-out-products' ) }
						</Placeholder>
					) }
				/>
			</div>
		</>
	);
}
