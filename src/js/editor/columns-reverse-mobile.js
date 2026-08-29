( function ( wp ) {
	const { addFilter } = wp.hooks;
	const { createHigherOrderComponent } = wp.compose;
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, ToggleControl } = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { __ } = wp.i18n;

	const BLOCK_NAME = 'core/columns';
	const ATTR = 'andreianReverseMobile';
	const CLASS_NAME = 'is-andreian-reverse-mobile';

	function applyReverseMobileClass( props, attributes ) {
		if ( ! attributes[ ATTR ] ) {
			return props;
		}

		props.className = ( ( props.className || '' ) + ' ' + CLASS_NAME ).trim();
		return props;
	}

	addFilter(
		'blocks.registerBlockType',
		'andreian/columns-reverse-mobile-attribute',
		function ( settings, name ) {
			if ( name !== BLOCK_NAME ) {
				return settings;
			}

			const existingGetEditWrapperProps = settings.getEditWrapperProps;

			return {
				...settings,
				attributes: {
					...settings.attributes,
					[ ATTR ]: {
						type: 'boolean',
						default: false,
					},
				},
				getEditWrapperProps: function ( attributes ) {
					const props = existingGetEditWrapperProps
						? existingGetEditWrapperProps( attributes )
						: {};

					return applyReverseMobileClass( props, attributes );
				},
			};
		}
	);

	const withReverseMobileControl = createHigherOrderComponent( function ( BlockEdit ) {
		return function ( props ) {
			if ( props.name !== BLOCK_NAME ) {
				return el( BlockEdit, props );
			}

			const { attributes, setAttributes } = props;
			const isEnabled = !! attributes[ ATTR ];

			return el(
				Fragment,
				null,
				el( BlockEdit, props ),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Mobile layout', 'andreian' ), initialOpen: false },
						el( ToggleControl, {
							label: __( 'Reverse mobile direction', 'andreian' ),
							help: __(
								'When columns stack on mobile, show them in reverse order (bottom column first).',
								'andreian'
							),
							checked: isEnabled,
							onChange: function ( value ) {
								setAttributes( { [ ATTR ]: value } );
							},
						} )
					)
				)
			);
		};
	}, 'withReverseMobileControl' );

	addFilter(
		'editor.BlockEdit',
		'andreian/columns-reverse-mobile-control',
		withReverseMobileControl
	);

	addFilter(
		'blocks.getSaveContent.extraProps',
		'andreian/columns-reverse-mobile-save-props',
		function ( props, blockType, attributes ) {
			if ( blockType.name !== BLOCK_NAME ) {
				return props;
			}

			return applyReverseMobileClass( props, attributes );
		}
	);
} )( window.wp );
