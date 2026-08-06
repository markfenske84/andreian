( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, SelectControl } = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { __ } = wp.i18n;

	const data = window.chwLegalData || {};
	const legalText = data.legalText || '';

	const TEXT_STYLE_OPTIONS = [
		{ label: __( 'Dark', 'chw' ), value: 'dark' },
		{ label: __( 'Light', 'chw' ), value: 'light' },
	];

	const ALLOWED_TEXT_STYLES = TEXT_STYLE_OPTIONS.map( function ( option ) {
		return option.value;
	} );

	function safeTextStyle( value ) {
		return ALLOWED_TEXT_STYLES.indexOf( value ) !== -1 ? value : 'dark';
	}

	function wrapperClassName( attributes ) {
		return 'chw-legal -text-' + safeTextStyle( attributes.textStyle );
	}

	registerBlockType( 'chw/chw-legal', {
		title: __( 'CHW Legal', 'chw' ),
		description: __( 'Displays the global legal disclaimer from the Customizer.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'media-text',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			textStyle: { type: 'string', default: 'dark' },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const blockProps = useBlockProps( { className: wrapperClassName( attributes ) } );

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Style', 'chw' ), initialOpen: true },
						el( SelectControl, {
							label: __( 'Text color', 'chw' ),
							value: attributes.textStyle || 'dark',
							options: TEXT_STYLE_OPTIONS,
							help: __( 'Choose dark text for light backgrounds, or light text for dark backgrounds.', 'chw' ),
							onChange: function ( value ) {
								setAttributes( { textStyle: value } );
							},
						} )
					)
				),
				el(
					'div',
					blockProps,
					legalText
						? el( 'div', {
								className: 'chw-legal__content',
								dangerouslySetInnerHTML: { __html: legalText },
						  } )
						: el(
								'p',
								{ style: { margin: 0, opacity: 0.7 } },
								__( 'No legal text yet — add it in Customizer → Legal Text.', 'chw' )
						  )
				)
			);
		},

		save: function () {
			return null;
		},
	} );
} )( window.wp );
