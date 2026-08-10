( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const {
		RichText,
		MediaUpload,
		MediaUploadCheck,
		InspectorControls,
		useBlockProps,
	} = wp.blockEditor;
	const { PanelBody, ToggleControl, Button } = wp.components;
	const { createElement: el, Fragment } = wp.element;
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

	/**
	 * Generate a reasonably unique id for repeater rows (used as React keys).
	 */
	function uid( prefix ) {
		return prefix + '-' + Math.random().toString( 36 ).slice( 2, 9 );
	}

	/**
	 * Immutable repeater helpers.
	 */
	function updateRow( rows, index, patch ) {
		return rows.map( function ( row, i ) {
			return i === index ? Object.assign( {}, row, patch ) : row;
		} );
	}

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

	/**
	 * Row action buttons (move up / move down / remove).
	 */
	function rowActions( rows, index, onChange ) {
		return el(
			'div',
			{ className: 'awards-recognition__card-actions', contentEditable: false },
			el( Button, {
				icon: 'arrow-up-alt2',
				label: __( 'Move up', 'chw' ),
				disabled: index === 0,
				onClick: function () {
					onChange( moveRow( rows, index, -1 ) );
				},
			} ),
			el( Button, {
				icon: 'arrow-down-alt2',
				label: __( 'Move down', 'chw' ),
				disabled: index === rows.length - 1,
				onClick: function () {
					onChange( moveRow( rows, index, 1 ) );
				},
			} ),
			el( Button, {
				icon: 'trash',
				isDestructive: true,
				label: __( 'Remove', 'chw' ),
				onClick: function () {
					onChange( removeRow( rows, index ) );
				},
			} )
		);
	}

	/**
	 * Image picker that shows the selected image or an upload button.
	 */
	function imagePicker( mediaClass, item, onSelect, onRemove ) {
		return el(
			'div',
			{ className: mediaClass, contentEditable: false },
			el( MediaUploadCheck, null,
				el( MediaUpload, {
					onSelect: onSelect,
					allowedTypes: [ 'image' ],
					value: item.imageId,
					render: function ( obj ) {
						if ( item.imageUrl ) {
							return el(
								'span',
								{ className: 'awards-recognition__media-edit' },
								el( 'img', {
									src: item.imageUrl,
									alt: item.imageAlt || '',
									onClick: obj.open,
									role: 'button',
								} ),
								el( Button, {
									icon: 'no-alt',
									label: __( 'Remove image', 'chw' ),
									className: 'awards-recognition__media-remove',
									onClick: onRemove,
								} )
							);
						}
						return el(
							Button,
							{ variant: 'secondary', className: 'chw-editor-add-button', onClick: obj.open },
							__( 'Add image', 'chw' )
						);
					},
				} )
			)
		);
	}

	registerBlockType( 'chw/awards-recognition', {
		title: __( 'Awards & Recognition', 'chw' ),
		description: __( 'Full-width awards and recognition section.', 'chw' ),
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
			featured: { type: 'array', default: [] },
			supporting: { type: 'array', default: [] },
			showAdditional: { type: 'boolean', default: false },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const featured = Array.isArray( attributes.featured ) ? attributes.featured : [];
			const supporting = Array.isArray( attributes.supporting ) ? attributes.supporting : [];

			const blockProps = useBlockProps( { className: 'awards-recognition _bg -quaternary' } );

			function setFeatured( rows ) {
				setAttributes( { featured: rows } );
			}

			function setSupporting( rows ) {
				setAttributes( { supporting: rows } );
			}

			// --- Featured cards --------------------------------------------------
			const featuredCards = featured.map( function ( award, index ) {
				return el(
					'article',
					{ key: award.id || 'feat-' + index, className: 'awards-recognition__card _flex -column -align-center _text -align-center' },
					imagePicker(
						'awards-recognition__card-media _flex -align-center -justify-center',
						award,
						function ( media ) {
							setFeatured( updateRow( featured, index, {
								imageId: media.id,
								imageUrl: media.url,
								imageAlt: media.alt || '',
							} ) );
						},
						function () {
							setFeatured( updateRow( featured, index, {
								imageId: undefined,
								imageUrl: '',
								imageAlt: '',
							} ) );
						}
					),
					el( 'div', { className: 'awards-recognition__card-rule' } ),
					el( RichText, {
						tagName: 'p',
						className: 'awards-recognition__card-title _text -tertiary _text-size -base',
						value: award.title || '',
						allowedFormats: [ 'core/bold', 'core/italic' ],
						placeholder: __( 'Award title', 'chw' ),
						onChange: function ( value ) {
							setFeatured( updateRow( featured, index, { title: value } ) );
						},
					} ),
					el( RichText, {
						tagName: 'p',
						className: 'awards-recognition__card-source _text -secondary _text-size -sm',
						value: award.source || '',
						allowedFormats: [ 'core/bold', 'core/italic' ],
						placeholder: __( 'Source', 'chw' ),
						onChange: function ( value ) {
							setFeatured( updateRow( featured, index, { source: value } ) );
						},
					} ),
					el( RichText, {
						tagName: 'p',
						className: 'awards-recognition__card-year _text -muted -transform-uppercase _text-size -sm',
						value: award.year || '',
						allowedFormats: [],
						placeholder: __( 'Year', 'chw' ),
						onChange: function ( value ) {
							setFeatured( updateRow( featured, index, { year: value } ) );
						},
					} ),
					rowActions( featured, index, setFeatured )
				);
			} );

			// --- Supporting items ------------------------------------------------
			const supportingItems = supporting.map( function ( item, index ) {
				return el(
					'figure',
					{ key: item.id || 'sup-' + index, className: 'awards-recognition__item _flex -align-center' },
					el(
						'div',
						{ className: 'awards-recognition__item-media-wrap', contentEditable: false },
						imagePicker(
							'awards-recognition__item-media _flex -align-center -justify-center',
							item,
							function ( media ) {
								setSupporting( updateRow( supporting, index, {
									imageId: media.id,
									imageUrl: media.url,
									imageAlt: media.alt || '',
								} ) );
							},
							function () {
								setSupporting( updateRow( supporting, index, {
									imageId: undefined,
									imageUrl: '',
									imageAlt: '',
								} ) );
							}
						)
					),
					el(
						'figcaption',
						{ className: 'awards-recognition__item-caption' },
						el( RichText, {
							tagName: 'span',
							className: 'awards-recognition__item-title _text -tertiary _text-size -sm',
							value: item.title || '',
							allowedFormats: [ 'core/bold', 'core/italic' ],
							placeholder: __( 'Name', 'chw' ),
							onChange: function ( value ) {
								setSupporting( updateRow( supporting, index, { title: value } ) );
							},
						} ),
						el( RichText, {
							tagName: 'span',
							className: 'awards-recognition__item-text _text -muted _text-size -sm',
							value: item.caption || '',
							allowedFormats: [ 'core/bold', 'core/italic' ],
							placeholder: __( 'Short description', 'chw' ),
							onChange: function ( value ) {
								setSupporting( updateRow( supporting, index, { caption: value } ) );
							},
						} ),
						el( RichText, {
							tagName: 'span',
							className: 'awards-recognition__item-wordmark-input _text -muted _text-size -sm',
							value: item.wordmark || '',
							allowedFormats: [],
							placeholder: __( 'Wordmark (used if no image)', 'chw' ),
							onChange: function ( value ) {
								setSupporting( updateRow( supporting, index, { wordmark: value } ) );
							},
						} )
					),
					rowActions( supporting, index, setSupporting )
				);
			} );

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Section Options', 'chw' ), initialOpen: true },
						el( ToggleControl, {
							label: __( 'Show Additional Recognition section', 'chw' ),
							help: __( 'Adds a secondary row of supporting recognition below the featured awards.', 'chw' ),
							checked: !! attributes.showAdditional,
							onChange: function ( value ) {
								setAttributes( { showAdditional: value } );
							},
						} )
					)
				),

				el(
					'section',
					blockProps,
					el( 'div', { className: 'awards-recognition__topline', 'aria-hidden': 'true' } ),
					el(
						'div',
						{ className: 'awards-recognition__inner _container' },

						// Header.
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

						// Featured awards.
						el(
							'div',
							{ className: 'awards-recognition__featured' },
							featuredCards
						),
						el(
							'div',
							{ className: 'awards-recognition__add', contentEditable: false },
							el(
								Button,
								{
									variant: 'secondary',
									onClick: function () {
										setFeatured( featured.concat( [ {
											id: uid( 'feat' ),
											imageId: undefined,
											imageUrl: '',
											imageAlt: '',
											title: '',
											source: '',
											year: '',
										} ] ) );
									},
								},
								__( 'Add featured award', 'chw' )
							)
						),

						// Additional recognition (only when enabled).
						attributes.showAdditional
							? el(
									Fragment,
									null,
									el(
										'div',
										{ className: 'awards-recognition__divider _flex -align-center' },
										el( 'span', { className: '_text -muted -transform-uppercase _text-size -sm' }, __( 'Additional recognition', 'chw' ) )
									),
									el(
										'div',
										{
											className: 'awards-recognition__supporting-wrap',
											'data-count': supporting.length || 1,
										},
										el(
											'div',
											{
												className: 'awards-recognition__supporting',
												'data-count': supporting.length || 1,
											},
											supportingItems
										)
									),
									el(
										'div',
										{ className: 'awards-recognition__add', contentEditable: false },
										el(
											Button,
											{
												variant: 'secondary',
												onClick: function () {
													setSupporting( supporting.concat( [ {
														id: uid( 'sup' ),
														imageId: undefined,
														imageUrl: '',
														imageAlt: '',
														wordmark: '',
														title: '',
														caption: '',
													} ] ) );
												},
											},
											__( 'Add supporting item', 'chw' )
										)
									)
							  )
							: null,

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
				)
			);
		},

		save: function () {
			return null;
		},
	} );
} )( window.wp );
