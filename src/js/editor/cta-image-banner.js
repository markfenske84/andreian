( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls, MediaUpload, MediaUploadCheck, useBlockProps } = wp.blockEditor;
	const {
		PanelBody,
		ToggleControl,
		TextControl,
		TextareaControl,
		SelectControl,
		BaseControl,
		Button,
	} = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { __ } = wp.i18n;

	const blockData = window.chwCtaImageBannerData || {};

	const MAX_BUTTONS = 3;

	const BUTTON_MODIFIER_OPTIONS = [
		{ label: __( 'None', 'chw' ), value: '' },
		{ label: __( 'Arrow', 'chw' ), value: 'arrow' },
		{ label: __( 'Phone', 'chw' ), value: 'phone' },
		{ label: __( 'Download', 'chw' ), value: 'download' },
	];

	const DEFAULT_ICON = blockData.defaultIcon || '';

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

	function buttonClassForIndex( index, modifier ) {
		let cls = '_button ' + ( index === 0 ? '-primary' : '-outline' );
		if ( modifier ) {
			cls += ' -' + modifier;
		}
		return cls;
	}

	function resolveImageUrl( imageUrl ) {
		return imageUrl || blockData.defaultImageUrl || '';
	}

	function resolveIcon( icon ) {
		return icon || DEFAULT_ICON;
	}

	registerBlockType( 'chw/cta-image-banner', {
		title: __( 'Call To Action: Image Banner', 'chw' ),
		description: __( 'An image-background call-to-action card with icon, badge, heading, support text, and CTA buttons. Optional global legal disclaimer.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'format-image',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			icon: { type: 'string', default: '' },
			badgeText: { type: 'string', default: '' },
			heading: { type: 'string', default: '' },
			description: { type: 'string', default: '' },
			buttons: { type: 'array', default: [] },
			showLegal: { type: 'boolean', default: true },
			imageId: { type: 'number', default: 0 },
			imageUrl: { type: 'string', default: '' },
			imageAlt: { type: 'string', default: '' },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const buttons = Array.isArray( attributes.buttons ) ? attributes.buttons : [];

			const blockProps = useBlockProps( { className: 'cta-image-banner' } );

			const boxStyle = {};
			const bgUrl = resolveImageUrl( attributes.imageUrl );
			if ( bgUrl ) {
				boxStyle.backgroundImage = 'url(' + bgUrl + ')';
				boxStyle.backgroundSize = 'cover';
				boxStyle.backgroundPosition = 'center';
			}

			const iconMarkup = resolveIcon( attributes.icon );

			function setButtons( rows ) {
				setAttributes( { buttons: rows } );
			}

			const buttonRows = buttons.map( function ( button, index ) {
				return el(
					'div',
					{ key: 'btn-' + index, className: 'chw-masthead-repeater-row' },
					el(
						'div',
						{ className: 'chw-masthead-repeater-row__head' },
						el(
							'span',
							null,
							( index === 0 ? __( 'Primary Button', 'chw' ) : __( 'Button', 'chw' ) ) + ' ' + ( index + 1 )
						),
						el(
							'div',
							{ className: 'chw-masthead-repeater-row__actions' },
							el( Button, {
								icon: 'arrow-up-alt2',
								label: __( 'Move up', 'chw' ),
								disabled: index === 0,
								onClick: function () {
									setButtons( moveRow( buttons, index, -1 ) );
								},
							} ),
							el( Button, {
								icon: 'arrow-down-alt2',
								label: __( 'Move down', 'chw' ),
								disabled: index === buttons.length - 1,
								onClick: function () {
									setButtons( moveRow( buttons, index, 1 ) );
								},
							} ),
							el( Button, {
								icon: 'trash',
								isDestructive: true,
								label: __( 'Remove', 'chw' ),
								onClick: function () {
									setButtons( removeRow( buttons, index ) );
								},
							} )
						)
					),
					el( TextControl, {
						label: __( 'Text', 'chw' ),
						value: button.text || '',
						placeholder: index === 0 ? __( 'MAKE A CLAIM', 'chw' ) : __( 'Button text', 'chw' ),
						onChange: function ( value ) {
							setButtons( updateRow( buttons, index, { text: value } ) );
						},
					} ),
					el( TextControl, {
						label: __( 'URL', 'chw' ),
						value: button.url || '',
						onChange: function ( value ) {
							setButtons( updateRow( buttons, index, { url: value } ) );
						},
					} ),
					el( SelectControl, {
						label: __( 'Icon', 'chw' ),
						value: button.modifier || '',
						options: BUTTON_MODIFIER_OPTIONS,
						onChange: function ( value ) {
							setButtons( updateRow( buttons, index, { modifier: value } ) );
						},
					} ),
					el( ToggleControl, {
						label: __( 'Open in new tab', 'chw' ),
						checked: !! button.new_tab,
						onChange: function ( value ) {
							setButtons( updateRow( buttons, index, { new_tab: value } ) );
						},
					} )
				);
			} );

			const buttonPreview = buttons
				.filter( function ( button ) {
					return !! button.text;
				} )
				.map( function ( button, index ) {
					return el(
						'span',
						{
							key: 'btn-preview-' + index,
							className: buttonClassForIndex( index, button.modifier || '' ),
							contentEditable: false,
						},
						button.text
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
						{ title: __( 'Background', 'chw' ), initialOpen: true },
						el(
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
										Fragment,
										null,
										el(
											Button,
											{ variant: 'secondary', onClick: obj.open },
											bgUrl
												? __( 'Replace background image', 'chw' )
												: __( 'Select background image', 'chw' )
										),
										attributes.imageUrl
											? el(
													Button,
													{
														variant: 'link',
														isDestructive: true,
														onClick: function () {
															setAttributes( {
																imageId: 0,
																imageUrl: '',
																imageAlt: '',
															} );
														},
													},
													__( 'Remove image (use default)', 'chw' )
											  )
											: null
									);
								},
							} )
						),
						attributes.imageUrl
							? el( TextControl, {
									label: __( 'Image alt text', 'chw' ),
									value: attributes.imageAlt || '',
									onChange: function ( value ) {
										setAttributes( { imageAlt: value } );
									},
							  } )
							: null
					),
					el(
						PanelBody,
						{ title: __( 'Icon', 'chw' ), initialOpen: false },
						el( TextareaControl, {
							label: __( 'SVG markup', 'chw' ),
							help: __( 'Paste inline SVG markup for the circular icon.', 'chw' ),
							value: attributes.icon || '',
							onChange: function ( value ) {
								setAttributes( { icon: value } );
							},
						} ),
						attributes.icon
							? el(
									Button,
									{
										variant: 'link',
										isDestructive: true,
										onClick: function () {
											setAttributes( { icon: '' } );
										},
									},
									__( 'Reset to default icon', 'chw' )
							  )
							: null
					),
					el(
						PanelBody,
						{ title: __( 'CTA Buttons', 'chw' ), initialOpen: false },
						el(
							BaseControl,
							{ help: __( 'The first button is styled as primary; additional buttons use the outline style.', 'chw' ) },
							buttonRows
						),
						buttons.length < MAX_BUTTONS
							? el(
									Button,
									{
										variant: 'secondary',
										className: 'chw-editor-add-button',
										onClick: function () {
											setButtons( buttons.concat( [ {
												text: '',
												url: '',
												modifier: '',
												new_tab: false,
											} ] ) );
										},
									},
									__( 'Add Button', 'chw' )
							  )
							: null
					),
					el(
						PanelBody,
						{ title: __( 'Settings', 'chw' ), initialOpen: false },
						el( ToggleControl, {
							label: __( 'Show legal text', 'chw' ),
							help: __( 'Pulls the global legal text set in the Customizer.', 'chw' ),
							checked: attributes.showLegal !== false,
							onChange: function ( value ) {
								setAttributes( { showLegal: value } );
							},
						} )
					)
				),

				el(
					'section',
					blockProps,
					el(
						'div',
						{ className: '_container' },
						el(
							'div',
							{
								className: 'cta-image-banner__box',
								style: boxStyle,
							},
							el( 'span', { className: 'cta-image-banner__overlay', 'aria-hidden': true } ),
							el( 'span', { className: 'cta-image-banner__accent', 'aria-hidden': true } ),
							el(
								'div',
								{ className: 'cta-image-banner__top' },
								el(
									'div',
									{ className: 'cta-image-banner__main' },
									iconMarkup
										? el( 'div', {
												className: 'cta-image-banner__icon',
												contentEditable: false,
												'aria-hidden': true,
												dangerouslySetInnerHTML: { __html: iconMarkup },
										  } )
										: null,
									el(
										'div',
										{ className: 'cta-image-banner__content' },
										el(
											'span',
											{ className: '_eyebrow -on-dark cta-image-banner__badge' },
											el( RichText, {
												tagName: 'span',
												className: '_eyebrow__label',
												value: attributes.badgeText || '',
												allowedFormats: [],
												placeholder: __( 'EXISTING CUSTOMERS', 'chw' ),
												onChange: function ( value ) {
													setAttributes( { badgeText: value } );
												},
											} )
										),
										el( RichText, {
											tagName: 'h2',
											className: 'cta-image-banner__heading _text-style -h2',
											value: attributes.heading || '',
											allowedFormats: [ 'core/bold', 'core/text-color' ],
											placeholder: __( 'Need to file a claim?', 'chw' ),
											onChange: function ( value ) {
												setAttributes( { heading: value } );
											},
										} ),
										el( RichText, {
											tagName: 'p',
											className: 'cta-image-banner__intro',
											value: attributes.description || '',
											allowedFormats: [ 'core/bold', 'core/italic' ],
											placeholder: __( 'Already have coverage? Start a claim through Choice Home Warranty\'s claims center — service requests are available 24/7.', 'chw' ),
											onChange: function ( value ) {
												setAttributes( { description: value } );
											},
										} )
									)
								),
								buttonPreview.length
									? el(
											'div',
											{ className: 'cta-image-banner__buttons', contentEditable: false },
											buttonPreview
									  )
									: null
							),
							blockData.legalText && attributes.showLegal !== false
								? el( 'div', {
										className: 'cta-image-banner__legal _text -xs',
										contentEditable: false,
										dangerouslySetInnerHTML: { __html: blockData.legalText },
								  } )
								: null
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
