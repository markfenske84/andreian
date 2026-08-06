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
	const { createElement: el, Fragment, useEffect } = wp.element;
	const { __ } = wp.i18n;

	const blockData = window.chwComparisonTableData || {};

	const MIN_COMPARISONS = 2;
	const MAX_COMPARISONS = 3;

	const BUTTON_MODIFIER_OPTIONS = [
		{ label: __( 'None', 'chw' ), value: '' },
		{ label: __( 'Arrow', 'chw' ), value: 'arrow' },
		{ label: __( 'Phone', 'chw' ), value: 'phone' },
		{ label: __( 'Download', 'chw' ), value: 'download' },
	];

	function variantForIndex( index ) {
		if ( index === 1 ) {
			return 'secondary';
		}
		if ( index === 2 ) {
			return 'muted';
		}
		return 'primary';
	}

	function uid( prefix ) {
		return prefix + '-' + Math.random().toString( 36 ).slice( 2, 9 );
	}

	function emptyComparison() {
		return {
			id: uid( 'comparison' ),
			title: '',
			icon: '',
			features: [],
		};
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

	function buttonClass( modifier ) {
		let cls = '_button -primary';
		if ( modifier ) {
			cls += ' -' + modifier;
		}
		return cls;
	}

	function cardActions( rows, index, onChange ) {
		return el(
			'div',
			{ className: 'comparison-table__card-actions', contentEditable: false },
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
				label: __( 'Remove comparison', 'chw' ),
				disabled: rows.length <= MIN_COMPARISONS,
				onClick: function () {
					onChange( removeRow( rows, index ) );
				},
			} )
		);
	}

	registerBlockType( 'chw/comparison-table', {
		title: __( 'Comparison Table', 'chw' ),
		description: __( 'Side-by-side comparison cards with feature lists, a section CTA, and optional legal disclaimer.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'columns',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			eyebrow: { type: 'string', default: '' },
			heading: { type: 'string', default: '' },
			description: { type: 'string', default: '' },
			comparisons: { type: 'array', default: [] },
			button: {
				type: 'object',
				default: {
					text: '',
					url: '',
					new_tab: false,
					modifier: 'arrow',
				},
			},
			showLegal: { type: 'boolean', default: true },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const comparisons = Array.isArray( attributes.comparisons ) ? attributes.comparisons : [];
			const button = attributes.button || {};

			const blockProps = useBlockProps( { className: 'comparison-table' } );

			useEffect( function () {
				const comps = Array.isArray( attributes.comparisons ) ? attributes.comparisons : [];

				if ( comps.length >= MIN_COMPARISONS ) {
					return;
				}

				const padded = comps.slice();
				while ( padded.length < MIN_COMPARISONS ) {
					padded.push( emptyComparison() );
				}
				setAttributes( { comparisons: padded } );
			}, [] );

			function setComparisons( rows ) {
				setAttributes( { comparisons: rows } );
			}

			function setButton( patch ) {
				setAttributes( { button: Object.assign( {}, button, patch ) } );
			}

			const comparisonCards = comparisons.map( function ( comparison, index ) {
				const features = Array.isArray( comparison.features ) ? comparison.features : [];
				const variant = variantForIndex( index );

				const featureRows = features.map( function ( feature, fIndex ) {
					return el(
						'li',
						{
							key: 'feat-' + index + '-' + fIndex,
							className: 'comparison-table__feature comparison-table__feature-edit',
						},
						el( RichText, {
							tagName: 'span',
							value: feature || '',
							allowedFormats: [ 'core/bold', 'core/italic' ],
							placeholder: __( 'Feature', 'chw' ),
							onChange: function ( value ) {
								const next = features.slice();
								next[ fIndex ] = value;
								setComparisons( updateRow( comparisons, index, { features: next } ) );
							},
						} ),
						el( Button, {
							icon: 'no-alt',
							label: __( 'Remove feature', 'chw' ),
							isSmall: true,
							onClick: function () {
								const next = features.filter( function ( item, i ) {
									return i !== fIndex;
								} );
								setComparisons( updateRow( comparisons, index, { features: next } ) );
							},
						} )
					);
				} );

				return el(
					'article',
					{
						key: comparison.id || 'comparison-' + index,
						className: 'comparison-table__card -' + variant,
					},
					el(
						'div',
						{ className: 'comparison-table__card-head' },
						el( 'div', {
							className: 'comparison-table__icon',
							contentEditable: false,
							dangerouslySetInnerHTML: { __html: comparison.icon || '' },
						} ),
						el( RichText, {
							tagName: 'h3',
							className: 'comparison-table__card-title _text -secondary',
							value: comparison.title || '',
							allowedFormats: [ 'core/bold', 'core/italic' ],
							placeholder: __( 'Comparison title', 'chw' ),
							onChange: function ( value ) {
								setComparisons( updateRow( comparisons, index, { title: value } ) );
							},
						} )
					),
					el( 'ul', { className: 'comparison-table__features' }, featureRows ),
					el(
						'div',
						{ className: 'comparison-table__add', contentEditable: false },
						el(
							Button,
							{
								variant: 'secondary',
								isSmall: true,
								onClick: function () {
									setComparisons(
										updateRow( comparisons, index, {
											features: features.concat( [ '' ] ),
										} )
									);
								},
							},
							__( 'Add feature', 'chw' )
						)
					),
					el(
						'div',
						{ className: 'comparison-table__icon-input', contentEditable: false },
						el( TextareaControl, {
							label: __( 'Icon (paste SVG)', 'chw' ),
							value: comparison.icon || '',
							rows: 2,
							onChange: function ( value ) {
								setComparisons( updateRow( comparisons, index, { icon: value } ) );
							},
						} )
					),
					cardActions( comparisons, index, setComparisons )
				);
			} );

			const comparisonSettingRows = comparisons.map( function ( comparison, index ) {
				return el(
					'div',
					{ key: 'comparison-set-' + index, className: 'chw-masthead-repeater-row' },
					el(
						'div',
						{ className: 'chw-masthead-repeater-row__head' },
						el( 'span', null, ( comparison.title || __( 'Comparison', 'chw' ) ) + ' ' + ( index + 1 ) )
					),
					el( TextareaControl, {
						label: __( 'Icon (paste SVG)', 'chw' ),
						value: comparison.icon || '',
						rows: 3,
						onChange: function ( value ) {
							setComparisons( updateRow( comparisons, index, { icon: value } ) );
						},
					} )
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
						{ title: __( 'Section CTA', 'chw' ), initialOpen: true },
						el( TextControl, {
							label: __( 'Button Text', 'chw' ),
							value: button.text || '',
							onChange: function ( value ) {
								setButton( { text: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Button URL', 'chw' ),
							value: button.url || '',
							onChange: function ( value ) {
								setButton( { url: value } );
							},
						} ),
						el( SelectControl, {
							label: __( 'Icon', 'chw' ),
							value: button.modifier || 'arrow',
							options: BUTTON_MODIFIER_OPTIONS,
							onChange: function ( value ) {
								setButton( { modifier: value } );
							},
						} ),
						el( ToggleControl, {
							label: __( 'Open in new tab', 'chw' ),
							checked: !! button.new_tab,
							onChange: function ( value ) {
								setButton( { new_tab: value } );
							},
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Comparison Cards', 'chw' ), initialOpen: false },
						el(
							BaseControl,
							{
								help: __(
									'Card accent colors are fixed by position: 1st orange, 2nd blue, 3rd gray. Reorder cards to change which column gets each color.',
									'chw'
								),
							},
							comparisonSettingRows
						),
						comparisons.length < MAX_COMPARISONS
							? el(
									Button,
									{
										variant: 'secondary',
										className: 'chw-editor-add-button',
										onClick: function () {
											setComparisons( comparisons.concat( [ emptyComparison() ] ) );
										},
									},
									__( 'Add comparison', 'chw' )
							  )
							: null
					),
					el(
						PanelBody,
						{ title: __( 'Legal', 'chw' ), initialOpen: false },
						el( ToggleControl, {
							label: __( 'Show legal disclaimer', 'chw' ),
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
						{ className: 'comparison-table__inner' },
						el(
							'div',
							{ className: 'comparison-table__header _text -align-left' },
							el( RichText, {
								tagName: 'span',
								className: '_eyebrow -plain _text -primary',
								value: attributes.eyebrow || '',
								allowedFormats: [],
								placeholder: __( 'Eyebrow', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { eyebrow: value } );
								},
							} ),
							el( RichText, {
								tagName: 'h2',
								className: 'comparison-table__heading _text -secondary',
								value: attributes.heading || '',
								allowedFormats: [ 'core/bold', 'core/italic', 'core/text-color' ],
								placeholder: __( 'Heading', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { heading: value } );
								},
							} ),
							el( RichText, {
								tagName: 'p',
								className: 'comparison-table__intro _text -muted _text-size -lg',
								value: attributes.description || '',
								allowedFormats: [ 'core/bold', 'core/italic' ],
								placeholder: __( 'Add an introductory sentence describing the comparison.', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { description: value } );
								},
							} )
						),
						el(
							'div',
							{
								className: 'comparison-table__cards',
								'data-count': comparisons.length,
							},
							comparisonCards
						),
						comparisons.length < MAX_COMPARISONS
							? el(
									'div',
									{ className: 'comparison-table__add', contentEditable: false },
									el(
										Button,
										{
											variant: 'secondary',
											onClick: function () {
												setComparisons( comparisons.concat( [ emptyComparison() ] ) );
											},
										},
										__( 'Add comparison', 'chw' )
									)
							  )
							: null,
						button.text
							? el(
									'div',
									{ className: 'comparison-table__cta', contentEditable: false },
									el(
										'span',
										{
											className: buttonClass( button.modifier || 'arrow' ),
										},
										button.text
									)
							  )
							: null,
						blockData.legalText && attributes.showLegal !== false
							? el( 'div', {
									className: 'comparison-table__legal _text -align-left _text -muted',
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
