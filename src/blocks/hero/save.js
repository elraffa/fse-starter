/**
 * Hero block — save component.
 *
 * Returns static HTML serialized into the post content.
 * InnerBlocks.Content renders the user's inner blocks in place.
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save() {
	const blockProps = useBlockProps.save( {
		className: 'wp-block-geller2026-hero',
	} );

	return (
		<section { ...blockProps }>
			<div className="hero__inner">
				<InnerBlocks.Content />
			</div>
		</section>
	);
}
