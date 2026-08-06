( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls, BlockControls, useBlockProps } = wp.blockEditor;
	const AlignmentControl =
		wp.blockEditor.AlignmentControl || wp.blockEditor.AlignmentToolbar;
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

	const BUTTON_STYLE_OPTIONS = [
		{ label: __( 'Primary (Orange)', 'chw' ), value: 'primary' },
		{ label: __( 'Secondary (Blue)', 'chw' ), value: 'secondary' },
		{ label: __( 'Tertiary (Navy)', 'chw' ), value: 'tertiary' },
		{ label: __( 'Quaternary (Sand)', 'chw' ), value: 'quaternary' },
		{ label: __( 'Outline', 'chw' ), value: 'outline' },
		{ label: __( 'Outline Text', 'chw' ), value: 'outline-text' },
		{ label: __( 'Outline Primary', 'chw' ), value: 'outline-primary' },
		{ label: __( 'Outline Secondary', 'chw' ), value: 'outline-secondary' },
		{ label: __( 'Outline Tertiary', 'chw' ), value: 'outline-tertiary' },
		{ label: __( 'Outline White', 'chw' ), value: 'outline-white' },
	];

	const BUTTON_MODIFIER_OPTIONS = [
		{ label: __( 'None', 'chw' ), value: '' },
		{ label: __( 'Arrow', 'chw' ), value: 'arrow' },
		{ label: __( 'Phone', 'chw' ), value: 'phone' },
		{ label: __( 'Download', 'chw' ), value: 'download' },
	];

	const ALLOWED_STYLES = BUTTON_STYLE_OPTIONS.map( function ( option ) {
		return option.value;
	} );

	const ALIGN_VALUES = [ 'left', 'center', 'right' ];

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

	function safeStyle( value ) {
		return ALLOWED_STYLES.indexOf( value ) !== -1 ? value : 'primary';
	}

	function buttonClassFromStyle( style, modifier ) {
		const safe = safeStyle( style );
		const classes = [ '_button' ];

		if ( safe === 'outline' ) {
			classes.push( '-outline' );
		} else if ( safe.indexOf( 'outline-' ) === 0 ) {
			classes.push( '-outline' );
			classes.push( '-' + safe.slice( 'outline-'.length ) );
		} else {
			classes.push( '-' + safe );
		}

		if ( modifier ) {
			classes.push( '-' + modifier );
		}

		return classes.join( ' ' );
	}

	function wrapperClassName( attributes ) {
		const classes = [ 'chw-buttons' ];
		const align = attributes.align;

		if ( align === 'left' ) {
			classes.push( '-align-left' );
		} else if ( align === 'right' ) {
			classes.push( '-align-right' );
		}

		return classes.join( ' ' );
	}

	registerBlockType( 'chw/buttons', {
		title: __( 'CHW Buttons', 'chw' ),
		description: __( 'A row of CTA buttons with selectable styles and optional icons.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'button',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			align: { type: 'string', default: '' },
			buttons: { type: 'array', default: [] },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const buttons = Array.isArray( attributes.buttons ) ? attributes.buttons : [];
			const blockProps = useBlockProps( { className: wrapperClassName( attributes ) } );

			function setButtons( rows ) {
				setAttributes( { buttons: rows } );
			}

			const buttonRows = buttons.map( function ( button, index ) {
				return el(
					'div',
					{ key: button.id || 'btn-' + index, className: 'chw-masthead-repeater-row' },
					el(
						'div',
						{ className: 'chw-masthead-repeater-row__head' },
						el( 'span', null, __( 'Button', 'chw' ) + ' ' + ( index + 1 ) ),
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
								label: __( 'Remove button', 'chw' ),
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
						label: __( 'Style', 'chw' ),
						value: safeStyle( button.style || 'primary' ),
						options: BUTTON_STYLE_OPTIONS,
						onChange: function ( value ) {
							setButtons( updateRow( buttons, index, { style: value } ) );
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
							key: button.id || 'btn-preview-' + index,
							className: buttonClassFromStyle( button.style, button.modifier || '' ),
							contentEditable: false,
						},
						button.text
					);
				} );

			return el(
				Fragment,
				null,
				el(
					BlockControls,
					null,
					el( AlignmentControl, {
						value: attributes.align || undefined,
						onChange: function ( value ) {
							setAttributes( {
								align: ALIGN_VALUES.indexOf( value ) !== -1 ? value : '',
							} );
						},
					} )
				),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Buttons', 'chw' ), initialOpen: true },
						el( BaseControl, null, buttonRows ),
						el(
							Button,
							{
								variant: 'secondary',
								className: 'chw-editor-add-button',
								onClick: function () {
									setButtons( buttons.concat( [ {
										id: uid( 'btn' ),
										text: '',
										url: '',
										style: 'primary',
										modifier: '',
										new_tab: false,
									} ] ) );
								},
							},
							__( 'Add button', 'chw' )
						)
					)
				),
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: 'chw-buttons__inner _flex -align-center -justify-center', contentEditable: false },
						buttonPreview.length
							? buttonPreview
							: el(
									'p',
									{ style: { margin: 0, opacity: 0.7 } },
									__( 'Add a button in the sidebar to get started.', 'chw' )
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
