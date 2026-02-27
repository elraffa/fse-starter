/**
 * Hero block — entry point.
 *
 * Registers the block type using the metadata from block.json,
 * wired up to the React edit and save components.
 */
import { registerBlockType } from '@wordpress/blocks';

import metadata from './block.json';
import Edit from './edit';
import save from './save';

import './style.scss';

registerBlockType( metadata.name, {
	edit: Edit,
	save,
} );
