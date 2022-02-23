/**
 * Demonstration of how to use filters for the cart and checkout block.
 * Filters demonstrated here:
 * 1 - cart item name
 * 2 - totalLabel
 *
 * For more examples check the documentation here:
 * https://github.com/woocommerce/woocommerce-gutenberg-products-block/blob/trunk/docs/extensibility/available-filters.md#cart-line-items
 */

/**
 * External dependencies
 */
import { __experimentalRegisterCheckoutFilters } from '@woocommerce/blocks-checkout';

const changeCartItemName = ( itemName ) => {
	return `Awesome product name: ${ itemName }`;
};

const changeTotalLabel = () => {
	return `Awesome total:`;
};

__experimentalRegisterCheckoutFilters( 'my-extension-namespace', {
	itemName: changeCartItemName,
	totalLabel: changeTotalLabel,
} );
