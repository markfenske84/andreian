( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { RichText, useBlockProps } = wp.blockEditor;
	const { TextControl, TextareaControl, ToggleControl, Button } = wp.components;
	const { createElement: el } = wp.element;
	const { __ } = wp.i18n;

	const blockData = window.chwAudienceSectionData || {};

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

	function pad( n ) {
		return ( n < 10 ? '0' : '' ) + n;
	}

	function cardActions( rows, index, onChange ) {
		return el(
			'div',
			{ className: 'audience-section__card-actions', contentEditable: false },
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

	registerBlockType( 'chw/audience-section', {
		title: __( 'Audience Section', 'chw' ),
		description: __( 'An eyebrow badge, dual-color heading, and an unlimited grid of clickable, auto-numbered audience cards (four per row).', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'groups',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			eyebrowIcon: { type: 'string', default: '' },
			eyebrowText: { type: 'string', default: '' },
			heading: { type: 'string', default: '' },
			description: { type: 'string', default: '' },
			cards: { type: 'array', default: [] },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const cards = Array.isArray( attributes.cards ) ? attributes.cards : [];

			const blockProps = useBlockProps( { className: 'audience-section' } );

			function setCards( rows ) {
				setAttributes( { cards: rows } );
			}

			const cardItems = cards.map( function ( card, index ) {
				return el(
					'div',
					{
						key: card.id || 'card-' + index,
						className: 'audience-section__card-cell',
					},
					el(
						'div',
						{ className: 'audience-section__card' },
						el(
							'span',
							{ className: 'audience-section__number', contentEditable: false, 'aria-hidden': true },
							pad( index + 1 )
						),
						el( 'div', {
							className: 'audience-section__icon',
							contentEditable: false,
							'aria-hidden': true,
							dangerouslySetInnerHTML: { __html: card.icon || '' },
						} ),
						el( RichText, {
							tagName: 'h3',
							className: 'audience-section__card-title _text -secondary',
							value: card.headline || '',
							allowedFormats: [ 'core/bold', 'core/italic' ],
							placeholder: __( 'Card headline', 'chw' ),
							onChange: function ( value ) {
								setCards( updateRow( cards, index, { headline: value } ) );
							},
						} ),
						el( RichText, {
							tagName: 'p',
							className: 'audience-section__card-text _text -muted',
							value: card.description || '',
							allowedFormats: [ 'core/bold', 'core/italic' ],
							placeholder: __( 'Card description', 'chw' ),
							onChange: function ( value ) {
								setCards( updateRow( cards, index, { description: value } ) );
							},
						} ),
						el(
							'div',
							{ className: 'audience-section__icon-input', contentEditable: false },
							el( TextareaControl, {
								label: __( 'Icon (paste SVG)', 'chw' ),
								value: card.icon || '',
								rows: 2,
								onChange: function ( value ) {
									setCards( updateRow( cards, index, { icon: value } ) );
								},
							} ),
							el( TextControl, {
								label: __( 'CTA label', 'chw' ),
								value: card.ctaLabel || '',
								onChange: function ( value ) {
									setCards( updateRow( cards, index, { ctaLabel: value } ) );
								},
							} ),
							el( TextControl, {
								label: __( 'CTA URL', 'chw' ),
								value: card.url || '',
								onChange: function ( value ) {
									setCards( updateRow( cards, index, { url: value } ) );
								},
							} ),
							el( ToggleControl, {
								label: __( 'Open in new tab', 'chw' ),
								checked: !! card.new_tab,
								onChange: function ( value ) {
									setCards( updateRow( cards, index, { new_tab: value } ) );
								},
							} )
						)
					),
					cardActions( cards, index, setCards )
				);
			} );

			return el(
				'section',
				blockProps,
				el(
					'div',
					{ className: '_container' },
					el(
						'div',
						{ className: 'audience-section__header _text -align-left' },
					el(
						'span',
						{ className: '_eyebrow -brand-orange' },
						el( 'span', {
							className: '_eyebrow__icon',
							contentEditable: false,
							'aria-hidden': true,
							dangerouslySetInnerHTML: { __html: attributes.eyebrowIcon || '' },
						} ),
						el( RichText, {
							tagName: 'span',
							className: '_eyebrow__label',
							value: attributes.eyebrowText || '',
							allowedFormats: [],
							placeholder: __( 'Eyebrow text', 'chw' ),
							onChange: function ( value ) {
								setAttributes( { eyebrowText: value } );
							},
						} )
					),
					el(
						'div',
						{ className: 'audience-section__eyebrow-input', contentEditable: false },
						el( TextareaControl, {
							label: __( 'Eyebrow icon (paste SVG)', 'chw' ),
							value: attributes.eyebrowIcon || '',
							rows: 2,
							onChange: function ( value ) {
								setAttributes( { eyebrowIcon: value } );
							},
						} )
					),
					el( RichText, {
						tagName: 'h2',
						className: 'audience-section__heading _text -secondary',
						value: attributes.heading || '',
						allowedFormats: [ 'core/bold', 'core/text-color' ],
						placeholder: __( 'Coverage built around how you live and work.', 'chw' ),
						onChange: function ( value ) {
							setAttributes( { heading: value } );
						},
					} ),
					el( RichText, {
						tagName: 'p',
						className: 'audience-section__intro _text -muted _text-size -lg',
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
					{ className: 'audience-section__editor-stack' },
					el(
						'div',
						{ className: 'audience-section__cards', 'data-count': cards.length },
						cardItems
					),
					el(
						'div',
						{ className: 'audience-section__add', contentEditable: false },
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
										ctaLabel: '',
										url: '',
										new_tab: false,
									} ] ) );
								},
							},
							__( 'Add card', 'chw' )
						)
					)
				),
				blockData.legalText
					? el( 'div', {
							className: 'audience-section__legal _text -align-center _text -muted',
							contentEditable: false,
							dangerouslySetInnerHTML: { __html: blockData.legalText },
					  } )
					: null
				)
			);
		},

		save: function () {
			return null;
		},
	} );
} )( window.wp );
