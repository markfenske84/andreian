( function ( wp ) {
	const { registerBlockType } = wp.blocks;
	const { RichText, InspectorControls, useBlockProps } = wp.blockEditor;
	const {
		PanelBody,
		ToggleControl,
		TextControl,
		SelectControl,
	} = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { __ } = wp.i18n;

	const BUTTON_MODIFIER_OPTIONS = [
		{ label: __( 'None', 'chw' ), value: '' },
		{ label: __( 'Arrow', 'chw' ), value: 'arrow' },
		{ label: __( 'Phone', 'chw' ), value: 'phone' },
		{ label: __( 'Download', 'chw' ), value: 'download' },
	];

	function buttonClass( modifier ) {
		let cls = '_button -primary';
		if ( modifier ) {
			cls += ' -' + modifier;
		}
		return cls;
	}

	registerBlockType( 'chw/cta-subtle', {
		title: __( 'Call To Action: Subtle', 'chw' ),
		description: __( 'A contained call-to-action card with headline, supporting text, and a single primary button.', 'chw' ),
		category: 'choice-home-warranty',
		icon: 'megaphone',
		supports: {
			anchor: true,
			html: false,
		},
		attributes: {
			heading: { type: 'string', default: '' },
			description: { type: 'string', default: '' },
			buttonText: { type: 'string', default: '' },
			buttonUrl: { type: 'string', default: '' },
			buttonModifier: { type: 'string', default: '' },
			buttonNewTab: { type: 'boolean', default: false },
		},

		edit: function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;

			const blockProps = useBlockProps( { className: 'cta-subtle' } );

			const buttonPreview = attributes.buttonText
				? el(
						'span',
						{
							className: buttonClass( attributes.buttonModifier || '' ),
							contentEditable: false,
						},
						attributes.buttonText
				  )
				: null;

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Button', 'chw' ), initialOpen: true },
						el( TextControl, {
							label: __( 'Text', 'chw' ),
							value: attributes.buttonText || '',
							onChange: function ( value ) {
								setAttributes( { buttonText: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'URL', 'chw' ),
							value: attributes.buttonUrl || '',
							onChange: function ( value ) {
								setAttributes( { buttonUrl: value } );
							},
						} ),
						el( SelectControl, {
							label: __( 'Icon', 'chw' ),
							value: attributes.buttonModifier || '',
							options: BUTTON_MODIFIER_OPTIONS,
							onChange: function ( value ) {
								setAttributes( { buttonModifier: value } );
							},
						} ),
						el( ToggleControl, {
							label: __( 'Open in new tab', 'chw' ),
							checked: !! attributes.buttonNewTab,
							onChange: function ( value ) {
								setAttributes( { buttonNewTab: value } );
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
							{ className: 'cta-subtle__card' },
							el(
								'div',
								{ className: 'cta-subtle__main' },
								el( RichText, {
									tagName: 'h2',
									className: 'cta-subtle__heading _text-style -h2',
									value: attributes.heading || '',
									allowedFormats: [ 'core/bold', 'core/text-color' ],
									placeholder: __( 'Ready to connect with your territory rep?', 'chw' ),
									onChange: function ( value ) {
										setAttributes( { heading: value } );
									},
								} ),
								el( RichText, {
									tagName: 'p',
									className: 'cta-subtle__description',
									value: attributes.description || '',
									allowedFormats: [ 'core/bold', 'core/italic' ],
									placeholder: __( 'Connect with a Choice Home Warranty representative near you.', 'chw' ),
									onChange: function ( value ) {
										setAttributes( { description: value } );
									},
								} )
							),
							buttonPreview
								? el(
										'div',
										{ className: 'cta-subtle__button', contentEditable: false },
										buttonPreview
								  )
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
