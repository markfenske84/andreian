( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls, MediaUpload, MediaUploadCheck, useBlockProps } = wp.blockEditor;
	const { PanelBody, SelectControl, TextControl, Button } = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { __ } = wp.i18n;

	const SOURCE_OPTIONS = [
		{ label: __( 'Third-party (YouTube / Vimeo embed URL)', 'chw' ), value: 'third-party' },
		{ label: __( 'Self-hosted file', 'chw' ), value: 'self-hosted' },
	];

	registerBlockType( 'chw/video-modal', {
		title: __( 'Video Modal', 'chw' ),
		description: __( 'A clickable thumbnail that opens a video in a modal.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'video-alt3',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			videoSource: { type: 'string', default: 'third-party' },
			videoUrl: { type: 'string', default: '' },
			videoFileId: { type: 'number', default: 0 },
			videoFileUrl: { type: 'string', default: '' },
			thumbnailId: { type: 'number', default: 0 },
			thumbnailUrl: { type: 'string', default: '' },
			thumbnailAlt: { type: 'string', default: '' },
			modalBackground: { type: 'string', default: 'rgba(0,0,0,0.8)' },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const isSelfHosted = attributes.videoSource === 'self-hosted';

			const blockProps = useBlockProps( { className: 'video-modal-trigger' } );

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Video Source', 'chw' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Source type', 'chw' ),
							value: attributes.videoSource,
							options: SOURCE_OPTIONS,
							onChange: function ( value ) {
								setAttributes( { videoSource: value } );
							},
						} ),
						isSelfHosted
							? el(
									MediaUploadCheck,
									null,
									el( MediaUpload, {
										onSelect: function ( media ) {
											setAttributes( {
												videoFileId: media.id,
												videoFileUrl: media.url,
											} );
										},
										allowedTypes: [ 'video' ],
										value: attributes.videoFileId,
										render: function ( obj ) {
											return el(
												Button,
												{ variant: 'secondary', onClick: obj.open },
												attributes.videoFileUrl
													? __( 'Replace video file', 'chw' )
													: __( 'Select video file', 'chw' )
											);
										},
									} )
							  )
							: el( TextControl, {
									label: __( 'Embed URL', 'chw' ),
									help: __( 'Paste the YouTube/Vimeo embed URL.', 'chw' ),
									value: attributes.videoUrl,
									onChange: function ( value ) {
										setAttributes( { videoUrl: value } );
									},
							  } )
					),
					el(
						PanelBody,
						{ title: __( 'Appearance', 'chw' ), initialOpen: false },
						el( TextControl, {
							label: __( 'Modal background', 'chw' ),
							help: __( 'Any CSS color, e.g. rgba(0,0,0,0.8).', 'chw' ),
							value: attributes.modalBackground,
							onChange: function ( value ) {
								setAttributes( { modalBackground: value } );
							},
						} )
					)
				),

				el(
					'button',
					Object.assign( {}, blockProps, { type: 'button' } ),
					el( 'i', { className: 'fa-solid fa-circle-play', 'aria-hidden': 'true' } ),
					el(
						'div',
						{ className: 'thumbnail', contentEditable: false },
						el(
							MediaUploadCheck,
							null,
							el( MediaUpload, {
								onSelect: function ( media ) {
									setAttributes( {
										thumbnailId: media.id,
										thumbnailUrl: media.url,
										thumbnailAlt: media.alt || '',
									} );
								},
								allowedTypes: [ 'image' ],
								value: attributes.thumbnailId,
								render: function ( obj ) {
									if ( attributes.thumbnailUrl ) {
										return el( 'img', {
											src: attributes.thumbnailUrl,
											alt: attributes.thumbnailAlt || '',
											onClick: obj.open,
											role: 'button',
										} );
									}
									return el(
										Button,
										{ variant: 'secondary', onClick: obj.open },
										__( 'Select thumbnail', 'chw' )
									);
								},
							} )
						)
					)
				)
			);
		},

		save: function () {
			return null;
		},
	} );
} )( window.wp );
