/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';

registerBlockType(metadata, {
	edit: () => {
		return 'Admin example';
	},
	save: () => {
		return 'Frontend example';
	},
});
