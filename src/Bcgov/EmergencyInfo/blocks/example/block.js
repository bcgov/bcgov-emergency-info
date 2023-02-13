/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';

const { name } = metadata;
console.log(name, metadata)
registerBlockType( { name, metadata });