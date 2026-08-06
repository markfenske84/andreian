( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { RichText, InnerBlocks, useBlockProps } = wp.blockEditor;
	const { createElement: el } = wp.element;
	const { __ } = wp.i18n;

	// Child: a single tab pane (label + body).
	registerBlockType( 'chw/tab-pane', {
		title: __( 'Tab Pane', 'chw' ),
		description: __( 'A single labelled tab pane.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'index-card',
		parent: [ 'chw/tabbed-panes' ],
		supports: {
			html: false,
		},
		attributes: {
			label: { type: 'string', default: '' },
		},

		edit: function ( props ) {
			const blockProps = useBlockProps( { className: 'tab-pane-editor' } );

			return el(
				'div',
				blockProps,
				el( RichText, {
					tagName: 'div',
					className: 'tab-pane-editor__label',
					value: props.attributes.label || '',
					allowedFormats: [],
					placeholder: __( 'Tab label', 'chw' ),
					onChange: function ( value ) {
						props.setAttributes( { label: value } );
					},
				} ),
				el(
					'div',
					{ className: 'tab-pane-editor__body' },
					el( InnerBlocks, {
						templateLock: false,
					} )
				)
			);
		},

		save: function () {
			return el( InnerBlocks.Content );
		},
	} );

	// Parent: container of tab panes.
	registerBlockType( 'chw/tabbed-panes', {
		title: __( 'Tabbed Panes', 'chw' ),
		description: __( 'A set of tabbed content panes.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'index-card',
		supports: {
			anchor: true,
			html: false,
		},

		edit: function () {
			const blockProps = useBlockProps( { className: 'tabbed-panes-editor' } );

			return el(
				'div',
				blockProps,
				el( InnerBlocks, {
					allowedBlocks: [ 'chw/tab-pane' ],
					template: [ [ 'chw/tab-pane' ] ],
					templateLock: false,
				} )
			);
		},

		save: function () {
			return el( InnerBlocks.Content );
		},
	} );
} )( window.wp );
