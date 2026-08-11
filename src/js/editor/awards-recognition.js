( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const {
		RichText,
		MediaUpload,
		MediaUploadCheck,
		useBlockProps,
	} = wp.blockEditor;
	const { Button } = wp.components;
	const { createElement: el } = wp.element;
	const { __ } = wp.i18n;

	const blockData = window.awardsRecognitionData || {};
	const legalText = blockData.legalText || '';

	const EYEBROW_ICON = el(
		'span',
		{ className: '_eyebrow__icon _text -primary' },
		el(
			'svg',
			{ viewBox: '0 0 24 24', 'aria-hidden': 'true', focusable: 'false' },
			el( 'path', {
				d: 'M12 2a5 5 0 0 1 5 5 5 5 0 0 1-3 4.58V13l1.7 6.3a.5.5 0 0 1-.66.6L12 18.5l-3.04 1.4a.5.5 0 0 1-.66-.6L10 13v-1.42A5 5 0 0 1 7 7a5 5 0 0 1 5-5Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z',
			} )
		)
	);

	function removeRow( rows, index ) {
		return rows.filter( function ( row, i ) {
			return i !== index;
		} );
	}

	function moveRow( rows, index, dir ) {
		const target = index + dir;
		if ( target < 0 || target >= rows.length ) {
			return rows;
		}
		const next = rows.slice();
		const tmp = next[ index ];
		next[ index ] = next[ target ];
		next[ target ] = tmp;
		return next;
	}

	function logoActions( logos, index, onChange ) {
		return el(
			'div',
			{ className: 'awards-recognition__logo-actions', contentEditable: false },
			el( Button, {
				icon: 'arrow-left-alt2',
				label: __( 'Move left', 'chw' ),
				disabled: index === 0,
				onClick: function () {
					onChange( moveRow( logos, index, -1 ) );
				},
			} ),
			el( Button, {
				icon: 'arrow-right-alt2',
				label: __( 'Move right', 'chw' ),
				disabled: index === logos.length - 1,
				onClick: function () {
					onChange( moveRow( logos, index, 1 ) );
				},
			} ),
			el( Button, {
				icon: 'trash',
				isDestructive: true,
				label: __( 'Remove', 'chw' ),
				onClick: function () {
					onChange( removeRow( logos, index ) );
				},
			} )
		);
	}

	function mapMediaToLogos( media ) {
		return media.map( function ( item ) {
			return {
				id: item.id,
				url: item.url,
				alt: item.alt || '',
			};
		} );
	}

	registerBlockType( 'chw/awards-recognition', {
		title: __( 'Awards & Recognition', 'chw' ),
		description: __( 'Full-width awards and recognition section with a logo gallery.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'awards',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			eyebrow: { type: 'string', default: '' },
			heading: { type: 'string', default: '' },
			intro: { type: 'string', default: '' },
			logos: { type: 'array', default: [] },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const logos = Array.isArray( attributes.logos ) ? attributes.logos : [];

			const blockProps = useBlockProps( { className: 'awards-recognition _bg -quaternary' } );

			function setLogos( rows ) {
				setAttributes( { logos: rows } );
			}

			const logoItems = logos.map( function ( logo, index ) {
				return el(
					'li',
					{
						key: logo.id || 'logo-' + index,
						className: 'awards-recognition__logo',
					},
					logo.url
						? el( 'img', {
								className: 'awards-recognition__logo-img',
								src: logo.url,
								alt: logo.alt || '',
						  } )
						: null,
					logoActions( logos, index, setLogos )
				);
			} );

			return el(
				'section',
				blockProps,
				el( 'div', { className: 'awards-recognition__topline', 'aria-hidden': 'true' } ),
				el(
					'div',
					{ className: 'awards-recognition__inner _container' },

					el(
						'div',
						{ className: 'awards-recognition__header _text -align-center' },
						el(
							'span',
							{ className: '_eyebrow -on-light _text -tertiary' },
							EYEBROW_ICON,
							el( RichText, {
								tagName: 'span',
								className: '_eyebrow__label',
								value: attributes.eyebrow || '',
								allowedFormats: [],
								placeholder: __( 'Awards & Recognition', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { eyebrow: value } );
								},
							} )
						),
						el( RichText, {
							tagName: 'h2',
							className: 'awards-recognition__heading _text -secondary _text-style -h4',
							value: attributes.heading || '',
							allowedFormats: [ 'core/bold', 'core/italic', 'core/text-color' ],
							placeholder: __( 'Recognized as a top home warranty provider', 'chw' ),
							onChange: function ( value ) {
								setAttributes( { heading: value } );
							},
						} ),
						el( RichText, {
							tagName: 'p',
							className: 'awards-recognition__intro _text -muted _text-size -lg',
							value: attributes.intro || '',
							allowedFormats: [ 'core/bold', 'core/italic' ],
							placeholder: __( 'Add an introductory sentence describing your recognition.', 'chw' ),
							onChange: function ( value ) {
								setAttributes( { intro: value } );
							},
						} )
					),

					el(
						'div',
						{ className: 'awards-recognition__gallery' },
						el(
							'ul',
							{ className: 'awards-recognition__logos' },
							logoItems
						),
						el(
							'div',
							{ className: 'awards-recognition__add', contentEditable: false },
							el( MediaUploadCheck, null,
								el( MediaUpload, {
									multiple: true,
									gallery: true,
									addToGallery: true,
									allowedTypes: [ 'image' ],
									value: logos.map( function ( logo ) {
										return logo.id;
									} ),
									onSelect: function ( media ) {
										setLogos( mapMediaToLogos( media ) );
									},
									render: function ( obj ) {
										return el(
											Button,
											{
												variant: 'secondary',
												className: 'chw-editor-add-button',
												onClick: obj.open,
											},
											logos.length
												? __( 'Edit logos', 'chw' )
												: __( 'Add logos', 'chw' )
										);
									},
								} )
							)
						)
					),

					legalText
						? el(
								'div',
								{
									className: 'chw-legal -text-dark',
									contentEditable: false,
								},
								el( 'div', {
									className: 'chw-legal__content',
									dangerouslySetInnerHTML: { __html: legalText },
								} )
						  )
						: null
				)
			);
		},

		save: function () {
			return null;
		},
	} );
} )( window.wp );
