( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const {
		RichText,
		InspectorControls,
		BlockControls,
		useBlockProps,
		useSetting,
		ColorPalette,
	} = wp.blockEditor;
	const AlignmentControl =
		wp.blockEditor.AlignmentControl || wp.blockEditor.AlignmentToolbar;
	const {
		PanelBody,
		SelectControl,
		TextareaControl,
		BaseControl,
	} = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { __ } = wp.i18n;

	const STYLE_OPTIONS = [
		{ label: __( 'On light', 'chw' ), value: 'on-light' },
		{ label: __( 'On dark', 'chw' ), value: 'on-dark' },
		{ label: __( 'Brand orange', 'chw' ), value: 'brand-orange' },
		{ label: __( 'Plain', 'chw' ), value: 'plain' },
		{ label: __( 'Custom', 'chw' ), value: 'custom' },
	];

	const STYLES_WITH_BUNDLED_TEXT_COLOR = [ 'on-dark', 'brand-orange' ];

	// Slugs that map to `._text -{slug}` utility classes on the front end.
	const TEXT_COLOR_TOKENS = [
		'primary',
		'secondary',
		'tertiary',
		'quaternary',
		'text',
		'muted',
		'white',
	];

	const ALLOWED_STYLES = STYLE_OPTIONS.map( function ( option ) {
		return option.value;
	} );

	const ALIGN_VALUES = [ 'left', 'center', 'right' ];

	function tokenToColor( token, colors ) {
		const match = colors.filter( function ( option ) {
			return option.slug === token;
		} )[ 0 ];
		return match ? match.color : undefined;
	}

	function colorToToken( color, colors ) {
		const match = colors.filter( function ( option ) {
			return option.color === color;
		} )[ 0 ];
		return match ? match.slug : '';
	}

	function textColorSwatches( colors ) {
		return colors.filter( function ( option ) {
			return TEXT_COLOR_TOKENS.indexOf( option.slug ) !== -1;
		} );
	}

	function safeStyle( value ) {
		return ALLOWED_STYLES.indexOf( value ) !== -1 ? value : 'on-light';
	}

	function eyebrowClasses( attributes ) {
		const style = safeStyle( attributes.style );
		const classes = [ '_eyebrow' ];

		if ( style !== 'custom' ) {
			classes.push( '-' + style );
		}

		if ( TEXT_COLOR_TOKENS.indexOf( attributes.textColor ) !== -1
			&& STYLES_WITH_BUNDLED_TEXT_COLOR.indexOf( style ) === -1 ) {
			classes.push( '_text', '-' + attributes.textColor );
		}

		return classes.join( ' ' );
	}

	function iconClasses( attributes ) {
		const classes = [ '_eyebrow__icon' ];

		if ( TEXT_COLOR_TOKENS.indexOf( attributes.iconColor ) !== -1 ) {
			classes.push( '_text', '-' + attributes.iconColor );
		}

		return classes.join( ' ' );
	}

	function eyebrowInlineStyles( attributes ) {
		const style = safeStyle( attributes.style );

		if ( style !== 'custom' ) {
			return undefined;
		}

		const styles = {};
		const backgroundColor = ( attributes.backgroundColor || '' ).trim();
		const borderColor = ( attributes.borderColor || '' ).trim();

		if ( backgroundColor ) {
			styles.backgroundColor = backgroundColor;
		}

		if ( borderColor ) {
			styles.boxShadow = 'inset 0 0 0 1px ' + borderColor;
		}

		return Object.keys( styles ).length ? styles : undefined;
	}

	function wrapperAlignClass( attributes ) {
		const align = attributes.align;
		if ( align === 'center' ) {
			return 'badge-block -align-center';
		}
		if ( align === 'right' ) {
			return 'badge-block -align-right';
		}
		return 'badge-block';
	}

	registerBlockType( 'chw/badge', {
		title: __( 'Badge', 'chw' ),
		description: __( 'A reusable eyebrow badge with optional icon, text, and color styling.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'tag',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			text: { type: 'string', default: '' },
			icon: { type: 'string', default: '' },
			align: { type: 'string', default: '' },
			style: { type: 'string', default: 'on-light' },
			borderColor: { type: 'string', default: '' },
			backgroundColor: { type: 'string', default: '' },
			textColor: { type: 'string', default: 'tertiary' },
			iconColor: { type: 'string', default: 'primary' },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const isCustom = safeStyle( attributes.style ) === 'custom';
			const paletteColors = useSetting( 'color.palette' ) || [];
			const textColors = textColorSwatches( paletteColors );
			const blockProps = useBlockProps( { className: wrapperAlignClass( attributes ) } );

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
						{ title: __( 'Style', 'chw' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Style preset', 'chw' ),
							value: attributes.style || 'on-light',
							options: STYLE_OPTIONS,
							help: __( 'Choose a preset, or "Custom" to set your own background and border colors.', 'chw' ),
							onChange: function ( value ) {
								setAttributes( { style: value } );
							},
						} ),
						isCustom
							? el(
									BaseControl,
									{ label: __( 'Background color', 'chw' ) },
									el( ColorPalette, {
										colors: paletteColors,
										value: attributes.backgroundColor || undefined,
										clearable: true,
										onChange: function ( color ) {
											setAttributes( { backgroundColor: color || '' } );
										},
									} )
							  )
							: null,
						isCustom
							? el(
									BaseControl,
									{ label: __( 'Border color', 'chw' ) },
									el( ColorPalette, {
										colors: paletteColors,
										value: attributes.borderColor || undefined,
										clearable: true,
										onChange: function ( color ) {
											setAttributes( { borderColor: color || '' } );
										},
									} )
							  )
							: null
					),
					el(
						PanelBody,
						{ title: __( 'Text & Icon Colors', 'chw' ), initialOpen: true },
						el(
							BaseControl,
							{ label: __( 'Text color', 'chw' ) },
							el( ColorPalette, {
								colors: textColors,
								value: tokenToColor( attributes.textColor, paletteColors ),
								disableCustomColors: true,
								clearable: true,
								onChange: function ( color ) {
									setAttributes( { textColor: colorToToken( color, paletteColors ) } );
								},
							} )
						),
						el(
							BaseControl,
							{ label: __( 'Icon color', 'chw' ) },
							el( ColorPalette, {
								colors: textColors,
								value: tokenToColor( attributes.iconColor, paletteColors ),
								disableCustomColors: true,
								clearable: true,
								onChange: function ( color ) {
									setAttributes( { iconColor: colorToToken( color, paletteColors ) } );
								},
							} )
						)
					),
					el(
						PanelBody,
						{ title: __( 'Icon', 'chw' ), initialOpen: false },
						el( TextareaControl, {
							label: __( 'Icon (paste SVG)', 'chw' ),
							help: __( 'Paste raw SVG markup. Leave blank for no icon.', 'chw' ),
							value: attributes.icon || '',
							rows: 4,
							onChange: function ( value ) {
								setAttributes( { icon: value } );
							},
						} )
					)
				),
				el(
					'div',
					blockProps,
					el(
						'span',
						{
							className: eyebrowClasses( attributes ),
							style: eyebrowInlineStyles( attributes ),
						},
						attributes.icon
							? el( 'span', {
									className: iconClasses( attributes ),
									contentEditable: false,
									dangerouslySetInnerHTML: { __html: attributes.icon },
							  } )
							: null,
						el( RichText, {
							tagName: 'span',
							className: '_eyebrow__label',
							value: attributes.text || '',
							allowedFormats: [],
							placeholder: __( 'Badge label', 'chw' ),
							onChange: function ( value ) {
								setAttributes( { text: value } );
							},
						} )
					)
				)
			);
		},

		save: function () {
			return null;
		},
	} );
} )( window.wp );
