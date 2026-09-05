( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { useBlockProps } = wp.blockEditor;
	const { createElement: el } = wp.element;
	const { __ } = wp.i18n;

	registerBlockType( 'andreian/post-share', {
		title: __( 'Post Share', 'andreian' ),
		description: __( 'Displays sharing links for the current post.', 'andreian' ),
		icon: 'share',
		category: 'widgets',
		edit: function () {
			return el(
				'div',
				useBlockProps(),
				el( 'strong', null, __( 'Post Share', 'andreian' ) ),
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
