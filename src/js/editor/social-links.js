( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { useBlockProps } = wp.blockEditor;
	const { createElement: el } = wp.element;
	const { __ } = wp.i18n;

	const data = window.chwSocialLinksData || {};
	const links = Array.isArray( data.links ) ? data.links : [];

	registerBlockType( 'chw/social-links', {
		title: __( 'Social Links', 'chw' ),
		description: __( 'Displays the social profile links managed in the Customizer.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'share',
		supports: {
			anchor: true,
			html: false,
		},

		edit: function () {
			const blockProps = useBlockProps( { className: 'social-links' } );

			const icons = links.length
				? links.map( function ( link, index ) {
						return el(
							'a',
							{ key: 'social-' + index, href: link.url || '#', onClick: function ( e ) { e.preventDefault(); } },
							el( 'span', { className: 'sr-only' }, link.label || '' ),
							el( 'i', {
								className: 'fa-brands fa-' + ( link.platform || '' ),
								'aria-hidden': 'true',
							} )
						);
				  } )
				: [
						el(
							'p',
							{ key: 'empty', style: { margin: 0, opacity: 0.7 } },
							__( 'No social links yet — add them in Customizer → Social Links.', 'chw' )
						),
				  ];

			return el( 'div', blockProps, icons );
		},

		save: function () {
			return null;
		},
	} );
} )( window.wp );
