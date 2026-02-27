/**
 * Hero block — editor component.
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Starter template injected into InnerBlocks on first insertion.
 * Uses core blocks so no extra dependencies are needed.
 */
const TEMPLATE = [
	[
		'core/heading',
		{
			level: 1,
			placeholder: __( 'Hero headline…', 'geller2026' ),
			textAlign: 'center',
			style: {
				typography: { fontSize: 'var(--wp--preset--font-size--4xl)' },
			},
		},
	],
	[
		'core/paragraph',
		{
			placeholder: __( 'Supporting subtext goes here.', 'geller2026' ),
			align: 'center',
			style: {
				typography: { fontSize: 'var(--wp--preset--font-size--lg)' },
				color: { text: 'var(--wp--preset--color--muted)' },
			},
		},
	],
	[
		'core/buttons',
		{
			layout: { type: 'flex', justifyContent: 'center' },
			style: { spacing: { margin: { top: '1.5rem' } } },
		},
		[
			[
				'core/button',
				{
					text: __( 'Get Started', 'geller2026' ),
					backgroundColor: 'accent',
					textColor: 'base',
				},
			],
		],
	],
];

/**
 * Edit component — renders inside the block editor.
 *
 * @param {Object} props Block props from the editor.
 */
export default function Edit( props ) {
	const blockProps = useBlockProps( {
		className: 'wp-block-geller2026-hero',
	} );

	return (
		<section { ...blockProps }>
			<div className="hero__inner">
				<InnerBlocks
					template={ TEMPLATE }
					templateLock={ false }
					templateInsertUpdatesSelection={ true }
				/>
			</div>
		</section>
	);
}
