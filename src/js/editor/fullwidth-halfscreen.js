( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls, InnerBlocks, MediaUpload, MediaUploadCheck, useBlockProps } = wp.blockEditor;
	const { PanelBody, SelectControl, TextControl, Button } = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { __ } = wp.i18n;

	const ORIENTATION_OPTIONS = [
		{ label: __( 'Media | Content', 'chw' ), value: '-media-content' },
		{ label: __( 'Content | Media', 'chw' ), value: '-content-media' },
	];

	const MEDIA_TYPE_OPTIONS = [
		{ label: __( 'Image', 'chw' ), value: 'image' },
		{ label: __( 'Video', 'chw' ), value: 'video' },
	];

	const VIDEO_SOURCE_OPTIONS = [
		{ label: __( 'Self-hosted', 'chw' ), value: 'self' },
		{ label: __( 'YouTube', 'chw' ), value: 'youtube' },
		{ label: __( 'Vimeo', 'chw' ), value: 'vimeo' },
	];

	registerBlockType( 'chw/fullwidth-halfscreen', {
		title: __( 'Fullwidth Halfscreen', 'chw' ),
		description: __( 'A full-width section split into a media half and a content half.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'align-pull-left',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			orientation: { type: 'string', default: '-media-content' },
			mediaType: { type: 'string', default: 'image' },
			imageId: { type: 'number', default: 0 },
			imageUrl: { type: 'string', default: '' },
			imageAlt: { type: 'string', default: '' },
			videoSource: { type: 'string', default: 'self' },
			videoFileId: { type: 'number', default: 0 },
			videoFileUrl: { type: 'string', default: '' },
			videoUrl: { type: 'string', default: '' },
			mediaOverlay: { type: 'string', default: '' },
			contentBackground: { type: 'string', default: '' },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const isImage = attributes.mediaType === 'image';
			const isSelfVideo = attributes.videoSource === 'self';

			const blockProps = useBlockProps( {
				className: 'fullwidth-halfscreen-editor _flex ' + attributes.orientation,
			} );

			// --- Media preview ---------------------------------------------------
			let mediaPreview;
			if ( isImage ) {
				mediaPreview = el(
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
							if ( attributes.imageUrl ) {
								return el( 'img', {
									src: attributes.imageUrl,
									alt: attributes.imageAlt || '',
									onClick: obj.open,
									role: 'button',
									style: { display: 'block', width: '100%', cursor: 'pointer' },
								} );
							}
							return el(
								Button,
								{ variant: 'secondary', onClick: obj.open },
								__( 'Select image', 'chw' )
							);
						},
					} )
				);
			} else {
				mediaPreview = el(
					'div',
					{ className: 'fullwidth-halfscreen-editor__video-note' },
					__( 'Video media (configured in the sidebar).', 'chw' )
				);
			}

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Layout', 'chw' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Orientation', 'chw' ),
							value: attributes.orientation,
							options: ORIENTATION_OPTIONS,
							onChange: function ( value ) {
								setAttributes( { orientation: value } );
							},
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Media', 'chw' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Media type', 'chw' ),
							value: attributes.mediaType,
							options: MEDIA_TYPE_OPTIONS,
							onChange: function ( value ) {
								setAttributes( { mediaType: value } );
							},
						} ),
						! isImage
							? el(
									Fragment,
									null,
									el( SelectControl, {
										label: __( 'Video source', 'chw' ),
										value: attributes.videoSource,
										options: VIDEO_SOURCE_OPTIONS,
										onChange: function ( value ) {
											setAttributes( { videoSource: value } );
										},
									} ),
									isSelfVideo
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
												label: __( 'Video URL', 'chw' ),
												value: attributes.videoUrl,
												onChange: function ( value ) {
													setAttributes( { videoUrl: value } );
												},
										  } )
							  )
							: null,
						el( TextControl, {
							label: __( 'Media overlay color', 'chw' ),
							help: __( 'Any CSS color, e.g. rgba(0,0,0,0.4). Leave blank for none.', 'chw' ),
							value: attributes.mediaOverlay,
							onChange: function ( value ) {
								setAttributes( { mediaOverlay: value } );
							},
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Content', 'chw' ), initialOpen: false },
						el( TextControl, {
							label: __( 'Content background color', 'chw' ),
							help: __( 'Any CSS color. Leave blank for none.', 'chw' ),
							value: attributes.contentBackground,
							onChange: function ( value ) {
								setAttributes( { contentBackground: value } );
							},
						} )
					)
				),

				el(
					'div',
					blockProps,
					el( 'div', { className: '_media' }, mediaPreview ),
					el(
						'div',
						{
							className: '_content',
							style: attributes.contentBackground
								? { backgroundColor: attributes.contentBackground }
								: undefined,
						},
						el(
							'div',
							{ className: '_inner _gutter' },
							el( InnerBlocks, { templateLock: false } )
						)
					)
				)
			);
		},

		save: function () {
			return el( InnerBlocks.Content );
		},
	} );
} )( window.wp );
