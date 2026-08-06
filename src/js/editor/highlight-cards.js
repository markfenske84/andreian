( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls, useBlockProps } = wp.blockEditor;
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

	const blockData = window.chwHighlightCardsData || {};

	const MAX_BUTTONS = 2;

	const BADGE_STYLE_OPTIONS = [
		{ label: __( 'Plain', 'chw' ), value: 'plain' },
		{ label: __( 'On light', 'chw' ), value: 'on-light' },
		{ label: __( 'On dark', 'chw' ), value: 'on-dark' },
		{ label: __( 'Brand orange', 'chw' ), value: 'brand-orange' },
	];

	const ALLOWED_BADGE_STYLES = BADGE_STYLE_OPTIONS.map( function ( option ) {
		return option.value;
	} );

	const STYLES_WITH_BUNDLED_TEXT_COLOR = [ 'on-dark', 'brand-orange' ];

	const CARDS_PER_ROW_OPTIONS = [
		{ label: __( '3 cards', 'chw' ), value: '3' },
		{ label: __( '4 cards', 'chw' ), value: '4' },
	];

	const CARD_STYLE_OPTIONS = [
		{ label: __( 'Blue', 'chw' ), value: 'blue' },
		{ label: __( 'Orange', 'chw' ), value: 'orange' },
	];

	const LEGAL_ALIGN_OPTIONS = [
		{ label: __( 'Center', 'chw' ), value: 'center' },
		{ label: __( 'Left', 'chw' ), value: 'left' },
		{ label: __( 'Right', 'chw' ), value: 'right' },
	];

	const BUTTON_MODIFIER_OPTIONS = [
		{ label: __( 'None', 'chw' ), value: '' },
		{ label: __( 'Arrow', 'chw' ), value: 'arrow' },
		{ label: __( 'Phone', 'chw' ), value: 'phone' },
		{ label: __( 'Download', 'chw' ), value: 'download' },
	];

	function safeBadgeStyle( value ) {
		return ALLOWED_BADGE_STYLES.indexOf( value ) !== -1 ? value : 'plain';
	}

	function eyebrowClasses( badgeStyle ) {
		const style = safeBadgeStyle( badgeStyle );
		const classes = [ '_eyebrow' ];

		classes.push( '-' + style );

		if ( STYLES_WITH_BUNDLED_TEXT_COLOR.indexOf( style ) === -1 ) {
			classes.push( '_text', '-tertiary' );
		}

		return classes.join( ' ' );
	}

	function wrapperClassName( attributes ) {
		const classes = [ 'highlight-cards' ];
		if ( attributes.cardStyle === 'orange' ) {
			classes.push( '-card-style-orange' );
		}
		return classes.join( ' ' );
	}

	function cardsGridClassName( cardsPerRow ) {
		const cols = parseInt( cardsPerRow, 10 ) === 4 ? 4 : 3;
		return cols === 4 ? 'highlight-cards__cards -cols-4' : 'highlight-cards__cards';
	}

	function legalClassName( legalAlign ) {
		const align = [ 'center', 'left', 'right' ].indexOf( legalAlign ) !== -1 ? legalAlign : 'center';
		return 'highlight-cards__legal _text -muted _text -align-' + align;
	}

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
		let cls = '_button ' + ( index === 0 ? '-primary' : '-outline -text' );
		if ( modifier ) {
			cls += ' -' + modifier;
		}
		return cls;
	}

	function cardActions( rows, index, onChange ) {
		return el(
			'div',
			{ className: 'highlight-cards__card-actions', contentEditable: false },
			el( Button, {
				icon: 'arrow-left-alt2',
				label: __( 'Move left', 'chw' ),
				disabled: index === 0,
				onClick: function () {
					onChange( moveRow( rows, index, -1 ) );
				},
			} ),
			el( Button, {
				icon: 'arrow-right-alt2',
				label: __( 'Move right', 'chw' ),
				disabled: index === rows.length - 1,
				onClick: function () {
					onChange( moveRow( rows, index, 1 ) );
				},
			} ),
			el( Button, {
				icon: 'trash',
				isDestructive: true,
				label: __( 'Remove card', 'chw' ),
				onClick: function () {
					onChange( removeRow( rows, index ) );
				},
			} )
		);
	}

	registerBlockType( 'chw/highlight-cards', {
		title: __( 'Highlight Cards', 'chw' ),
		description: __( 'Icon cards in a three-column grid with CTA buttons and optional legal text.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'grid-view',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			eyebrow: { type: 'string', default: '' },
			heading: { type: 'string', default: '' },
			headingAccent: { type: 'string', default: '' },
			description: { type: 'string', default: '' },
			cards: { type: 'array', default: [] },
			buttons: { type: 'array', default: [] },
			showLegal: { type: 'boolean', default: false },
			badgeStyle: { type: 'string', default: 'plain' },
			cardsPerRow: { type: 'number', default: 3 },
			cardStyle: { type: 'string', default: 'blue' },
			legalAlign: { type: 'string', default: 'center' },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const cards = Array.isArray( attributes.cards ) ? attributes.cards : [];
			const buttons = Array.isArray( attributes.buttons ) ? attributes.buttons : [];

			const blockProps = useBlockProps( { className: wrapperClassName( attributes ) } );

			function setCards( rows ) {
				setAttributes( { cards: rows } );
			}

			function setButtons( rows ) {
				setAttributes( { buttons: rows } );
			}

			const cardItems = cards.map( function ( card, index ) {
				return el(
					'div',
					{
						key: card.id || 'card-' + index,
						className: 'highlight-cards__card-cell',
					},
					el(
						'article',
						{ className: 'highlight-cards__card' },
						el( 'div', {
							className: 'highlight-cards__icon',
							contentEditable: false,
							dangerouslySetInnerHTML: { __html: card.icon || '' },
						} ),
						el( RichText, {
							tagName: 'h3',
							className: 'highlight-cards__card-title _text -secondary',
							value: card.headline || '',
							allowedFormats: [ 'core/bold', 'core/italic' ],
							placeholder: __( 'Card headline', 'chw' ),
							onChange: function ( value ) {
								setCards( updateRow( cards, index, { headline: value } ) );
							},
						} ),
						el( RichText, {
							tagName: 'p',
							className: 'highlight-cards__card-text _text -muted',
							value: card.description || '',
							allowedFormats: [ 'core/bold', 'core/italic' ],
							placeholder: __( 'Card description', 'chw' ),
							onChange: function ( value ) {
								setCards( updateRow( cards, index, { description: value } ) );
							},
						} ),
						el(
							'div',
							{ className: 'highlight-cards__icon-input', contentEditable: false },
							el( TextareaControl, {
								label: __( 'Icon (paste SVG)', 'chw' ),
								value: card.icon || '',
								rows: 2,
								onChange: function ( value ) {
									setCards( updateRow( cards, index, { icon: value } ) );
								},
							} )
						)
					),
					cardActions( cards, index, setCards )
				);
			} );

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
						{ title: __( 'Badge Style', 'chw' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Style preset', 'chw' ),
							value: attributes.badgeStyle || 'plain',
							options: BADGE_STYLE_OPTIONS,
							onChange: function ( value ) {
								setAttributes( { badgeStyle: value } );
							},
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Layout', 'chw' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Cards per row', 'chw' ),
							value: String( attributes.cardsPerRow || 3 ),
							options: CARDS_PER_ROW_OPTIONS,
							onChange: function ( value ) {
								setAttributes( { cardsPerRow: parseInt( value, 10 ) } );
							},
						} ),
						el( SelectControl, {
							label: __( 'Card style', 'chw' ),
							value: attributes.cardStyle || 'blue',
							options: CARD_STYLE_OPTIONS,
							onChange: function ( value ) {
								setAttributes( { cardStyle: value } );
							},
						} )
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
						{ title: __( 'Legal Text', 'chw' ), initialOpen: false },
						el( ToggleControl, {
							label: __( 'Show legal text', 'chw' ),
							help: __( 'Pulls the global legal text set in the Customizer.', 'chw' ),
							checked: !! attributes.showLegal,
							onChange: function ( value ) {
								setAttributes( { showLegal: value } );
							},
						} ),
						el( SelectControl, {
							label: __( 'Alignment', 'chw' ),
							value: attributes.legalAlign || 'center',
							options: LEGAL_ALIGN_OPTIONS,
							onChange: function ( value ) {
								setAttributes( { legalAlign: value } );
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
							{ className: 'highlight-cards__header _text -align-left' },
							el(
								'span',
								{ className: eyebrowClasses( attributes.badgeStyle ) },
								el( RichText, {
									tagName: 'span',
									className: '_eyebrow__label',
									value: attributes.eyebrow || '',
									allowedFormats: [],
									placeholder: __( 'Why Choice', 'chw' ),
									onChange: function ( value ) {
										setAttributes( { eyebrow: value } );
									},
								} )
							),
							el( RichText, {
								tagName: 'h2',
								className: 'highlight-cards__heading _text -secondary',
								value: attributes.heading || '',
								allowedFormats: [ 'core/bold', 'core/italic', 'core/text-color' ],
								placeholder: __( 'Heading', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { heading: value } );
								},
							} ),
							el( RichText, {
								tagName: 'p',
								className: 'highlight-cards__intro _text -muted _text-size -lg',
								value: attributes.description || '',
								allowedFormats: [ 'core/bold', 'core/italic' ],
								placeholder: __( 'Add an introductory sentence.', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { description: value } );
								},
							} )
						),
						el(
							'div',
							{ className: 'highlight-cards__editor-stack' },
							el(
								'div',
								{ className: cardsGridClassName( attributes.cardsPerRow ), 'data-count': cards.length },
								cardItems
							),
							el(
								'div',
								{ className: 'highlight-cards__add', contentEditable: false },
								el(
									Button,
									{
										variant: 'secondary',
										onClick: function () {
											setCards( cards.concat( [ {
												id: uid( 'card' ),
												icon: '',
												headline: '',
												description: '',
											} ] ) );
										},
									},
									__( 'Add card', 'chw' )
								)
							),
							buttonPreview.length
								? el(
										'div',
										{ className: 'highlight-cards__buttons _flex -align-center -justify-center', contentEditable: false },
										buttonPreview
								  )
								: null
						),
						attributes.showLegal && blockData.legalText
							? el( 'div', {
									className: legalClassName( attributes.legalAlign ),
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
