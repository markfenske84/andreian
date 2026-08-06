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

	const blockData = window.chwCtaBlockData || {};

	const MAX_BUTTONS = 3;

	const BUTTON_MODIFIER_OPTIONS = [
		{ label: __( 'None', 'chw' ), value: '' },
		{ label: __( 'Arrow', 'chw' ), value: 'arrow' },
		{ label: __( 'Phone', 'chw' ), value: 'phone' },
		{ label: __( 'Download', 'chw' ), value: 'download' },
	];

	const BADGE_ICON = el(
		'svg',
		{ viewBox: '0 0 24 24', fill: 'none', 'aria-hidden': true, focusable: false },
		el( 'path', {
			d: 'M12 2l8 4v6c0 5.25-3.5 10-8 12-4.5-2-8-6.75-8-12V6l8-4z',
			stroke: 'currentColor',
			strokeWidth: 1.75,
			strokeLinejoin: 'round',
		} ),
		el( 'path', {
			d: 'M9 12l2 2 4-4',
			stroke: 'currentColor',
			strokeWidth: 1.75,
			strokeLinecap: 'round',
			strokeLinejoin: 'round',
		} )
	);

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

	registerBlockType( 'chw/cta-block', {
		title: __( 'CTA Block', 'chw' ),
		description: __( 'A centered call-to-action box with badge, heading, description, and CTA buttons. Includes the global legal disclaimer.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'megaphone',
		supports: {
			anchor: true,
			html: false,
			align: [ 'wide', 'full' ],
		},
		attributes: {
			align: { type: 'string', default: '' },
			badgeText: { type: 'string', default: '' },
			heading: { type: 'string', default: '' },
			description: { type: 'string', default: '' },
			buttons: { type: 'array', default: [] },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const buttons = Array.isArray( attributes.buttons ) ? attributes.buttons : [];

			const blockProps = useBlockProps( { className: 'cta-block' } );

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
							{ className: 'cta-block__box' },
							el(
								'div',
								{ className: 'cta-block__main' },
								el(
									'span',
									{ className: '_eyebrow -on-dark cta-block__badge' },
									el(
										'span',
										{ className: '_eyebrow__icon _text -primary', contentEditable: false, 'aria-hidden': true },
										BADGE_ICON
									),
									el( RichText, {
										tagName: 'span',
										className: '_eyebrow__label',
										value: attributes.badgeText || '',
										allowedFormats: [],
										placeholder: __( 'Get covered today', 'chw' ),
										onChange: function ( value ) {
											setAttributes( { badgeText: value } );
										},
									} )
								),
								el( RichText, {
									tagName: 'h2',
									className: 'cta-block__heading _text-style -h2',
									value: attributes.heading || '',
									allowedFormats: [ 'core/bold', 'core/text-color' ],
									placeholder: __( 'Ready to protect your home?', 'chw' ),
									onChange: function ( value ) {
										setAttributes( { heading: value } );
									},
								} ),
								el( RichText, {
									tagName: 'p',
									className: 'cta-block__intro -xl',
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
											{ className: 'cta-block__buttons _flex -align-center -justify-center', contentEditable: false },
											buttonPreview
									  )
									: null
							),
							blockData.legalText
								? el( 'div', {
										className: 'cta-block__legal _text -xs',
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
