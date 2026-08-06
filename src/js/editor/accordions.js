( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { RichText, InnerBlocks, useBlockProps } = wp.blockEditor;
	const { createElement: el } = wp.element;
	const { __ } = wp.i18n;

	// Child: a single accordion (summary + body).
	registerBlockType( 'chw/accordion', {
		title: __( 'Accordion', 'chw' ),
		description: __( 'A single collapsible accordion item.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'list-view',
		parent: [ 'chw/accordions' ],
		supports: {
			html: false,
		},
		attributes: {
			summary: { type: 'string', default: '' },
		},

		edit: function ( props ) {
			const blockProps = useBlockProps( { className: 'accordion', open: true } );

			return el(
				'div',
				blockProps,
				el( RichText, {
					tagName: 'div',
					className: '_summary',
					value: props.attributes.summary || '',
					allowedFormats: [ 'core/bold', 'core/italic' ],
					placeholder: __( 'Add a question…', 'chw' ),
					onChange: function ( value ) {
						props.setAttributes( { summary: value } );
					},
				} ),
				el(
					'div',
					{ className: '_inner' },
					el( InnerBlocks, {
						template: [ [ 'core/paragraph', { placeholder: __( 'Add the answer…', 'chw' ) } ] ],
						templateLock: false,
					} )
				)
			);
		},

		save: function () {
			return el( InnerBlocks.Content );
		},
	} );

	// Parent: container of accordion children.
	registerBlockType( 'chw/accordions', {
		title: __( 'Accordions', 'chw' ),
		description: __( 'A list of collapsible accordion items.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'menu',
		supports: {
			anchor: true,
			html: false,
		},

		edit: function () {
			const blockProps = useBlockProps( { className: 'accordions' } );

			return el(
				'div',
				blockProps,
				el( InnerBlocks, {
					allowedBlocks: [ 'chw/accordion' ],
					template: [ [ 'chw/accordion' ] ],
					templateLock: false,
				} )
			);
		},

		save: function () {
			return el( InnerBlocks.Content );
		},
	} );
} )( window.wp );
