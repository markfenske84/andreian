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

	const blockData = window.chwCtaHighlightsData || {};

	const MAX_BUTTONS = 3;

	const BUTTON_MODIFIER_OPTIONS = [
		{ label: __( 'None', 'chw' ), value: '' },
		{ label: __( 'Arrow', 'chw' ), value: 'arrow' },
		{ label: __( 'Phone', 'chw' ), value: 'phone' },
		{ label: __( 'Download', 'chw' ), value: 'download' },
	];

	function uid( prefix ) {
		return prefix + '-' + Math.random().toString( 36 ).slice( 2, 9 );
	}

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

	function highlightActions( rows, index, onChange ) {
		return el(
			'div',
			{ className: 'cta-highlights__check-actions', contentEditable: false },
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
				label: __( 'Remove highlight', 'chw' ),
				onClick: function () {
					onChange( removeRow( rows, index ) );
				},
			} )
		);
	}

	const CHECK_ICON_PATH = el( 'path', {
		d: 'M5 12.5l4 4 10-10',
		stroke: 'currentColor',
		strokeWidth: 2.5,
		strokeLinecap: 'round',
		strokeLinejoin: 'round',
		fill: 'none',
	} );

	function checkIcon() {
		return el(
			'svg',
			{ viewBox: '0 0 24 24', fill: 'none', 'aria-hidden': true, focusable: false },
			CHECK_ICON_PATH
		);
	}

	registerBlockType( 'chw/cta-highlights', {
		title: __( 'Call To Action: Highlights', 'chw' ),
		description: __( 'A navy call-to-action box with badge, heading, CTA buttons, and a checkmark highlights panel.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'megaphone',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			badgeText: { type: 'string', default: '' },
			heading: { type: 'string', default: '' },
			description: { type: 'string', default: '' },
			buttons: { type: 'array', default: [] },
			subText: { type: 'string', default: '' },
			showLegal: { type: 'boolean', default: false },
			infoEyebrow: { type: 'string', default: '' },
			highlights: { type: 'array', default: [] },
			infoText: { type: 'string', default: '' },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const buttons = Array.isArray( attributes.buttons ) ? attributes.buttons : [];
			const highlights = Array.isArray( attributes.highlights ) ? attributes.highlights : [];

			const blockProps = useBlockProps( { className: 'cta-highlights' } );

			function setButtons( rows ) {
				setAttributes( { buttons: rows } );
			}

			function setHighlights( rows ) {
				setAttributes( { highlights: rows } );
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

			const highlightItems = highlights.map( function ( highlight, index ) {
				return el(
					'li',
					{ key: highlight.id || 'hl-' + index, className: 'cta-highlights__check' },
					el(
						'span',
						{ className: 'cta-highlights__check-icon', contentEditable: false, 'aria-hidden': true },
						checkIcon()
					),
					el( RichText, {
						tagName: 'span',
						className: 'cta-highlights__check-text',
						value: highlight.text || '',
						allowedFormats: [ 'core/bold', 'core/italic' ],
						placeholder: __( 'Highlight item', 'chw' ),
						onChange: function ( value ) {
							setHighlights( updateRow( highlights, index, { text: value } ) );
						},
					} ),
					highlightActions( highlights, index, setHighlights )
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
							checked: !! attributes.showLegal,
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
							{ className: 'cta-highlights__box' },
						el(
							'div',
							{ className: 'cta-highlights__main' },
							el( RichText, {
								tagName: 'span',
								className: '_eyebrow cta-highlights__badge',
								value: attributes.badgeText || '',
								allowedFormats: [],
								placeholder: __( 'Limited-time offer', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { badgeText: value } );
								},
							} ),
							el( RichText, {
								tagName: 'h3',
								className: 'cta-highlights__heading _text-style -h1',
								value: attributes.heading || '',
								allowedFormats: [ 'core/bold', 'core/text-color' ],
								placeholder: __( 'First Month FREE. Sign up today.', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { heading: value } );
								},
							} ),
							el( RichText, {
								tagName: 'p',
								className: 'cta-highlights__intro',
								value: attributes.description || '',
								allowedFormats: [ 'core/bold', 'core/italic' ],
								placeholder: __( 'Add a supporting sentence.', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { description: value } );
								},
							} ),
							buttonPreview.length
								? el(
										'div',
										{ className: 'cta-highlights__buttons _flex -align-center', contentEditable: false },
										buttonPreview
								  )
								: null,
							el( RichText, {
								tagName: 'p',
								className: 'cta-highlights__subtext _text -sm',
								value: attributes.subText || '',
								allowedFormats: [ 'core/bold', 'core/italic' ],
								placeholder: __( 'Optional fine print below the buttons.', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { subText: value } );
								},
							} )
						),
						el(
							'aside',
							{ className: 'cta-highlights__info' },
							el( RichText, {
								tagName: 'span',
								className: '_eyebrow -plain cta-highlights__info-eyebrow',
								value: attributes.infoEyebrow || '',
								allowedFormats: [],
								placeholder: __( 'What you get', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { infoEyebrow: value } );
								},
							} ),
							el( 'ul', { className: 'cta-highlights__checks' }, highlightItems ),
							el(
								'div',
								{ className: 'cta-highlights__check-add', contentEditable: false },
								el(
									Button,
									{
										variant: 'secondary',
										onClick: function () {
											setHighlights( highlights.concat( [ {
												id: uid( 'hl' ),
												text: '',
											} ] ) );
										},
									},
									__( 'Add highlight', 'chw' )
								)
							),
							el( RichText, {
								tagName: 'p',
								className: 'cta-highlights__info-text',
								value: attributes.infoText || '',
								allowedFormats: [ 'core/bold', 'core/link' ],
								placeholder: __( 'Questions? Call 1-800-000-0000', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { infoText: value } );
								},
							} )
						),
						attributes.showLegal && blockData.legalText
							? el( 'div', {
									className: 'cta-highlights__legal _text -sm',
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
