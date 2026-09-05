( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { useBlockProps } = wp.blockEditor;
	const { createElement: el } = wp.element;
	const { __ } = wp.i18n;

	registerBlockType( 'andreian/table-of-contents', {
		title: __( 'Andreian Table of Contents', 'andreian' ),
		description: __( 'Jump links for h2 and h3 headings in the current content.', 'andreian' ),
		icon: 'list-view',
		category: 'widgets',
		keywords: [ 'toc', 'contents', 'headings', 'sidebar' ],
		edit: function () {
			return el(
				'div',
				useBlockProps(),
				el( 'strong', null, __( 'Andreian Table of Contents', 'andreian' ) ),
				el(
					'p',
					null,
					__( 'Heading links appear here on the front end.', 'andreian' )
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
