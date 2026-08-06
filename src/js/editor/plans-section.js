( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls, useBlockProps } = wp.blockEditor;
	const {
		PanelBody,
		ToggleControl,
		TextControl,
		BaseControl,
		Button,
	} = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { __ } = wp.i18n;

	const plansData = window.chwPlansData || {};

	const MAX_PLANS = 3;

	// Card styling is fixed by position: 1st blue, 2nd orange (popular), 3rd gray.
	function variantForIndex( index ) {
		if ( index === 1 ) {
			return 'featured';
		}
		if ( index === 2 ) {
			return 'muted';
		}
		return 'blue';
	}

	function buttonColorForVariant( variant ) {
		if ( variant === 'featured' ) {
			return '-primary';
		}
		if ( variant === 'blue' ) {
			return '-secondary';
		}
		return '-outline -text';
	}

	function labelColorForVariant( variant ) {
		if ( variant === 'featured' ) {
			return '-primary';
		}
		if ( variant === 'muted' ) {
			return '-tertiary';
		}
		return '-secondary';
	}

	const CHECK_ICON = el(
		'svg',
		{ className: 'plans-section__check', viewBox: '0 0 24 24', 'aria-hidden': 'true', focusable: 'false' },
		el( 'path', {
			d: 'M20 6 9 17l-5-5',
			fill: 'none',
			stroke: 'currentColor',
			'stroke-width': '2.5',
			'stroke-linecap': 'round',
			'stroke-linejoin': 'round',
		} )
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
	 * Card action buttons (move left / move right / remove).
	 */
	function cardActions( rows, index, onChange ) {
		return el(
			'div',
			{ className: 'plans-section__card-actions', contentEditable: false },
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
				label: __( 'Remove plan', 'chw' ),
				onClick: function () {
					onChange( removeRow( rows, index ) );
				},
			} )
		);
	}

	registerBlockType( 'chw/plans-section', {
		title: __( 'Plans Section', 'chw' ),
		description: __( 'Three plan cards with feature checklists and CTA buttons, on a full-width section.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'feedback',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			eyebrow: { type: 'string', default: '' },
			heading: { type: 'string', default: '' },
			headingAccent: { type: 'string', default: '' },
			description: { type: 'string', default: '' },
			plans: { type: 'array', default: [] },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const plans = Array.isArray( attributes.plans ) ? attributes.plans : [];

			const blockProps = useBlockProps( { className: 'plans-section _bg -quaternary' } );

			function setPlans( rows ) {
				setAttributes( { plans: rows } );
			}

			// --- Plan cards (in-canvas) ------------------------------------------
			const planCards = plans.map( function ( plan, index ) {
				const features = Array.isArray( plan.features ) ? plan.features : [];
				const button = plan.button || {};
				const variant = variantForIndex( index );

				const featureRows = features.map( function ( feature, fIndex ) {
					return el(
						'li',
						{ key: 'feat-' + index + '-' + fIndex, className: 'plans-section__feature plans-section__feature-edit' },
						CHECK_ICON,
						el( RichText, {
							tagName: 'span',
							value: feature || '',
							allowedFormats: [ 'core/bold', 'core/italic' ],
							placeholder: __( 'Feature', 'chw' ),
							onChange: function ( value ) {
								const next = features.slice();
								next[ fIndex ] = value;
								setPlans( updateRow( plans, index, { features: next } ) );
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
								setPlans( updateRow( plans, index, { features: next } ) );
							},
						} )
					);
				} );

				return el(
					'article',
					{
						key: plan.id || 'plan-' + index,
						className: 'plans-section__card -' + variant,
					},
					variant === 'featured'
						? el( 'span', { className: 'plans-section__ribbon', contentEditable: false }, __( 'Most Popular', 'chw' ) )
						: null,
					el(
						'div',
						{ className: 'plans-section__card-head' },
						el( RichText, {
							tagName: 'span',
							className: '_eyebrow -on-light _text ' + labelColorForVariant( variant ) + ' plans-section__card-label',
							value: plan.label || '',
							allowedFormats: [],
							placeholder: __( 'Plan name', 'chw' ),
							onChange: function ( value ) {
								setPlans( updateRow( plans, index, { label: value } ) );
							},
						} ),
						el( RichText, {
							tagName: 'p',
							className: 'plans-section__card-subtitle _text -muted',
							value: plan.subtitle || '',
							allowedFormats: [ 'core/bold', 'core/italic' ],
							placeholder: __( 'Short description', 'chw' ),
							onChange: function ( value ) {
								setPlans( updateRow( plans, index, { subtitle: value } ) );
							},
						} )
					),
					el( 'ul', { className: 'plans-section__features' }, featureRows ),
					el(
						'div',
						{ className: 'plans-section__add', contentEditable: false },
						el(
							Button,
							{
								variant: 'secondary',
								isSmall: true,
								onClick: function () {
									setPlans( updateRow( plans, index, { features: features.concat( [ '' ] ) } ) );
								},
							},
							__( 'Add feature', 'chw' )
						)
					),
					button.text
						? el(
								'div',
								{ className: 'plans-section__card-cta', contentEditable: false },
								el(
									'span',
									{ className: '_button -arrow ' + buttonColorForVariant( variant ) },
									button.text
								)
						  )
						: null,
					cardActions( plans, index, setPlans )
				);
			} );

			// --- Plan settings rows (Inspector) ----------------------------------
			const planSettingRows = plans.map( function ( plan, index ) {
				const button = plan.button || {};

				function setButton( patch ) {
					setPlans( updateRow( plans, index, { button: Object.assign( {}, button, patch ) } ) );
				}

				return el(
					'div',
					{ key: 'plan-set-' + index, className: 'chw-masthead-repeater-row' },
					el(
						'div',
						{ className: 'chw-masthead-repeater-row__head' },
						el( 'span', null, ( plan.label || __( 'Plan', 'chw' ) ) + ' ' + ( index + 1 ) )
					),
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
					el( ToggleControl, {
						label: __( 'Open in new tab', 'chw' ),
						checked: !! button.new_tab,
						onChange: function ( value ) {
							setButton( { new_tab: value } );
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
						{ title: __( 'Plan Cards', 'chw' ), initialOpen: true },
						el(
							BaseControl,
							{ help: __( 'Card styling is fixed by position: 1st blue, 2nd orange (popular), 3rd gray. Configure each card\u2019s CTA button below.', 'chw' ) },
							planSettingRows
						),
						plans.length < MAX_PLANS
							? el(
									Button,
									{
										variant: 'secondary',
										className: 'chw-editor-add-button',
										onClick: function () {
											setPlans( plans.concat( [ {
												id: uid( 'plan' ),
												label: '',
												subtitle: '',
												features: [],
												button: { text: '', url: '', new_tab: false },
											} ] ) );
										},
									},
									__( 'Add plan', 'chw' )
							  )
							: null
					)
				),

				el(
					'section',
					blockProps,
					el(
						'div',
						{ className: '_container' },

						// Header.
						el(
							'div',
							{ className: 'plans-section__header _text -align-left' },
							el( RichText, {
								tagName: 'span',
								className: '_eyebrow -plain _text -tertiary',
								value: attributes.eyebrow || '',
								allowedFormats: [],
								placeholder: __( 'Coverage', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { eyebrow: value } );
								},
							} ),
							el( RichText, {
								tagName: 'h2',
								className: 'plans-section__heading _text -secondary',
								value: attributes.heading || '',
								allowedFormats: [ 'core/bold', 'core/italic', 'core/text-color' ],
								placeholder: __( 'Heading', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { heading: value } );
								},
							} ),
							el( RichText, {
								tagName: 'p',
								className: 'plans-section__intro _text -muted _text-size -lg',
								value: attributes.description || '',
								allowedFormats: [ 'core/bold', 'core/italic' ],
								placeholder: __( 'Add an introductory sentence describing the plans.', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { description: value } );
								},
							} )
						),

						// Cards.
						el(
							'div',
							{ className: 'plans-section__cards', 'data-count': plans.length },
							planCards
						),
						plans.length < MAX_PLANS
							? el(
									'div',
									{ className: 'plans-section__add', contentEditable: false },
									el(
										Button,
										{
											variant: 'secondary',
											onClick: function () {
												setPlans( plans.concat( [ {
													id: uid( 'plan' ),
													label: '',
													subtitle: '',
													features: [],
													button: { text: '', url: '', new_tab: false },
												} ] ) );
											},
										},
										__( 'Add plan', 'chw' )
									)
							  )
							: null,

						// Legal preview (always shown when global legal text exists).
						plansData.legalText
							? el( 'div', {
									className: 'plans-section__legal _text -align-center _text -muted',
									contentEditable: false,
									dangerouslySetInnerHTML: { __html: plansData.legalText },
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
