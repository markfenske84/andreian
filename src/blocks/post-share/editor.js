( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { useBlockProps } = wp.blockEditor;
	const { createElement: el } = wp.element;
	const { __ } = wp.i18n;

	registerBlockType( 'andreian/post-share', {
		title: __( 'Andreian Share Links', 'andreian' ),
		description: __( 'Displays social sharing icons for the current post.', 'andreian' ),
		icon: 'share',
		category: 'widgets',
		keywords: [ 'share', 'social', 'facebook', 'sidebar' ],
		edit: function () {
			return el(
				'div',
				useBlockProps(),
				el( 'strong', null, __( 'Andreian Share Links', 'andreian' ) ),
				el(
					'p',
					null,
					__( 'Sharing icons appear here on single posts.', 'andreian' )
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
