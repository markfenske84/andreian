( function ( wp ) {
	const { addFilter } = wp.hooks;
	const { createHigherOrderComponent } = wp.compose;
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, ToggleControl } = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { __ } = wp.i18n;

	const BLOCK_NAME = 'core/spacer';
	const ATTR = 'chwHideMobile';
	const CLASS_NAME = 'is-chw-hide-mobile';

	function applyHideMobileClass( props, attributes ) {
		if ( ! attributes[ ATTR ] ) {
			return props;
		}

		props.className = ( ( props.className || '' ) + ' ' + CLASS_NAME ).trim();
		return props;
	}

	addFilter(
		'blocks.registerBlockType',
		'chw/spacer-hide-mobile-attribute',
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

					return applyHideMobileClass( props, attributes );
				},
			};
		}
	);

	const withHideMobileControl = createHigherOrderComponent( function ( BlockEdit ) {
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
						{ title: __( 'Mobile layout', 'chw' ), initialOpen: false },
						el( ToggleControl, {
							label: __( 'Hide on mobile', 'chw' ),
							help: __( 'Hide this spacer when viewed on mobile devices.', 'chw' ),
							checked: isEnabled,
							onChange: function ( value ) {
								setAttributes( { [ ATTR ]: value } );
							},
						} )
					)
				)
			);
		};
	}, 'withHideMobileControl' );

	addFilter( 'editor.BlockEdit', 'chw/spacer-hide-mobile-control', withHideMobileControl );

	addFilter(
		'blocks.getSaveContent.extraProps',
		'chw/spacer-hide-mobile-save-props',
		function ( props, blockType, attributes ) {
			if ( blockType.name !== BLOCK_NAME ) {
				return props;
			}

			return applyHideMobileClass( props, attributes );
		}
	);
} )( window.wp );
