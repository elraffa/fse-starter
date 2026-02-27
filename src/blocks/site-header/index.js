/**
 * Site Header block — editor registration.
 *
 * Uses ServerSideRender so the Site Editor shows the live PHP-rendered
 * header instead of a generic placeholder.
 */
import { registerBlockType } from '@wordpress/blocks';
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps } from '@wordpress/block-editor';

import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit() {
		const blockProps = useBlockProps( {
			style: { margin: 0, padding: 0 },
		} );

		return (
			<div { ...blockProps }>
				<ServerSideRender block={ metadata.name } />
			</div>
		);
	},

	// Server-side rendered — no static save output.
	save: () => null,
} );
