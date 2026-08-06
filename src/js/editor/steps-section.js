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

	const stepsData = window.chwStepsData || {};

	const MAX_STEPS = 4;
	const MAX_BUTTONS = 2;

	const BUTTON_MODIFIER_OPTIONS = [
		{ label: __( 'None', 'chw' ), value: '' },
		{ label: __( 'Arrow', 'chw' ), value: 'arrow' },
		{ label: __( 'Phone', 'chw' ), value: 'phone' },
		{ label: __( 'Download', 'chw' ), value: 'download' },
	];

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
			{ className: 'steps-section__step-actions', contentEditable: false },
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

	registerBlockType( 'chw/steps-section', {
		title: __( 'Steps Section', 'chw' ),
		description: __( 'Numbered steps with a connector, plus CTA buttons and optional legal text.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'editor-ol',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			eyebrow: { type: 'string', default: '' },
			heading: { type: 'string', default: '' },
			headingAccent: { type: 'string', default: '' },
			description: { type: 'string', default: '' },
			steps: { type: 'array', default: [] },
			buttons: { type: 'array', default: [] },
			showLegal: { type: 'boolean', default: false },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const steps = Array.isArray( attributes.steps ) ? attributes.steps : [];
			const buttons = Array.isArray( attributes.buttons ) ? attributes.buttons : [];

			const blockProps = useBlockProps( { className: 'steps-section' } );

			function setSteps( rows ) {
				setAttributes( { steps: rows } );
			}

			function setButtons( rows ) {
				setAttributes( { buttons: rows } );
			}

			// --- Step cards ------------------------------------------------------
			const stepCards = steps.map( function ( step, index ) {
				return el(
					'div',
					{ key: step.id || 'step-' + index, className: 'steps-section__step _text -align-center' },
					el(
						'div',
						{ className: 'steps-section__badge', contentEditable: false },
						el( 'span', {
							className: 'steps-section__icon',
							dangerouslySetInnerHTML: { __html: step.icon || '' },
						} ),
						el( 'span', { className: 'steps-section__number' }, index + 1 )
					),
					el( RichText, {
						tagName: 'h3',
						className: 'steps-section__step-title _text -tertiary',
						value: step.title || '',
						allowedFormats: [ 'core/bold', 'core/italic' ],
						placeholder: __( 'Step title', 'chw' ),
						onChange: function ( value ) {
							setSteps( updateRow( steps, index, { title: value } ) );
						},
					} ),
					el( RichText, {
						tagName: 'p',
						className: 'steps-section__step-text _text -muted',
						value: step.description || '',
						allowedFormats: [ 'core/bold', 'core/italic' ],
						placeholder: __( 'Step description', 'chw' ),
						onChange: function ( value ) {
							setSteps( updateRow( steps, index, { description: value } ) );
						},
					} ),
					el(
						'div',
						{ className: 'steps-section__icon-input', contentEditable: false },
						el( TextareaControl, {
							label: __( 'Icon (paste SVG)', 'chw' ),
							value: step.icon || '',
							rows: 2,
							onChange: function ( value ) {
								setSteps( updateRow( steps, index, { icon: value } ) );
							},
						} )
					),
					rowActions( steps, index, setSteps )
				);
			} );

			// --- CTA button rows (Inspector) -------------------------------------
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
							{ help: __( 'The first button is styled as primary; the second as outline.', 'chw' ) },
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
						{ className: 'steps-section__inner' },

						// Header.
						el(
							'div',
							{ className: 'steps-section__header _text -align-center' },
							el( RichText, {
								tagName: 'h2',
								className: 'steps-section__heading _text -tertiary',
								value: attributes.heading || '',
								allowedFormats: [ 'core/bold', 'core/italic', 'core/text-color' ],
								placeholder: __( 'Heading', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { heading: value } );
								},
							} ),
							el( RichText, {
								tagName: 'p',
								className: 'steps-section__intro _text -muted _text-size -lg',
								value: attributes.description || '',
								allowedFormats: [ 'core/bold', 'core/italic' ],
								placeholder: __( 'Add an introductory sentence describing the steps.', 'chw' ),
								onChange: function ( value ) {
									setAttributes( { description: value } );
								},
							} )
						),

						// Steps.
						el(
							'div',
							{ className: 'steps-section__steps', 'data-count': steps.length },
							stepCards
						),
						steps.length < MAX_STEPS
							? el(
									'div',
									{ className: 'steps-section__add', contentEditable: false },
									el(
										Button,
										{
											variant: 'secondary',
											onClick: function () {
												setSteps( steps.concat( [ {
													id: uid( 'step' ),
													icon: '',
													title: '',
													description: '',
												} ] ) );
											},
										},
										__( 'Add step', 'chw' )
									)
							  )
							: null,

						// Legal preview.
						attributes.showLegal && stepsData.legalText
							? el( 'div', {
									className: 'steps-section__legal _text -align-center _text -muted',
									contentEditable: false,
									dangerouslySetInnerHTML: { __html: stepsData.legalText },
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
