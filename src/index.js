/**
 * External dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Edit from './edit';
import Save from './save';
import block from '../block.json';
import cartIcon from './utils';

registerBlockType( block, {
	icon: {
		foreground: '#7f54b3',
		src: cartIcon,
	},
	edit: Edit,
	save: Save,
} );
