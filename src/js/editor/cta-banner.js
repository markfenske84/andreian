( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls, useBlockProps } = wp.blockEditor;
	const {
		PanelBody,
		ToggleControl,
		TextControl,
		SelectControl,
		BaseControl,
		Button,
	} = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { __ } = wp.i18n;

	const blockData = window.chwCtaBannerData || {};

	const MAX_BUTTONS = 3;

	const BUTTON_MODIFIER_OPTIONS = [
		{ label: __( 'None', 'chw' ), value: '' },
		{ label: __( 'Arrow', 'chw' ), value: 'arrow' },
		{ label: __( 'Phone', 'chw' ), value: 'phone' },
		{ label: __( 'Download', 'chw' ), value: 'download' },
	];

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

	registerBlockType( 'chw/cta-banner', {
		title: __( 'Call To Action: Banner', 'chw' ),
		description: __( 'A navy call-to-action banner with heading, paragraphs, and a CTA button repeater. Optional global legal disclaimer.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'megaphone',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			heading: { type: 'string', default: '' },
			description: { type: 'string', default: '' },
			subText: { type: 'string', default: '' },
			buttons: { type: 'array', default: [] },
			showLegal: { type: 'boolean', default: true },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const buttons = Array.isArray( attributes.buttons ) ? attributes.buttons : [];

			const blockProps = useBlockProps( { className: 'cta-banner' } );

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
						{ title: __( 'CTA Buttons', 'chw' ), initialOpen: true },
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
						{ title: __( 'Legal Text', 'chw' ), initialOpen: false },
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
							{ className: 'cta-banner__top' },
								el(
									'div',
									{ className: 'cta-banner__main' },
									el( RichText, {
										tagName: 'h2',
										className: 'cta-banner__heading _text-style -h2',
										value: attributes.heading || '',
										allowedFormats: [ 'core/bold', 'core/text-color' ],
										placeholder: __( 'Ready to protect your home?', 'chw' ),
										onChange: function ( value ) {
											setAttributes( { heading: value } );
										},
									} ),
									el( RichText, {
										tagName: 'p',
										className: 'cta-banner__intro',
										value: attributes.description || '',
										allowedFormats: [ 'core/bold', 'core/italic' ],
										placeholder: __( 'Add a supporting sentence.', 'chw' ),
										onChange: function ( value ) {
											setAttributes( { description: value } );
										},
									} ),
									el( RichText, {
										tagName: 'p',
										className: 'cta-banner__subtext _text -sm',
										value: attributes.subText || '',
										allowedFormats: [ 'core/bold', 'core/italic' ],
										placeholder: __( 'Optional fine print below the heading.', 'chw' ),
										onChange: function ( value ) {
											setAttributes( { subText: value } );
										},
									} )
								),
								buttonPreview.length
									? el(
											'div',
											{ className: 'cta-banner__buttons', contentEditable: false },
											buttonPreview
									  )
									: null
							),
							blockData.legalText && attributes.showLegal !== false
								? el( 'div', {
										className: 'cta-banner__legal _text -xs',
										contentEditable: false,
										dangerouslySetInnerHTML: { __html: blockData.legalText },
								  } )
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
