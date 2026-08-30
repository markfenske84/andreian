( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { useBlockProps } = wp.blockEditor;
	const { createElement: el } = wp.element;
	const { __ } = wp.i18n;
	const ServerSideRender = wp.serverSideRender;

	registerBlockType( 'andreian/social-links', {
		title: __( 'Social Links', 'andreian' ),
		description: __(
			'Displays the social profile links managed in the Customizer.',
			'andreian'
		),
		icon: 'share',
		category: 'widgets',
		edit: function () {
			const blockProps = useBlockProps();

			return el(
				'div',
				blockProps,
				ServerSideRender
					? el( ServerSideRender, {
							block: 'andreian/social-links',
							EmptyResponsePlaceholder: function () {
								return el(
									'p',
									null,
									__(
										'Add social links in Customizer → Social Links.',
										'andreian'
									)
								);
							},
					  } )
					: null
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
