( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls, InnerBlocks, MediaUpload, MediaUploadCheck, useBlockProps, PanelColorSettings } = wp.blockEditor;
	const { PanelBody, SelectControl, Button } = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { __ } = wp.i18n;

	const BACKGROUND_TYPE_OPTIONS = [
		{ label: __( 'Color', 'chw' ), value: 'color' },
		{ label: __( 'Image', 'chw' ), value: 'image' },
	];

	registerBlockType( 'chw/fullwidth-cover-section', {
		title: __( 'Fullwidth Cover Section', 'chw' ),
		description: __( 'A full-width section with an editable color or image background, containing standard inner blocks.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'cover-image',
		supports: {
			anchor: true,
			html: false,
			align: [ 'full' ],
		},
		attributes: {
			align: { type: 'string', default: 'full' },
			backgroundType: { type: 'string', default: 'color' },
			backgroundColor: { type: 'string', default: '' },
			imageId: { type: 'number', default: 0 },
			imageUrl: { type: 'string', default: '' },
			imageAlt: { type: 'string', default: '' },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const isImage = attributes.backgroundType === 'image';

			const sectionStyle = {};
			if ( isImage && attributes.imageUrl ) {
				sectionStyle.backgroundImage = 'url(' + attributes.imageUrl + ')';
				sectionStyle.backgroundSize = 'cover';
				sectionStyle.backgroundPosition = 'center';
			} else if ( ! isImage && attributes.backgroundColor ) {
				sectionStyle.backgroundColor = attributes.backgroundColor;
			}

			const blockProps = useBlockProps( {
				className: 'fullwidth-cover-section-editor',
				style: sectionStyle,
			} );

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Background', 'chw' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Background type', 'chw' ),
							value: attributes.backgroundType,
							options: BACKGROUND_TYPE_OPTIONS,
							onChange: function ( value ) {
								setAttributes( { backgroundType: value } );
							},
						} ),
						isImage
							? el(
									MediaUploadCheck,
									null,
									el( MediaUpload, {
										onSelect: function ( media ) {
											setAttributes( {
												imageId: media.id,
												imageUrl: media.url,
												imageAlt: media.alt || '',
											} );
										},
										allowedTypes: [ 'image' ],
										value: attributes.imageId,
										render: function ( obj ) {
											return el(
												Button,
												{ variant: 'secondary', onClick: obj.open },
												attributes.imageUrl
													? __( 'Replace background image', 'chw' )
													: __( 'Select background image', 'chw' )
											);
										},
									} )
							  )
							: null
					),
					! isImage
						? el( PanelColorSettings, {
								title: __( 'Background color', 'chw' ),
								initialOpen: true,
								colorSettings: [
									{
										value: attributes.backgroundColor,
										onChange: function ( value ) {
											setAttributes( { backgroundColor: value || '' } );
										},
										label: __( 'Background color', 'chw' ),
									},
								],
						  } )
						: null
				),

				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: '_inner _gutter' },
						el( InnerBlocks, { templateLock: false } )
					)
				)
			);
		},

		save: function () {
			return el( InnerBlocks.Content );
		},
	} );
} )( window.wp );
