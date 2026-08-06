( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, TextareaControl, Button } = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { __ } = wp.i18n;

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

	function itemActions( rows, index, onChange ) {
		return el(
			'div',
			{ className: 'highlights-list__item-actions', contentEditable: false },
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
				label: __( 'Remove item', 'chw' ),
				onClick: function () {
					onChange( removeRow( rows, index ) );
				},
			} )
		);
	}

	registerBlockType( 'chw/highlights-list', {
		title: __( 'Highlights List', 'chw' ),
		description: __( 'A card-style list with an eyebrow header and description/value rows.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'list-view',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			eyebrowIcon: { type: 'string', default: '' },
			eyebrowText: { type: 'string', default: '' },
			items: { type: 'array', default: [] },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const items = Array.isArray( attributes.items ) ? attributes.items : [];
			const blockProps = useBlockProps( { className: 'highlights-list' } );

			function setItems( rows ) {
				setAttributes( { items: rows } );
			}

			const itemRows = items.map( function ( item, index ) {
				return el(
					'div',
					{
						key: item.id || 'item-' + index,
						className: 'highlights-list__item highlights-list__item--editor',
					},
					el(
						'div',
						{ className: 'highlights-list__item-row' },
						el( RichText, {
							tagName: 'div',
							className: 'highlights-list__description _text -muted',
							value: item.description || '',
							allowedFormats: [ 'core/bold', 'core/italic' ],
							placeholder: __( 'Description', 'chw' ),
							onChange: function ( value ) {
								setItems( updateRow( items, index, { description: value } ) );
							},
						} ),
						el( RichText, {
							tagName: 'div',
							className: 'highlights-list__value _text -tertiary _text-size -xl',
							value: item.value || '',
							allowedFormats: [ 'core/bold', 'core/italic' ],
							placeholder: __( 'Value', 'chw' ),
							onChange: function ( value ) {
								setItems( updateRow( items, index, { value: value } ) );
							},
						} )
					),
					itemActions( items, index, setItems )
				);
			} );

			const inspectorPanels = items.map( function ( item, index ) {
				return el(
					PanelBody,
					{
						key: item.id || 'panel-' + index,
						title: __( 'Item', 'chw' ) + ' ' + ( index + 1 ),
						initialOpen: false,
					},
					itemActions( items, index, setItems )
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
						{ title: __( 'Eyebrow', 'chw' ), initialOpen: true },
						el( TextareaControl, {
							label: __( 'Eyebrow icon (paste SVG)', 'chw' ),
							value: attributes.eyebrowIcon || '',
							rows: 3,
							onChange: function ( value ) {
								setAttributes( { eyebrowIcon: value } );
							},
						} )
					),
					inspectorPanels
				),
				el(
					'div',
					blockProps,
					el(
						'header',
						{ className: 'highlights-list__header' },
						el(
							'span',
							{ className: '_eyebrow -plain _text -secondary highlights-list__eyebrow' },
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
						)
					),
					el(
						'div',
						{ className: 'highlights-list__items' },
						itemRows
					),
					el(
						'div',
						{ className: 'highlights-list__add', contentEditable: false },
						el(
							Button,
							{
								variant: 'secondary',
								onClick: function () {
									setItems( items.concat( [ {
										id: uid( 'item' ),
										description: '',
										value: '',
									} ] ) );
								},
							},
							__( 'Add item', 'chw' )
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
