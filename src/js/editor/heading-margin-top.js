( function ( wp ) {
	const { addFilter } = wp.hooks;
	const { createHigherOrderComponent } = wp.compose;
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, RangeControl, Button } = wp.components;
	const { createElement: el, Fragment } = wp.element;
	const { __, sprintf } = wp.i18n;

	const settings = window.andreianHeadingMarginTop || {
		defaultMultiplier: 2.5,
		containerGutter: 16,
	};

	const BLOCK_NAME = 'core/heading';
	const ATTR = 'andreianMarginTopMultiplier';
	const DEFAULT = settings.defaultMultiplier;
	const CONTAINER_GUTTER = settings.containerGutter;
	const MIN = 0;
	const MAX = 5;
	const STEP = 0.25;

	function normalizeMultiplier( value ) {
		return Math.round( value * 100 ) / 100;
	}

	function isDefaultMultiplier( value ) {
		if ( value === undefined || value === null ) {
			return true;
		}

		return Math.abs( normalizeMultiplier( value ) - DEFAULT ) < 0.001;
	}

	function formatMultiplier( value ) {
		return String( normalizeMultiplier( value ) ).replace( /\.?0+$/, '' );
	}

	function getMarginTopStyle( multiplier ) {
		if ( isDefaultMultiplier( multiplier ) ) {
			return null;
		}

		if ( Math.abs( normalizeMultiplier( multiplier ) ) < 0.001 ) {
			return '0';
		}

		return 'calc(var(--container-gutter) * ' + formatMultiplier( multiplier ) + ')';
	}

	function applyMarginTopProps( props, attributes ) {
		const marginTop = getMarginTopStyle( attributes[ ATTR ] );

		if ( ! marginTop ) {
			return props;
		}

		return {
			...props,
			style: {
				...( props.style || {} ),
				marginTop: marginTop,
			},
		};
	}

	function getHelpText( multiplier ) {
		if ( isDefaultMultiplier( multiplier ) ) {
			return sprintf(
				/* translators: 1: multiplier, 2: pixel value */
				__(
					'Theme default: %1$s× gutter (%2$spx). Drag to adjust or set to 0 to remove.',
					'andreian'
				),
				formatMultiplier( DEFAULT ),
				Math.round( DEFAULT * CONTAINER_GUTTER )
			);
		}

		if ( Math.abs( normalizeMultiplier( multiplier ) ) < 0.001 ) {
			return __( 'Top margin removed.', 'andreian' );
		}

		return sprintf(
			/* translators: 1: multiplier, 2: pixel value */
			__( 'Custom: %1$s× gutter (%2$spx).', 'andreian' ),
			formatMultiplier( multiplier ),
			Math.round( normalizeMultiplier( multiplier ) * CONTAINER_GUTTER )
		);
	}

	addFilter(
		'blocks.registerBlockType',
		'andreian/heading-margin-top-attribute',
		function ( blockSettings, name ) {
			if ( name !== BLOCK_NAME ) {
				return blockSettings;
			}

			return {
				...blockSettings,
				attributes: {
					...blockSettings.attributes,
					[ ATTR ]: {
						type: 'number',
						default: DEFAULT,
					},
				},
			};
		}
	);

	addFilter(
		'editor.BlockListBlock',
		'andreian/heading-margin-top-editor',
		createHigherOrderComponent( function ( BlockListBlock ) {
			return function ( props ) {
				if ( props.name !== BLOCK_NAME ) {
					return el( BlockListBlock, props );
				}

				const marginTop = getMarginTopStyle( props.attributes[ ATTR ] );

				if ( ! marginTop ) {
					return el( BlockListBlock, props );
				}

				return el( BlockListBlock, {
					...props,
					wrapperProps: {
						...( props.wrapperProps || {} ),
						style: {
							...( props.wrapperProps?.style || {} ),
							marginTop: marginTop,
						},
					},
				} );
			};
		}, 'withHeadingMarginTopEditor' )
	);

	const withHeadingMarginTopControl = createHigherOrderComponent( function ( BlockEdit ) {
		return function ( props ) {
			if ( props.name !== BLOCK_NAME ) {
				return el( BlockEdit, props );
			}

			const { attributes, setAttributes } = props;
			const multiplier = normalizeMultiplier(
				attributes[ ATTR ] === undefined ? DEFAULT : attributes[ ATTR ]
			);
			const isDefault = isDefaultMultiplier( multiplier );

			return el(
				Fragment,
				null,
				el( BlockEdit, props ),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Top margin', 'andreian' ), initialOpen: false },
						el( RangeControl, {
							label: __( 'Top margin', 'andreian' ),
							help: getHelpText( multiplier ),
							value: multiplier,
							onChange: function ( value ) {
								setAttributes( {
									[ ATTR ]: normalizeMultiplier( value ),
								} );
							},
							min: MIN,
							max: MAX,
							step: STEP,
							allowReset: true,
							initialPosition: DEFAULT,
							resetFallbackValue: DEFAULT,
						} ),
						el(
							Button,
							{
								variant: 'secondary',
								disabled: isDefault,
								onClick: function () {
									setAttributes( { [ ATTR ]: DEFAULT } );
								},
							},
							__( 'Reset to theme default', 'andreian' )
						),
						el(
							Button,
							{
								variant: 'secondary',
								disabled: Math.abs( multiplier ) < 0.001,
								onClick: function () {
									setAttributes( { [ ATTR ]: 0 } );
								},
							},
							__( 'Remove top margin', 'andreian' )
						)
					)
				)
			);
		};
	}, 'withHeadingMarginTopControl' );

	addFilter(
		'editor.BlockEdit',
		'andreian/heading-margin-top-control',
		withHeadingMarginTopControl
	);

	addFilter(
		'blocks.getSaveContent.extraProps',
		'andreian/heading-margin-top-save-props',
		function ( props, blockType, attributes ) {
			if ( blockType.name !== BLOCK_NAME ) {
				return props;
			}

			return applyMarginTopProps( props, attributes );
		}
	);
} )( window.wp );
